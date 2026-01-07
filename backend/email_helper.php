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
        $mail->CharSet    = 'UTF-8'; // Fix Encoding

        // Recipients
        $mail->setFrom($env['SMTP_USER'] ?? 'noreply@shootstars.com', 'ShootStars');
        $mail->addAddress($to);

        // Styling Template
        // We use inline styles for maximum compatibility, but try to import fonts
        $fullBody = "
        <!DOCTYPE html>
        <html lang='es'>
        <head>
            <meta charset='UTF-8'>
            <style>
                @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;700&family=Orbitron:wght@700&display=swap');
                body { margin: 0; padding: 0; background-color: #050014; }
            </style>
        </head>
        <body style='margin: 0; padding: 0; background-color: #050014; font-family: \"Outfit\", sans-serif;'>
            <table width='100%' cellpadding='0' cellspacing='0' border='0' style='background: #050014; background: radial-gradient(circle at bottom center, #240046 0%, #10002b 40%, #000000 100%);'>
                <tr>
                    <td align='center' style='padding: 20px 10px;'>
                        
                        <!-- Main Card -->
                        <table width='100%' cellpadding='0' cellspacing='0' border='0' style='max-width: 400px; width: 100%; border-radius: 12px; border: 1px solid rgba(255, 255, 255, 0.1); background-color: rgba(30, 20, 60, 0.9); box-shadow: 0 0 30px rgba(114, 9, 183, 0.3);'>
                            <tr>
                                <td align='center' style='padding: 25px;'>
                                    
                                    <!-- Logo -->
                                    <h1 style=\"font-family: 'Orbitron', sans-serif; font-size: 22px; color: #ffffff; text-shadow: 0 0 10px #4cc9f0; margin: 0 0 15px 0; letter-spacing: 2px; text-transform: uppercase;\">
                                        ShootStars
                                    </h1>
                                    
                                    <!-- Content -->
                                    <div style=\"color: #e0bbff; font-family: 'Outfit', sans-serif; font-size: 14px; line-height: 1.5; text-align: center;\">
                                        $body
                                    </div>

                                    <!-- Divider -->
                                    <hr style='border: 0; border-top: 1px solid rgba(255,255,255,0.1); margin: 20px 0;'>

                                    <!-- Footer -->
                                    <div style='color: #4cc9f0; font-family: \"Outfit\", sans-serif; font-size: 11px;'>
                                        &copy; " . date('Y') . " ShootStars.
                                    </div>

                                </td>
                            </tr>
                        </table>

                    </td>
                </tr>
            </table>
        </body>
        </html>";

        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $fullBody;
        $mail->AltBody = strip_tags($body);

        $mail->send();
        return ['success' => true];
    } catch (Exception $e) {
        return ['success' => false, 'error' => "Message could not be sent. Mailer Error: {$mail->ErrorInfo}"];
    }
}
