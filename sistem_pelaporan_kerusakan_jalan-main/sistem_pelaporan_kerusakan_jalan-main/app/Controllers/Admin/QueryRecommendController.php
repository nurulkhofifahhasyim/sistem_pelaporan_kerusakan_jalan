<?php
namespace App\Controllers\Admin;

use App\Core\Controller;

class QueryRecommendController extends Controller
{
    private string $logFile;
    private string $timeRange = 'now-30d';

    public function __construct()
    {
        parent::__construct();
        $this->logFile = BASE_PATH . '/logs/app.log';
    }

    public function index(): void
    {
        $this->requireAuth();
        $patterns  = $this->analyzeLog();
        $this->view('admin/query-recommend/index', [
            'patterns'  => $patterns,
            'timeRange' => $this->timeRange,
        ], 'admin');
    }

    private function analyzeLog(): array
    {
        if (!file_exists($this->logFile)) {
            return [];
        }

        $lines    = file($this->logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $errors   = [];
        $warnings = [];
        $msgFreq  = [];
        $oldestTimestamp = null;

        foreach (array_reverse($lines) as $line) {
            $entry = json_decode($line, true);
            if (!$entry) continue;

            $level = $entry['level'] ?? 'INFO';
            $msg   = $entry['message'] ?? '';
            $ts    = $entry['timestamp'] ?? null;

            if ($ts && ($oldestTimestamp === null || $ts < $oldestTimestamp)) {
                $oldestTimestamp = $ts;
            }

            if ($level === 'ERROR')   $errors[]   = $entry;
            if ($level === 'WARNING') $warnings[]  = $entry;

            if ($level === 'ERROR' || $level === 'WARNING') {
                $msgFreq[$msg] = ($msgFreq[$msg] ?? 0) + 1;
            }
        }

        $this->timeRange = $this->calculateTimeRange($oldestTimestamp);

        $patterns = [];

        $dbErrors = array_filter($errors, fn($e) => str_contains(strtolower($e['message']), 'database')
            || isset($e['context']['exception']) && str_contains($e['context']['exception'], 'PDO'));
        if (count($dbErrors) > 0) {
            $patterns[] = $this->buildPattern(
                'Database Connection Failure',
                count($dbErrors),
                'ERROR',
                'Terdeteksi ' . count($dbErrors) . ' error koneksi database.',
                $this->queryDatabaseError()
            );
        }

        $uploadErrors = array_filter($errors, fn($e) => str_contains(strtolower($e['message']), 'upload')
            || str_contains(strtolower($e['message']), 'foto'));
        if (count($uploadErrors) > 0) {
            $patterns[] = $this->buildPattern(
                'File Upload Error',
                count($uploadErrors),
                'ERROR',
                'Terdeteksi ' . count($uploadErrors) . ' error upload file.',
                $this->queryUploadError()
            );
        }

        $loginWarn = array_filter($warnings, fn($e) => str_contains(strtolower($e['message']), 'login'));
        if (count($loginWarn) > 0) {
            $patterns[] = $this->buildPattern(
                'Authentication Failure',
                count($loginWarn),
                'WARNING',
                'Terdeteksi ' . count($loginWarn) . ' percobaan login gagal — kemungkinan brute force.',
                $this->queryLoginFailure()
            );
        }

        arsort($msgFreq);
        $topMsg = array_slice($msgFreq, 0, 3, true);
        foreach ($topMsg as $msg => $freq) {
            if ($freq >= 3) {
                $patterns[] = $this->buildPattern(
                    'Repeated Error: "' . substr($msg, 0, 50) . '"',
                    $freq,
                    'ERROR',
                    'Pesan ini muncul ' . $freq . ' kali. Perlu investigasi lebih lanjut.',
                    $this->queryRepeatedMessage($msg)
                );
                break;
            }
        }

        $todayErrors = array_filter($errors, function($e) {
            return str_starts_with($e['timestamp'] ?? '', date('Y-m-d'));
        });
        if (!empty($errors)) {
            $patterns[] = $this->buildPattern(
                'Semua ERROR Hari Ini',
                count($todayErrors),
                'ERROR',
                'Total ' . count($errors) . ' error dalam log, ' . count($todayErrors) . ' terjadi hari ini.',
                $this->queryAllErrorsToday()
            );
        }

        return $patterns;
    }

    private function buildPattern(string $title, int $count, string $severity, string $desc, string $query): array
    {
        return [
            'title'    => $title,
            'count'    => $count,
            'severity' => $severity,
            'desc'     => $desc,
            'query'    => $query,
        ];
    }

    private function calculateTimeRange(?string $oldestTimestamp): string
    {
        if (!$oldestTimestamp) {
            return 'now-30d';
        }

        $oldest = strtotime($oldestTimestamp);
        if ($oldest === false) {
            return 'now-30d';
        }

        $diffSeconds = time() - $oldest;
        $diffDays    = (int) ceil($diffSeconds / 86400);

        $diffDays = max($diffDays + 1, 1);

        if ($diffDays <= 1) {
            return 'now-24h';
        } elseif ($diffDays <= 7) {
            return 'now-7d';
        } elseif ($diffDays <= 30) {
            return 'now-30d';
        } elseif ($diffDays <= 90) {
            return 'now-90d';
        } else {
            return 'now-365d';
        }
    }

    private function queryDatabaseError(): string
    {
        return json_encode([
            'query' => [
                'bool' => [
                    'must'   => [['match' => ['level' => 'ERROR']]],
                    'should' => [
                        ['match_phrase' => ['message' => 'database']],
                        ['match_phrase' => ['context.exception' => 'PDOException']],
                    ],
                    'minimum_should_match' => 1,
                    'filter' => [['range' => ['@timestamp' => ['gte' => $this->timeRange]]]],
                ],
            ],
            'sort' => [['@timestamp' => ['order' => 'desc']]],
            'size' => 50,
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }

    private function queryUploadError(): string
    {
        return json_encode([
            'query' => [
                'bool' => [
                    'must' => [
                        ['match' => ['level' => 'ERROR']],
                        ['bool' => ['should' => [
                            ['match_phrase' => ['message' => 'upload']],
                            ['match_phrase' => ['message' => 'foto']],
                        ]]],
                    ],
                    'filter' => [['range' => ['@timestamp' => ['gte' => $this->timeRange]]]],
                ],
            ],
            'sort' => [['@timestamp' => ['order' => 'desc']]],
            'size' => 20,
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }

    private function queryLoginFailure(): string
    {
        return json_encode([
            'query' => [
                'bool' => [
                    'must'   => [['match' => ['level' => 'WARNING']]],
                    'filter' => [
                        ['match_phrase' => ['message' => 'login']],
                        ['range' => ['@timestamp' => ['gte' => $this->timeRange]]],
                    ],
                ],
            ],
            'aggs' => [
                'by_ip' => ['terms' => ['field' => 'http.ip_address.keyword', 'size' => 10]],
            ],
            'sort' => [['@timestamp' => ['order' => 'desc']]],
            'size' => 50,
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }

    private function queryRepeatedMessage(string $msg): string
    {
        return json_encode([
            'query' => [
                'bool' => [
                    'must'   => [['match_phrase' => ['message' => $msg]]],
                    'filter' => [['range' => ['@timestamp' => ['gte' => $this->timeRange]]]],
                ],
            ],
            'aggs' => [
                'by_level' => ['terms' => ['field' => 'level.keyword']],
            ],
            'sort' => [['@timestamp' => ['order' => 'desc']]],
            'size' => 50,
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }

    private function queryAllErrorsToday(): string
    {
        return json_encode([
            'query' => [
                'bool' => [
                    'must'   => [['match' => ['level' => 'ERROR']]],
                    'filter' => [['range' => ['@timestamp' => ['gte' => $this->timeRange]]]],
                ],
            ],
            'aggs' => [
                'by_message' => ['terms' => ['field' => 'message.keyword', 'size' => 10]],
                'error_over_time' => [
                    'date_histogram' => ['field' => '@timestamp', 'calendar_interval' => '1h'],
                ],
            ],
            'sort' => [['@timestamp' => ['order' => 'desc']]],
            'size' => 100,
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }
}
