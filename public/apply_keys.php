<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// डेटाबेस कनेक्शन डिटेल्स
$db_host = 'localhost'; 
$db_user = 'u403768071_medical';
$db_pass = 'Medical@2026';
$db_name = 'u403768071_medical';
$sql_file = '../u781523241_medical.sql'; 

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// PHP 8+ में mysqli एरर पर एक्सेप्शन फेंकना बंद करें
mysqli_report(MYSQLI_REPORT_OFF);

// फॉरेन की चेक को अस्थायी रूप से बंद करें
$conn->query("SET FOREIGN_KEY_CHECKS = 0;");

if (!file_exists($sql_file)) {
    die("Error: SQL file not found.");
}

$file_size = filesize($sql_file);
$handle = fopen($sql_file, 'r');
if (!$handle) {
    die("Cannot open SQL file.");
}

echo "SQL फ़ाइल के अंत में से Indexes और Auto-Increment खोजे जा रहे हैं...<br>";

// फ़ाइल के अंतिम 300KB हिस्से पर जाना
$seek_pos = max(0, $file_size - 300000);
fseek($handle, $seek_pos);

// Index सेक्शन की शुरुआत ढूंढना
$found = false;
while (($line = fgets($handle)) !== false) {
    if (strpos($line, 'Indexes for dumped tables') !== false) {
        $found = true;
        break;
    }
}

if (!$found) {
    die("<span style='color: red;'>त्रुटि: SQL फ़ाइल के अंत में Indexes सेक्शन नहीं मिला।</span>");
}

echo "<span style='color: green;'>सफलता: Indexes सेक्शन मिल गया! कमांड्स रन हो रहे हैं...</span><br><br>";

$templine = '';
$executed_queries = 0;
$failed_queries = 0;

// बचे हुए सभी ALTER TABLE कमांड्स को रन करना
while (($line = fgets($handle)) !== false) {
    if (substr(trim($line), 0, 2) == '--' || substr(trim($line), 0, 2) == '/*' || trim($line) == '') {
        continue;
    }
    
    $templine .= $line;
    
    if (substr(trim($line), -1, 1) == ';') {
        try {
            if ($conn->query($templine)) {
                $executed_queries++;
            } else {
                // अगर एरर "Multiple primary key" या "already exists" का है, तो उसे इग्नोर करेंगे
                echo "<span style='color: orange;'>स्किप किया गया (या एरर):</span> " . htmlspecialchars(substr($templine, 0, 80)) . "... -> <b>" . $conn->error . "</b><br>";
                $failed_queries++;
            }
        } catch (Exception $e) {
            // एक्सेप्शन को स्किप करें
            $failed_queries++;
        }
        $templine = '';
    }
}

fclose($handle);

// फॉरेन की चेक को वापस चालू करना
$conn->query("SET FOREIGN_KEY_CHECKS = 1;");
$conn->close();

echo "<h3>प्रक्रिया पूर्ण!</h3>";
echo "सफलतापूर्वक निष्पादित (Executed): <b>" . $executed_queries . "</b> कमांड्स.<br>";
echo "स्किप / पहले से मौजूद (Skipped/Failed): <b>" . $failed_queries . "</b> कमांड्स.<br>";
?>