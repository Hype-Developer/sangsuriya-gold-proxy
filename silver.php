<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

$url = 'http://27.254.77.78/rest/public/rest/silver';

$ch = curl_init($url);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_TIMEOUT => 10,
    CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36',
    CURLOPT_HTTPHEADER => [
        'Accept: application/json, text/plain, */*',
        'Connection: keep-alive',
    ],
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

error_log("SILVER_PROXY: url=$url httpCode=$httpCode error=$error");

if ($error) {
    http_response_code(502);
    echo json_encode(['error' => 'Upstream connection failed: ' . $error, 'url' => $url]);
    exit;
}

if ($httpCode !== 200) {
    http_response_code($httpCode);
    echo json_encode(['error' => 'Upstream returned ' . $httpCode, 'url' => $url, 'body' => substr($response, 0, 500)]);
    exit;
}

http_response_code($httpCode);
echo $response;
