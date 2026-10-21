<?php
namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Elasticsearch;
use App\Core\ElasticsearchException;

/**
 * Monitor Logging (Superadmin): membaca log aplikasi dari Elasticsearch
 * (logs/app.log → Logstash → index app-logs-*) untuk membantu debugging.
 */
class MonitorLogController extends Controller
{
    private const PER_PAGE = 50;

    // rentang => [label, interval date_histogram]
    private const RANGES = [
        '15m' => ['15 menit terakhir', '30s'],
        '1h'  => ['1 jam terakhir',    '1m'],
        '6h'  => ['6 jam terakhir',    '5m'],
        '24h' => ['24 jam terakhir',   '30m'],
        '7d'  => ['7 hari terakhir',   '3h'],
        '30d' => ['30 hari terakhir',  '12h'],
    ];

    private const LEVELS = ['INFO', 'WARNING', 'ERROR'];

    public function index(): void
    {
        $this->requireSuperadmin();

        $filters = [
            'range'      => isset(self::RANGES[$_GET['range'] ?? '']) ? $_GET['range'] : '24h',
            'level'      => in_array($_GET['level'] ?? '', self::LEVELS, true) ? $_GET['level'] : '',
            'q'          => trim($_GET['q'] ?? ''),
            'endpoint'   => trim($_GET['endpoint'] ?? ''),
            'request_id' => trim($_GET['request_id'] ?? ''),
            'hide_req'   => ($_GET['hide_req'] ?? '1') === '1',
        ];
        $page = max(1, (int) ($_GET['page'] ?? 1));

        $es    = new Elasticsearch();
        $data  = null;
        $error = null;

        try {
            $data = $this->fetch($es, $filters, $page);
        } catch (ElasticsearchException $e) {
            $error = $e->getMessage();
        }

        $this->view('admin/monitor-log/index', [
            'filters'   => $filters,
            'ranges'    => self::RANGES,
            'levels'    => self::LEVELS,
            'page'      => $page,
            'perPage'   => self::PER_PAGE,
            'log'       => $data,
            'error'     => $error,
            'esHost'    => $es->config('host'),
            'esIndex'   => $es->config('index'),
            'kibanaUrl' => $es->config('kibana_url'),
        ], 'admin');
    }

