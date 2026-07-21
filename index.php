<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

$path = $_GET['path'] ?? 'GoldPrices/Latest?readjson=false';
$url = 'https://www.goldtraders.or.th/api/' . $path;

$ch = curl_init($url);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_TIMEOUT => 10,
    CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36',
    CURLOPT_REFERER => 'https://www.goldtraders.or.th/',
    CURLOPT_HTTPHEADER => [
        'Accept: text/plain, */*; q=0.01',
        'Accept-Language: en-US,en;q=0.9,th;q=0.8',
        'X-Requested-With: XMLHttpRequest',
        'Sec-Fetch-Dest: empty',
        'Sec-Fetch-Mode: cors',
        'Sec-Fetch-Site: same-origin',
    ],
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
$effectiveUrl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
curl_close($ch);

error_log("GOLD_PROXY: url=$url httpCode=$httpCode error=$error effectiveUrl=$effectiveUrl");

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
