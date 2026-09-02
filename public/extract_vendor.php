<?php
header('Content-Type: text/plain');
ini_set('max_execution_time', 300);

echo "=== DAWALO REMOTE ZIP EXTRACTOR ===\n\n";

$zipFile = dirname(__DIR__) . '/vendor.zip';
$extractTo = dirname(__DIR__);

if (!file_exists($zipFile)) {
    echo "❌ ERROR: 'vendor.zip' was not found in the website root directory.\n";
    echo "Please do the following:\n";
    echo "1. Go to your local computer's project folder: 'c:\\xampp\\htdocs\\medical'\n";
    echo "2. Compress (Zip) the 'vendor' folder and name the file 'vendor.zip'.\n";
    echo "3. Upload 'vendor.zip' directly to the root directory (the parent folder of 'public') on your live server.\n";
    echo "4. Refresh this page to extract it instantly!\n";
    exit;
}

if (!class_exists('ZipArchive')) {
    echo "❌ ERROR: PHP 'ZipArchive' class is not loaded on this server. Please contact your hosting provider to enable it.\n";
    exit;
}

echo "✅ 'vendor.zip' found! Starting extraction to root folder...\n";
$zip = new ZipArchive;
$res = $zip->open($zipFile);

if ($res === TRUE) {
    $zip->extractTo($extractTo);
    $zip->close();
    echo "✅ SUCCESS: 'vendor.zip' extracted successfully!\n";
    echo "You can now delete 'vendor.zip' and this extraction script for safety.\n";
} else {
    echo "❌ FAILED: Could not open or extract 'vendor.zip'. Error code: " . $res . "\n";
}
