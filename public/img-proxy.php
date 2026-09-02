<?php
// Image proxy to bypass hotlink protection
// URL: /img-proxy?url=https://onemg.gumlet.io/...

$url = $_GET['url'] ?? '';

// Only allow onemg.gumlet.io URLs for safety
if (empty($url) || !preg_match('#^https?://(onemg\.gumlet\.io|cdn\.1mg\.com|images\.1mg\.com|onemg\.s3\.amazonaws\.com)/#', $url)) {
    http_response_code(403);
    exit('Invalid URL');
}

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
curl_setopt($ch, CURLOPT_REFERER, 'https://www.1mg.com/');
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36');
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Accept: image/webp,image/apng,image/*,*/*;q=0.8',
    'Accept-Encoding: gzip, deflate, br',
    'Accept-Language: en-US,en;q=0.9',
]);

$data = curl_exec($ch);
$contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode !== 200 || empty($data)) {
    http_response_code(404);
    exit;
}

// Cache for 24 hours
header('Cache-Control: public, max-age=86400');
header('Content-Type: ' . ($contentType ?: 'image/jpeg'));
echo $data;
