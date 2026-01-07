<?php
session_start();
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/TokenManager.php';
require_once __DIR__ . '/../email_helper.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);

    if (empty($email)) {
        header("Location: /forgot_password?error=Ingresa tu email");
        exit;
    }

    $conn = getDBConnection();

    // Check if email exists in DB
    $stmt = $conn->prepare("SELECT id_usuario FROM usuarios WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Generate Token
        $tm = new TokenManager();
        $token = $tm->createToken($email);

        // Send Email
        // Assuming HTTPS for production, but using current host relative
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
        $host = $_SERVER['HTTP_HOST'];
        $link = "$protocol://$host/reset_password?token=$token";

        $subject = "Restablecer Contraseña - ShootStars";
        $body = "<h2>Restablecer Contraseña</h2>
                 <p>Hemos recibido una solicitud para restablecer tu contraseña.</p>
                 <p>Haz clic en el siguiente botón para continuar:</p>
                 <p style='text-align: center; margin: 30px 0;'>
                    <a href='$link' style='background: #4cc9f0; color: #000; padding: 12px 24px; text-decoration: none; border-radius: 5px; font-weight: bold;'>Restablecer Contraseña</a>
                 </p>
                 <p>Si no has solicitado esto, puedes ignorar este correo.</p>
                 <p><small>El enlace expira en 1 hora.</small></p>";

        $result = sendEmail($email, $subject, $body);

        if ($result['success']) {
            header("Location: /forgot_password?success=Revisa tu correo para continuar.");
        } else {
            error_log("Mail Error: " . $result['error']);
            header("Location: /forgot_password?error=Error al enviar el correo.");
        }
    } else {
        // Security: Don't reveal if email user exists or not, OR just simulate success?
        // For UX simplicity in this project:
        header("Location: /forgot_password?success=Si el email existe, se envió el enlace.");
    }

    $stmt->close();
    $conn->close();
}
