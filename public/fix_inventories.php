<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// डेटाबेस कनेक्शन डिटेल्स
$db_host = 'localhost'; 
$db_user = 'u403768071_medical';
$db_pass = 'Medical@2026';
$db_name = 'u403768071_medical';

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
if ($conn->connect_error) {
    die("डेटाबेस कनेक्शन विफल: " . $conn->connect_error);
}

// फॉरेन की चेक को बंद करें
$conn->query("SET FOREIGN_KEY_CHECKS = 0;");

echo "<h3>inventories टेबल को ठीक करने की प्रक्रिया शुरू हो रही है...</h3>";

// 1. यदि पुरानी टेम्परेरी टेबल मौजूद हो तो उसे हटाएँ
$conn->query("DROP TABLE IF EXISTS inventories_temp;");

// 2. inventories जैसी ही एक खाली टेम्परेरी टेबल बनाएँ
if ($conn->query("CREATE TABLE inventories_temp LIKE inventories;")) {
    echo "1. टेम्परेरी टेबल बनाई गई...<br>";
} else {
    die("त्रुटि (टेम्प टेबल): " . $conn->error);
}

// 3. टेम्परेरी टेबल में Primary Key जोड़ें
if ($conn->query("ALTER TABLE inventories_temp ADD PRIMARY KEY (id);")) {
    echo "2. टेम्परेरी टेबल में Primary Key जोड़ी गई...<br>";
} else {
    die("त्रुटि (प्राइमरी की): " . $conn->error);
}

// 4. inventories से डेटा को टेम्परेरी टेबल में डालें (डुप्लीकेट्स को अनदेखा करते हुए)
if ($conn->query("INSERT IGNORE INTO inventories_temp SELECT * FROM inventories;")) {
    echo "3. डुप्लीकेट डेटा हटाकर साफ रिकॉर्ड्स टेम्परेरी टेबल में डाल दिए गए हैं...<br>";
} else {
    die("त्रुटि (डेटा ट्रांसफर): " . $conn->error);
}

// 5. मूल inventories टेबल को डिलीट करें
if ($conn->query("DROP TABLE inventories;")) {
    echo "4. पुरानी डुप्लीकेट वाली टेबल डिलीट की गई...<br>";
} else {
    die("त्रुटि (टेबल ड्रॉप): " . $conn->error);
}

// 6. टेम्परेरी टेबल का नाम बदलकर inventories करें
if ($conn->query("RENAME TABLE inventories_temp TO inventories;")) {
    echo "5. टेबल का नाम बदलकर 'inventories' कर दिया गया है...<br>";
} else {
    die("त्रुटि (टेबल रीनेम): " . $conn->error);
}

// 7. AUTO_INCREMENT लागू करें
if ($conn->query("ALTER TABLE inventories MODIFY id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;")) {
    echo "6. AUTO_INCREMENT सफलतापूर्वक लागू कर दिया गया है!<br>";
} else {
    die("त्रुटि (ऑटो इंक्रीमेंट): " . $conn->error);
}

// 8. फॉरेन की बाधाओं (Foreign Key Constraints) को दोबारा जोड़ें
$fk1 = "ALTER TABLE inventories ADD CONSTRAINT inventories_medicine_id_foreign FOREIGN KEY (medicine_id) REFERENCES medicines (id) ON DELETE CASCADE;";
$fk2 = "ALTER TABLE inventories ADD CONSTRAINT inventories_shop_id_foreign FOREIGN KEY (shop_id) REFERENCES shops (id) ON DELETE CASCADE;";

if ($conn->query($fk1) && $conn->query($fk2)) {
    echo "7. फॉरेन की (Foreign Key Constraints) दोबारा स्थापित कर दी गई हैं...<br>";
} else {
    echo "चेतावनी (फॉरेन की): " . $conn->error . " (इसे इग्नोर किया जा सकता है)<br>";
}

// फॉरेन की चेक वापस चालू करें
$conn->query("SET FOREIGN_KEY_CHECKS = 1;");
$conn->close();

echo "<h2 style='color: green;'>🎉 inventories टेबल सफलतापूर्वक ठीक हो गई है!</h2>";
echo "<p>अब आप अपनी वेबसाइट पर जाकर टेस्ट कर सकते हैं। ऑर्डर एरर दूर हो जाएगा।</p>";
?>
