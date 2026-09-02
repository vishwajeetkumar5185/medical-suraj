<?php
/**
 * DAWALO Forgot Password Mail Test Script
 * Target Recipient: vishalsharmaskr9549@gmail.com
 * Access URL: https://medical.techomission.com/test_forgot_mail.php (or https://dawalo.com/test_forgot_mail.php)
 * DELETE after testing!
 */

define('LARAVEL_START', microtime(true));
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

use Illuminate\Support\Facades\Mail;
use App\Mail\ResetPasswordMail;
use Illuminate\Support\Str;

header('Content-Type: text/plain; charset=utf-8');
echo "=== DAWALO FORGOT PASSWORD MAIL DIAGNOSTICS ===\n";
echo "Time: " . date('Y-m-d H:i:s') . "\n\n";

$targetEmail = 'vishalsharmaskr9549@gmail.com';

// 1. Inspect Environment Mail Settings
echo "--- 1. Checking Mail Environment Configuration ---\n";
echo "MAIL_MAILER       : " . config('mail.default') . "\n";
echo "MAIL_HOST         : " . config('mail.mailers.smtp.host') . "\n";
echo "MAIL_PORT         : " . config('mail.mailers.smtp.port') . "\n";
echo "MAIL_USERNAME     : " . config('mail.mailers.smtp.username') . "\n";
echo "MAIL_PASSWORD     : " . (config('mail.mailers.smtp.password') ? "SET (" . strlen(config('mail.mailers.smtp.password')) . " chars) ✅" : "MISSING ❌") . "\n";
echo "MAIL_ENCRYPTION   : " . (config('mail.mailers.smtp.encryption') ?: 'tls') . "\n";
echo "MAIL_FROM_ADDRESS : " . config('mail.from.address') . "\n";
echo "MAIL_FROM_NAME    : " . config('mail.from.name') . "\n\n";

// 2. OpenSSL Socket Test to SMTP Host
echo "--- 2. Checking SMTP Socket Connection to smtp.gmail.com:465 ---\n";
$host = config('mail.mailers.smtp.host', 'smtp.gmail.com');
$port = config('mail.mailers.smtp.port', 465);
$timeout = 5;

$fp = @fsockopen("ssl://{$host}", $port, $errno, $errstr, $timeout);
if ($fp) {
    echo "✅ SMTP Server Port 465 Connection SUCCESSFUL!\n";
    fclose($fp);
} else {
    echo "❌ SMTP Server Port 465 Connection FAILED: [$errno] $errstr\n";
}
echo "\n";

// 3. Attempting to Dispatch Test Password Reset Email via Laravel Mail
echo "--- 3. Sending Password Reset Test Email to: {$targetEmail} ---\n";

$fakeToken = Str::random(64);

try {
    echo "Sending ResetPasswordMail to {$targetEmail}...\n";
    Mail::to($targetEmail)->send(new ResetPasswordMail($fakeToken, $targetEmail));
    
    echo "\n✅ SUCCESS! Password Reset email was accepted by SMTP server for delivery.\n";
    echo "   Recipient : {$targetEmail}\n";
    echo "   Subject   : Reset Your Dawalo Password\n";
    echo "   Test Link : " . url('/reset-password/' . $fakeToken . '?email=' . urlencode($targetEmail)) . "\n";
    echo "   Please check the inbox (or Spam folder) for {$targetEmail}.\n";
} catch (\Exception $e) {
    echo "\n❌ ERROR: Failed to send password reset email!\n";
    echo "   Exception Message : " . $e->getMessage() . "\n";
    echo "   File              : " . $e->getFile() . " (Line " . $e->getLine() . ")\n\n";
    
    if (str_contains($e->getMessage(), '535') || str_contains($e->getMessage(), 'BadCredentials')) {
        echo "⚠️ DIAGNOSIS: Gmail SMTP 535 Bad Credentials Error!\n";
        echo "   The Gmail App Password in .env (MAIL_PASSWORD) is invalid or revoked by Google.\n\n";
        echo "📌 HOW TO FIX:\n";
        echo "   1. Open Google Account for dawaloofficial@gmail.com (https://myaccount.google.com/)\n";
        echo "   2. Go to Security -> 2-Step Verification -> App Passwords.\n";
        echo "   3. Create a new App Password (name: 'Dawalo App').\n";
        echo "   4. Update MAIL_PASSWORD in .env with the new 16-character code (without spaces).\n";
    }
}

echo "\n=== END DIAGNOSTICS ===\n";
