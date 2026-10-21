<?php
// Koneksi ke Elasticsearch untuk menu Monitor Logging (index diisi oleh Logstash dari logs/app.log)
return [
    'host'       => rtrim(getenv('ELASTICSEARCH_HOST') ?: 'https://localhost:9200', '/'),
    'username'   => getenv('ELASTICSEARCH_USER') ?: 'elastic',
    'password'   => getenv('ELASTICSEARCH_PASSWORD') ?: '',
    'index'      => getenv('ELASTICSEARCH_INDEX') ?: 'app-logs-*',
    // Sertifikat ES lokal biasanya self-signed
    'verify_ssl' => getenv('ELASTICSEARCH_SSL_VERIFY') === 'true',
    'timeout'    => 10,
    'kibana_url' => rtrim(getenv('KIBANA_URL') ?: 'http://localhost:5601', '/'),
];
