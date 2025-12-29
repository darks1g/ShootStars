<?php
// backend/email_helper.php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/vendor/autoload.php';

function sendEmail($to, $subject, $body) {
    // Load .env
    $envPath = __DIR__ . '/../.env';
    if (!file_exists($envPath)) {
        return ['success' => false, 'error' => '.env file missing'];
    }
    $env = parse_ini_file($envPath);

    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = $env['SMTP_USER'] ?? '';
        $mail->Password   = $env['SMTP_PASS'] ?? '';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; 
        $mail->Port       = 587;

        // Recipients
        $mail->setFrom($env['SMTP_USER'] ?? 'noreply@shootstars.com', 'ShootStars');
        $mail->addAddress($to);

        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $body;
        // $mail->AltBody = strip_tags($body);

        $mail->send();
        return ['success' => true];
    } catch (Exception $e) {
        return ['success' => false, 'error' => "Message could not be sent. Mailer Error: {$mail->ErrorInfo}"];
    }
}
