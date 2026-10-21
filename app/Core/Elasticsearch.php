<?php
namespace App\Core;

class Elasticsearch
{
    private array $config;

    public function __construct(?array $config = null)
    {
        $this->config = $config ?? require __DIR__ . '/../Config/elasticsearch.php';
    }

    public function config(string $key): mixed
    {
        return $this->config[$key] ?? null;
    }

    public function search(array $body): array
    {
        $index = rawurlencode($this->config['index']);
        return $this->request('POST', '/' . $index . '/_search?ignore_unavailable=true&allow_no_indices=true', $body);
    }

    private function request(string $method, string $path, ?array $body = null): array
    {
        $ch = curl_init($this->config['host'] . $path);
        curl_setopt_array($ch, [
            CURLOPT_CUSTOMREQUEST  => $method,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 3,
            CURLOPT_TIMEOUT        => $this->config['timeout'],
            CURLOPT_HTTPHEADER     => ['Content-Type: application/json', 'Accept: application/json'],
            CURLOPT_SSL_VERIFYPEER => $this->config['verify_ssl'],
            CURLOPT_SSL_VERIFYHOST => $this->config['verify_ssl'] ? 2 : 0,
        ]);
        if ($this->config['password'] !== '') {
            curl_setopt($ch, CURLOPT_USERPWD, $this->config['username'] . ':' . $this->config['password']);
        }
        if ($body !== null) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
        }

        $raw    = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $err    = curl_error($ch);
        curl_close($ch);

        if ($raw === false) {
            throw new ElasticsearchException('Tidak dapat terhubung ke Elasticsearch di ' . $this->config['host'] . ': ' . $err);
        }

        $data = json_decode($raw, true);
        if ($status === 401) {
            throw new ElasticsearchException('Autentikasi Elasticsearch gagal (401). Periksa ELASTICSEARCH_USER / ELASTICSEARCH_PASSWORD.');
        }
        if ($status >= 400 || !is_array($data)) {
            $reason = $data['error']['root_cause'][0]['reason'] ?? $data['error']['reason'] ?? substr((string) $raw, 0, 300);
            throw new ElasticsearchException("Elasticsearch mengembalikan HTTP $status: $reason");
        }
        return $data;
    }
}
