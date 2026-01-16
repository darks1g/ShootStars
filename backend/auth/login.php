<?php
/**
 * Login Handler
 * 
 * Handles user authentication.
 * 1. Validates input.
 * 2. Checks database for user.
 * 3. Verifies password hash.
 * 4. Checks if account is suspended.
 * 5. Initiates 2FA flow by storing temporary session data.
 * 6. Sends verification code via Email.
 * 
 * @package ShootStars\Auth
 */
session_start();
require_once __DIR__ . '/../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $emailOrUser = trim($_POST['email']);
    $password = $_POST['password'];

    // 1. Validate Input
    if (empty($emailOrUser) || empty($password)) {
        header("Location: /login?error=Campos vacíos");
        exit;
    }

    $conn = getDBConnection();

    // 2. Retrieve User Data
    $stmt = $conn->prepare("SELECT id_usuario, nombre_usuario, contraseña, email, es_admin, avatar, estado FROM usuarios WHERE email = ? OR nombre_usuario = ?");
    $stmt->bind_param("ss", $emailOrUser, $emailOrUser);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows === 1) {
        $user = $res->fetch_assoc();
        
        // 3. Check Account Status
        if ($user['estado'] === 'suspendido') {
            header("Location: /login?error=Tu cuenta está suspendida.");
            exit;
        }

        // 4. Verify Password
        if (password_verify($password, $user['contraseña'])) {
            // Login Success - Initialize 2FA Session
            $_SESSION['temp_user_id'] = $user['id_usuario'];
            $_SESSION['temp_username'] = $user['nombre_usuario'];
            $_SESSION['temp_is_admin'] = $user['es_admin'];
            $_SESSION['temp_avatar'] = $user['avatar'];
            
            // 5. Generate 2FA Code
            $code = (string)rand(100000, 999999);
            $_SESSION['2fa_code'] = $code;

            // 6. Send Verification Email
            require_once __DIR__ . '/../email_helper.php';
            $result = sendEmail($user['email'], 'Código de Verificación - ShootStars', "<h2>Código de verificación</h2><p>Tu código es: <b>$code</b></p>");

            if (!$result['success']) {
                error_log("Mail Error: " . $result['error']);
                header("Location: /login?error=Error al enviar código de verificación");
                exit;
            }
            
            // Redirect to 2FA Page
            header("Location: /verify_2fa");
            exit;
        } else {
            header("Location: /login?error=Credenciales incorrectas"); // Invalid Password
        }
    } else {
        header("Location: /login?error=Credenciales incorrectas"); // User not found
    }

    $stmt->close();
    $conn->close();
}
