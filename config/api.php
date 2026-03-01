<?php
// ============================================================
// FILE: config/api.php
// Deskripsi: Konfigurasi URL API dan helper fungsi cURL
// ============================================================

// URL base API
define('API_BASE_URL', 'http://localhost/menucafe-api');

// ===== cURL GET =====
function api_get(string $endpoint): array
{
    $url = API_BASE_URL . $endpoint;
    $ch  = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL            => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 10,
        CURLOPT_HTTPHEADER     => ['Accept: application/json'],
    ]);
    $response = curl_exec($ch);
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $decoded = json_decode($response, true);
    return ['code' => $httpcode, 'body' => $decoded ?? []];
}

// ===== cURL POST JSON =====
function api_post_json(string $endpoint, array $payload): array
{
    $url = API_BASE_URL . $endpoint;
    $ch  = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL            => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 10,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => json_encode($payload),
        CURLOPT_HTTPHEADER     => [
            'Content-Type: application/json',
            'Accept: application/json',
        ],
    ]);
    $response = curl_exec($ch);
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $decoded = json_decode($response, true);
    return ['code' => $httpcode, 'body' => $decoded ?? []];
}

// ===== cURL POST MULTIPART (untuk upload file) =====
function api_post_multipart(string $endpoint, array $fields, ?array $file = null): array
{
    $url      = API_BASE_URL . $endpoint;
    $postdata = $fields;

    if ($file !== null) {
        $postdata['gambar'] = new CURLFile(
            $file['tmp_name'],
            $file['type'],
            $file['name']
        );
    }

    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL            => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 30,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => $postdata,
    ]);
    $response = curl_exec($ch);
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $decoded = json_decode($response, true);
    return ['code' => $httpcode, 'body' => $decoded ?? []];
}

// ===== cURL DELETE =====
function api_delete(string $endpoint, array $payload): array
{
    $url = API_BASE_URL . $endpoint;
    $ch  = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL            => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 10,
        CURLOPT_CUSTOMREQUEST  => 'DELETE',
        CURLOPT_POSTFIELDS     => json_encode($payload),
        CURLOPT_HTTPHEADER     => [
            'Content-Type: application/json',
            'Accept: application/json',
        ],
    ]);
    $response = curl_exec($ch);
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $decoded = json_decode($response, true);
    return ['code' => $httpcode, 'body' => $decoded ?? []];
}

// ===== Helper URL gambar dari API =====
function gambar_url(string $filename): string
{
    if (empty($filename)) return '';
    return API_BASE_URL . '/api/menu/get_image.php?file=' . urlencode($filename);
}
