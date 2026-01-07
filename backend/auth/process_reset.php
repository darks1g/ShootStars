<?php
session_start();
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/TokenManager.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['token'];
    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];

    if (empty($password) || empty($confirm)) {
        header("Location: /reset_password?token=$token&error=Campos vacíos");
        exit;
    }

    if ($password !== $confirm) {
        header("Location: /reset_password?token=$token&error=Las contraseñas no coinciden");
        exit;
    }

    if (strlen($password) < 8) {
        header("Location: /reset_password?token=$token&error=La contraseña es muy corta");
        exit;
    }

    // Validate Token again
    $tm = new TokenManager();
    $email = $tm->validateToken($token);

    if (!$email) {
        die("Error: Token inválido o expirado");
    }

    // Update Password in DB
    $conn = getDBConnection();
    $hashed = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("UPDATE usuarios SET contraseña = ? WHERE email = ?");
    $stmt->bind_param("ss", $hashed, $email);
    
    if ($stmt->execute()) {
        // Success
        $tm->removeToken($token);
        header("Location: /login?error=Contraseña actualizada con éxito, inicia sesión."); // abusing error param for generic msg or create success param in login
    } else {
        header("Location: /reset_password?token=$token&error=Error al actualizar base de datos");
    }

    $stmt->close();
    $conn->close();
}
