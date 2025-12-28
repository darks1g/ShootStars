<?php
session_start();
require_once __DIR__ . '/../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $emailOrUser = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($emailOrUser) || empty($password)) {
        header("Location: /login.php?error=Campos vacíos");
        exit;
    }

    $conn = getDBConnection();

    $stmt = $conn->prepare("SELECT id_usuario, nombre_usuario, contraseña, email, es_admin FROM usuarios WHERE email = ? OR nombre_usuario = ?");
    $stmt->bind_param("ss", $emailOrUser, $emailOrUser);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows === 1) {
        $user = $res->fetch_assoc();
        if (password_verify($password, $user['contraseña'])) {
            // Login Success - Start 2FA Flow
            $_SESSION['temp_user_id'] = $user['id_usuario'];
            $_SESSION['temp_username'] = $user['nombre_usuario'];
            $_SESSION['temp_is_admin'] = $user['es_admin'];
            
            // Generate 2FA Code (Fixed for dev or random)
            // Generate 2FA Code
            $code = (string)rand(100000, 999999);
            $_SESSION['2fa_code'] = $code;

            // Send email
            require_once __DIR__ . '/../email_helper.php';
            $result = sendEmail($user['email'], 'Código de Verificación - ShootStars', "<h2>Código de verificación</h2><p>Tu código es: <b>$code</b></p>");

            if (!$result['success']) {
                error_log("Mail Error: " . $result['error']);
                header("Location: /login.php?error=Error al enviar código de verificación");
                exit;
            }
            
            header("Location: /verify_2fa.php");
            exit;
        } else {
            header("Location: /login.php?error=Credenciales incorrectas");
        }
    } else {
        header("Location: /login.php?error=Credenciales incorrectas");
    }

    $stmt->close();
    $conn->close();
}
