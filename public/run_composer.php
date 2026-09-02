<?php
// Disable output buffering to send updates to browser in real-time
if (ob_get_level() === 0) ob_start();
header('Content-Type: text/plain');
header('Cache-Control: no-cache');
ini_set('max_execution_time', 600); // 10 minutes

echo "=== REMOTE COMPOSER INSTALLER FOR DAWALO ===\n\n";
ob_flush(); flush();

$rootPath = dirname(__DIR__);
$composerPhar = $rootPath . '/composer.phar';

// 1. Check if shell execution is disabled
echo "Step 1: Checking system execution permissions...\n";
$disabled_functions = explode(',', ini_get('disable_functions'));
$disabled_functions = array_map('trim', $disabled_functions);

if (in_array('exec', $disabled_functions)) {
    echo "❌ ERROR: 'exec' function is disabled on this server by your hosting provider.\n";
    echo "You cannot run Composer via browser. Please upload the 'vendor/' folder from your local system instead.\n";
    ob_flush(); flush();
    exit;
}
echo "✅ Shell execution ('exec') is enabled!\n\n";
ob_flush(); flush();

// 2. Download composer.phar if not exists
if (!file_exists($composerPhar)) {
    echo "Step 2: Downloading composer.phar...\n";
    ob_flush(); flush();
    $downloadUrl = 'https://getcomposer.org/composer.phar';
    if (@copy($downloadUrl, $composerPhar)) {
        echo "   - ✅ Downloaded composer.phar successfully!\n\n";
    } else {
        echo "   - ❌ Failed to download composer.phar. Trying curl fallback...\n";
        ob_flush(); flush();
        $ch = curl_init($downloadUrl);
        $fp = fopen($composerPhar, 'wb');
        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_exec($ch);
        curl_close($ch);
        fclose($fp);
        
        if (file_exists($composerPhar) && filesize($composerPhar) > 100000) {
            echo "   - ✅ Downloaded composer.phar via curl!\n\n";
        } else {
            echo "   - ❌ Fatal: Failed to write composer.phar. Check write permissions in root directory.\n";
            exit;
        }
    }
} else {
    echo "Step 2: composer.phar already exists ✅\n\n";
}
ob_flush(); flush();

// 3. Test if PHP CLI works
echo "Step 3: Testing PHP CLI and Composer integration...\n";
ob_flush(); flush();
chdir($rootPath);
$versionOutput = [];
$versionStatus = 0;
exec("php composer.phar -V 2>&1", $versionOutput, $versionStatus);
echo "   - Command output: " . implode("\n   - ", $versionOutput) . "\n";
echo "   - Exit status: " . $versionStatus . "\n";

if ($versionStatus !== 0) {
    echo "❌ ERROR: Cannot run PHP CLI command. Your server might use a different PHP binary path (e.g. /usr/local/bin/php).\n";
    echo "Trying alternative PHP command 'php83'...\n";
    ob_flush(); flush();
    $versionOutput2 = [];
    exec("php83 composer.phar -V 2>&1", $versionOutput2, $versionStatus2);
    echo "   - Command output: " . implode("\n   - ", $versionOutput2) . "\n";
    if ($versionStatus2 === 0) {
        $phpBinary = "php83";
        echo "✅ Found working PHP binary: 'php83'\n\n";
    } else {
        echo "❌ Cannot execute PHP from shell. Please upload the local 'vendor/' directory.\n";
        exit;
    }
} else {
    $phpBinary = "php";
    echo "✅ PHP CLI integration works perfectly!\n\n";
}
ob_flush(); flush();

// 4. Run composer require
echo "Step 4: Running Composer installation (this will download 39 packages)...\n";
echo "Command: {$phpBinary} composer.phar require minishlink/web-push --ignore-platform-reqs --no-interaction\n";
echo "Please wait, do not close or reload this page...\n\n";
ob_flush(); flush();

$output = [];
$returnCode = 0;

// Run command and pipe output to file so we can read it in real time
$logFile = $rootPath . '/composer_install.log';
if (file_exists($logFile)) @unlink($logFile);

// Run in background and redirect output to log file
$cmd = "{$phpBinary} composer.phar require minishlink/web-push --ignore-platform-reqs --no-interaction > " . escapeshellarg($logFile) . " 2>&1";
pclose(popen("start /B " . $cmd, "r")); // Windows background
if (strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN') {
    // Linux background
    exec($cmd . " &");
}

// Poll the log file and flush to browser in real-time
$lastPos = 0;
$startTime = time();
$timeout = 300; // 5 minutes

while (time() - $startTime < $timeout) {
    if (file_exists($logFile)) {
        clearstatcache(false, $logFile);
        $len = filesize($logFile);
        if ($len > $lastPos) {
            $f = fopen($logFile, 'rb');
            fseek($f, $lastPos);
            $newData = fread($f, $len - $lastPos);
            fclose($f);
            echo $newData;
            ob_flush(); flush();
            $lastPos = $len;
            
            // Check if process finished
            if (strpos($newData, 'INSTALLATION COMPLETED') !== false || 
                strpos($newData, 'Package operations') !== false ||
                strpos($newData, 'Generating optimized autoload files') !== false ||
                strpos($newData, 'Lock file operations') !== false ||
                strpos($newData, 'Locking minishlink/web-push') !== false
            ) {
                // Keep reading for a bit
            }
        }
    }
    
    // Check if the composer lock file was written and autoload generated
    if (file_exists($rootPath . '/vendor/minishlink/web-push/src/WebPush.php')) {
        echo "\n\n=== ✅ INSTALLATION COMPLETED SUCCESSFULLY! ===\n";
        echo "WebPush library found in vendor. You can now delete this script and debug_push.php.\n";
        @unlink($logFile);
        exit;
    }
    
    // Sleep for 1 second before checking again
    sleep(1);
}

echo "\n❌ Timeout reached. Please check the 'composer_install.log' file in your root folder for more details.\n";
