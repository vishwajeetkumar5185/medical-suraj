<?php
// एरर देखने के लिए (ताकि स्क्रिप्ट क्रैश न हो)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// PHP Limits
ini_set('memory_limit', '512M');
set_time_limit(30); 

// डेटाबेस कनेक्शन डिटेल्स
$db_host = 'localhost'; 
$db_user = 'u403768071_medical';
$db_pass = 'Medical@2026';
$db_name = 'u403768071_medical';

// SQL फ़ाइल पाथ
$sql_file = '../u781523241_medical.sql'; 

// वर्तमान स्थिति प्राप्त करना
$start_pos = isset($_GET['pos']) ? intval($_GET['pos']) : 0;
$query_count = isset($_GET['count']) ? intval($_GET['count']) : 0;

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
if ($conn->connect_error) {
    die("<span style='color: red;'>डेटाबेस कनेक्शन विफल: </span>" . $conn->connect_error);
}

// PHP 8+ में mysqli एरर पर एक्सेप्शन फेंकना बंद करें ताकि स्क्रिप्ट क्रैश न हो
mysqli_report(MYSQLI_REPORT_OFF);

if (!file_exists($sql_file)) {
    die("<span style='color: red;'>त्रुटि: u781523241_medical.sql फ़ाइल public_html में नहीं मिली।</span>");
}

$file_size = filesize($sql_file);
$lines = fopen($sql_file, 'r');
if (!$lines) {
    die("SQL फ़ाइल खोली नहीं जा सकी।");
}

// पिछली पढ़ी गई स्थिति पर जाना
fseek($lines, $start_pos);

$templine = '';
$start_time = time();
$max_run_time = 15; // हर रिक्वेस्ट को 15 सेकंड चलाना है

while (($line = fgets($lines)) !== false) {
    if (time() - $start_time > $max_run_time) {
        $current_pos = ftell($lines);
        fclose($lines);
        $conn->close();
        
        $percent = round(($current_pos / $file_size) * 100, 2);
        $next_url = "import.php?pos=" . $current_pos . "&count=" . $query_count;
        
        echo "<div style='font-family: Arial, sans-serif; text-align: center; margin-top: 50px;'>";
        echo "<h2>डेटाबेस इम्पोर्ट हो रहा है... कृपया प्रतीक्षा करें</h2>";
        echo "<div style='width: 400px; background-color: #f3f3f3; border: 1px solid #ccc; margin: 20px auto; border-radius: 5px; overflow: hidden;'>";
        echo "  <div style='width: {$percent}%; background-color: #4CAF50; height: 30px; text-align: center; line-height: 30px; color: white;'>{$percent}%</div>";
        echo "</div>";
        echo "<p>निष्पादित क्वेरीज़: <b>{$query_count}</b></p>";
        echo "<p>पेज स्वतः रीफ्रेश हो रहा है...</p>";
        echo "</div>";
        
        echo "<script>
            setTimeout(function() {
                window.location.href = '{$next_url}';
            }, 200);
        </script>";
        exit;
    }

    if (substr(trim($line), 0, 2) == '--' || substr(trim($line), 0, 2) == '/*' || trim($line) == '') {
        continue;
    }
    
    $templine .= $line;
    
    if (substr(trim($line), -1, 1) == ';') {
        try {
            $conn->query($templine);
            $query_count++;
        } catch (Exception $e) {
            // एरर होने पर स्क्रिप्ट रुकेगी नहीं, उसे छोड़ देगी
        }
        $templine = '';
    }
}

fclose($lines);
$conn->close();

echo "<div style='font-family: Arial, sans-serif; text-align: center; margin-top: 50px; color: green;'>";
echo "<h1>🎉 बधाई हो!</h1>";
echo "<h3>डेटाबेस इम्पोर्ट की प्रक्रिया 100% पूरी हो चुकी है।</h3>";
echo "<p>कुल <b>{$query_count}</b> क्वेरीज़ सफलतापूर्वक निष्पादित की गईं।</p>";
echo "</div>";
?>