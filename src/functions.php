<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/vendor/autoload.php';

// SMTP Configuration
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'singhrewards32@gmail.com'); // Gmail ID
define('SMTP_PASSWORD', 'oveccubrjcxcabvv');      // App password
define('FROM_EMAIL', 'singhrewards32@gmail.com');
define('FROM_NAME', 'GitHub Timeline Service');

function generateVerificationCode() {
    return sprintf('%06d', mt_rand(0, 999999));
}

function registerEmail($email) {
    $file = __DIR__ . '/registered_emails.txt';
    $emails = file_exists($file) ? array_filter(explode("\n", trim(file_get_contents($file)))) : [];

    if (!in_array($email, $emails)) {
        $emails[] = $email;
        file_put_contents($file, implode("\n", $emails) . "\n");
    }
}

function unsubscribeEmail($email) {
    $file = __DIR__ . '/registered_emails.txt';
    if (file_exists($file)) {
        $emails = array_filter(explode("\n", trim(file_get_contents($file))));
        $emails = array_filter($emails, fn($e) => $e !== $email);
        file_put_contents($file, implode("\n", $emails) . "\n");
    }
}

function sendEmailPHPMailer($to, $subject, $message) {
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host       = SMTP_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = SMTP_USERNAME;
        $mail->Password   = SMTP_PASSWORD;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // TLS
        $mail->Port       = SMTP_PORT;

        $mail->setFrom(FROM_EMAIL, FROM_NAME);
        $mail->addAddress($to);
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $message;

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Email sending failed: {$mail->ErrorInfo}");
        return false;
    }
}

function sendVerificationEmail($email, $code) {
    $subject = "Your Verification Code";
    $message = "<p>Your verification code is: <strong>$code</strong></p>";
    return sendEmailPHPMailer($email, $subject, $message);
}

function sendUnsubscribeConfirmationEmail($email, $code) {
    $subject = "Confirm Unsubscription";
    $message = "<p>To confirm unsubscription, use this code: <strong>$code</strong></p>";
    return sendEmailPHPMailer($email, $subject, $message);
}

function fetchGitHubTimeline() {
    $context = stream_context_create([
        'http' => [
            'method'  => 'GET',
            'header'  => [
                'User-Agent: PHP Timeline Fetcher',
                'Accept: application/json',
            ],
            'timeout' => 30
        ]
    ]);

    $response = file_get_contents('https://api.github.com/events', false, $context);
    return $response ? json_decode($response, true) : null;
}

function formatGitHubData($data) {
    $html = "<h2>GitHub Timeline Updates</h2>\n";
    $html .= "<table border=\"1\">\n<tr><th>Event</th><th>User</th></tr>\n";

    if (is_array($data) && !empty($data)) {
        foreach ($data as $event) {
            $eventType = htmlspecialchars($event['type'] ?? 'Unknown');
            $user = htmlspecialchars($event['actor']['login'] ?? 'Unknown');
            $html .= "<tr><td>$eventType</td><td>$user</td></tr>\n";
        }
    } else {
        $html .= "<tr><td>Push</td><td>testuser</td></tr>\n"; // fallback
    }

    $html .= "</table>\n";
    return $html;
}

function sendGitHubUpdatesToSubscribers() {
    $file = __DIR__ . '/registered_emails.txt';
    if (!file_exists($file)) return;

    $emails = array_filter(explode("\n", trim(file_get_contents($file))));
    if (empty($emails)) return;

    $githubData = fetchGitHubTimeline();
    $formattedData = formatGitHubData($githubData);
    $unsubscribeUrl = "http://localhost:8000/unsubscribe.php";
    $emailBody = $formattedData . "<p><a href=\"$unsubscribeUrl\">Unsubscribe</a></p>";
    $subject = "Latest GitHub Updates";

    foreach ($emails as $email) {
        if (!empty(trim($email))) {
            sendEmailPHPMailer(trim($email), $subject, $emailBody);
        }
    }
}

?>
