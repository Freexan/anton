<?php
// Simple cross-platform smoke test for CRUD API (Part A)
// Usage: php tools/smoke.php [base_url]
// Example: php tools/smoke.php http://localhost:8080

$base = $argv[1] ?? 'http://localhost:8080';
$api = rtrim($base, '/') . '/entities';

function req(string $method, string $url, $body = null, array $headers = []): array {
    $opts = [
        'http' => [
            'method' => $method,
            'ignore_errors' => true,
            'header' => $headers,
            'timeout' => 10,
        ],
    ];
    if ($body !== null) {
        if (is_array($body)) {
            $payload = json_encode($body);
            $opts['http']['content'] = $payload;
            $opts['http']['header'][] = 'Content-Type: application/json';
        } else {
            $opts['http']['content'] = (string)$body;
        }
    }
    $ctx = stream_context_create($opts);
    $resp = @file_get_contents($url, false, $ctx);
    $status = 0;
    $respHeaders = $http_response_header ?? [];
    foreach ($respHeaders as $h) {
        if (preg_match('#^HTTP/\S+\s+(\d{3})#i', $h, $m)) { $status = (int)$m[1]; }
    }
    $json = null;
    if ($resp !== false && strlen($resp) > 0) {
        $json = json_decode($resp, true);
    }
    return [ 'status' => $status, 'json' => $json, 'raw' => $resp, 'headers' => $respHeaders ];
}

function assertStatus($got, $expected, $label) {
    if ($got !== $expected) {
        fwrite(STDERR, "FAIL [$label]: expected $expected, got $got\n");
        exit(1);
    } else {
        echo "OK   [$label]: $got\n";
    }
}

function randSuffix($len=6){ return substr(bin2hex(random_bytes($len)), 0, $len); }

echo "Base: $base\n";

// 1) List (200)
$r = req('GET', $api);
assertStatus($r['status'], 200, 'GET /entities');

// 2) Create (201)
$suf = randSuffix(3);
$name = 'Smoke '.$suf;
$create = [ 'name' => $name, 'quantity' => 3, 'note' => 'test' ];
$r = req('POST', $api, $create);
assertStatus($r['status'], 201, 'POST /entities');
$id = $r['json']['id'] ?? null;
if (!$id) { fwrite(STDERR, "FAIL: missing id in create response\n"); exit(1); }

// 3) Detail (200)
$r = req('GET', $api.'/'.$id);
assertStatus($r['status'], 200, 'GET /entities/{id}');

// 4) Update (200)
$update = [ 'name' => $name.'-u', 'quantity' => 5, 'note' => 'updated' ];
$r = req('PUT', $api.'/'.$id, $update);
assertStatus($r['status'], 200, 'PUT /entities/{id}');

// 5) Delete (204)
$r = req('DELETE', $api.'/'.$id);
assertStatus($r['status'], 204, 'DELETE /entities/{id}');

// 6) Detail after delete (404)
$r = req('GET', $api.'/'.$id);
assertStatus($r['status'], 404, 'GET /entities/{id} after delete');

echo "\nSmoke test passed.\n";