    private function fetch(Elasticsearch $es, array $f, int $page): array
    {
        $since    = 'now-' . $f['range'];
        $interval = self::RANGES[$f['range']][1];
        $tz       = date_default_timezone_get();

        $histogram = [
            'field'           => '@timestamp',
            'fixed_interval'  => $interval,
            'time_zone'       => $tz,
            'min_doc_count'   => 0,
            'extended_bounds' => ['min' => $since, 'max' => 'now'],
        ];
        $isRequest = ['term' => ['message.keyword' => 'HTTP request']];

        // Filter tabel (post_filter) — grafik & statistik tetap menggambarkan seluruh rentang waktu
        $tableFilter = [];
        if ($f['level'] !== '')      $tableFilter[] = ['term' => ['level' => $f['level']]];
        if ($f['endpoint'] !== '')   $tableFilter[] = ['wildcard' => ['http.endpoint' => ['value' => '*' . $f['endpoint'] . '*', 'case_insensitive' => true]]];
        if ($f['request_id'] !== '') $tableFilter[] = ['term' => ['request_id' => $f['request_id']]];
        if ($f['q'] !== '') {
            $tableFilter[] = ['simple_query_string' => [
                'query'            => $f['q'],
                'fields'           => ['message', 'http.endpoint.text', 'context_text'],
                'default_operator' => 'and',
                'lenient'          => true,
            ]];
        }
        $tableMustNot = $f['hide_req'] && $f['request_id'] === '' ? [$isRequest] : [];

        $from = min(($page - 1) * self::PER_PAGE, 10000 - self::PER_PAGE);

        $body = [
            'track_total_hits' => true,
            'size'  => self::PER_PAGE,
            'from'  => $from,
            'sort'  => [['@timestamp' => 'desc']],
            'query' => ['bool' => ['filter' => [['range' => ['@timestamp' => ['gte' => $since]]]]]],
            'post_filter' => ['bool' => ['filter' => $tableFilter, 'must_not' => $tableMustNot]],
            'aggs' => [
                'levels'   => ['terms' => ['field' => 'level', 'size' => 10]],
                'timeline' => [
                    'date_histogram' => $histogram,
                    'aggs' => ['levels' => ['terms' => ['field' => 'level', 'size' => 10]]],
                ],
                'requests' => [
                    'filter' => $isRequest,
                    'aggs'   => [
                        'avg_ms'    => ['avg' => ['field' => 'http.duration_ms']],
                        'p95_ms'    => ['percentiles' => ['field' => 'http.duration_ms', 'percents' => [95]]],
                        'status'    => ['range' => ['field' => 'http.status_code', 'keyed' => true, 'ranges' => [
                            ['key' => 'ok', 'to' => 400], ['key' => '4xx', 'from' => 400, 'to' => 500], ['key' => '5xx', 'from' => 500],
                        ]]],
                        'over_time' => [
                            'date_histogram' => $histogram,
                            'aggs' => [
                                'avg_ms' => ['avg' => ['field' => 'http.duration_ms']],
                                'status' => ['range' => ['field' => 'http.status_code', 'keyed' => true, 'ranges' => [
                                    ['key' => 'ok', 'to' => 400], ['key' => '4xx', 'from' => 400, 'to' => 500], ['key' => '5xx', 'from' => 500],
                                ]]],
                            ],
                        ],
                        'slowest' => ['terms' => [
                            'field' => 'http.endpoint', 'size' => 5, 'order' => ['avg_ms' => 'desc'],
                        ], 'aggs' => ['avg_ms' => ['avg' => ['field' => 'http.duration_ms']]]],
                    ],
                ],
                'top_errors' => [
                    'filter' => ['terms' => ['level' => ['ERROR', 'WARNING']]],
                    'aggs'   => ['messages' => [
                        'terms' => ['field' => 'message.keyword', 'size' => 8],
                        'aggs'  => [
                            'level'     => ['terms' => ['field' => 'level', 'size' => 1]],
                            'last_seen' => ['max' => ['field' => '@timestamp']],
                        ],
                    ]],
                ],
                'error_endpoints' => [
                    'filter' => ['bool' => ['should' => [
                        ['term' => ['level' => 'ERROR']],
                        ['range' => ['http.status_code' => ['gte' => 500]]],
                    ], 'minimum_should_match' => 1]],
                    'aggs' => ['endpoints' => ['terms' => ['field' => 'http.endpoint', 'size' => 5]]],
                ],
            ],
        ];

        $res  = $es->search($body);
        $aggs = $res['aggregations'] ?? [];

        $levelCount = array_fill_keys(self::LEVELS, 0);
        foreach ($aggs['levels']['buckets'] ?? [] as $b) {
            $levelCount[$b['key']] = $b['doc_count'];
        }

        $timeline = ['labels' => [], 'INFO' => [], 'WARNING' => [], 'ERROR' => []];
        foreach ($aggs['timeline']['buckets'] ?? [] as $b) {
            $timeline['labels'][] = $b['key'];
            $per = array_column($b['levels']['buckets'], 'doc_count', 'key');
            foreach (self::LEVELS as $lv) {
                $timeline[$lv][] = $per[$lv] ?? 0;
            }
        }

        $req      = $aggs['requests'] ?? [];
        $requests = ['labels' => [], 'ok' => [], '4xx' => [], '5xx' => [], 'avg_ms' => []];
        foreach ($req['over_time']['buckets'] ?? [] as $b) {
            $requests['labels'][] = $b['key'];
            foreach (['ok', '4xx', '5xx'] as $k) {
                $requests[$k][] = $b['status']['buckets'][$k]['doc_count'] ?? 0;
            }
            $requests['avg_ms'][] = $b['avg_ms']['value'] !== null ? round($b['avg_ms']['value'], 1) : null;
        }

        $totalReq = $req['doc_count'] ?? 0;
        $req5xx   = $req['status']['buckets']['5xx']['doc_count'] ?? 0;

        return [
            'total_events' => $res['hits']['total']['value'] ?? 0,
            'hits'         => array_map(fn($h) => $h['_source'] + ['_id' => $h['_id'], '_index' => $h['_index']], $res['hits']['hits'] ?? []),
            'levels'       => $levelCount,
            'all_events'   => array_sum($levelCount),
            'timeline'     => $timeline,
            'requests'     => $requests,
            'stats'        => [
                'total_req' => $totalReq,
                'req_4xx'   => $req['status']['buckets']['4xx']['doc_count'] ?? 0,
                'req_5xx'   => $req5xx,
                'err_rate'  => $totalReq > 0 ? round($req5xx / $totalReq * 100, 2) : 0,
                'avg_ms'    => isset($req['avg_ms']['value']) ? round($req['avg_ms']['value'], 1) : null,
                'p95_ms'    => isset($req['p95_ms']['values']['95.0']) ? round($req['p95_ms']['values']['95.0'], 1) : null,
            ],
            'top_errors'   => array_map(fn($b) => [
                'message'   => $b['key'],
                'count'     => $b['doc_count'],
                'level'     => $b['level']['buckets'][0]['key'] ?? 'ERROR',
                'last_seen' => $b['last_seen']['value_as_string'] ?? null,
            ], $aggs['top_errors']['messages']['buckets'] ?? []),
            'error_endpoints' => array_map(fn($b) => ['endpoint' => $b['key'], 'count' => $b['doc_count']],
                $aggs['error_endpoints']['endpoints']['buckets'] ?? []),
            'slowest'      => array_map(fn($b) => ['endpoint' => $b['key'], 'avg_ms' => round($b['avg_ms']['value'] ?? 0, 1), 'count' => $b['doc_count']],
                $req['slowest']['buckets'] ?? []),
        ];
    }
}
