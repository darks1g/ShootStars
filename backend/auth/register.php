<?php
/**
 * Registration Handler
 * 
 * Handles new user registration.
 * 1. Validates inputs (empty check, password matching).
 * 2. Enforces strong password policy.
 * 3. Checks for duplicate username/email.
 * 4. Hashes password and inserts new user into database.
 * 
 * @package ShootStars\Auth
 */
session_start();
require_once __DIR__ . '/../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $password_confirm = $_POST['password_confirm'] ?? '';

    // 1. Basic Validation
    if (empty($username) || empty($email) || empty($password)) {
        header("Location: /register?error=Todos los campos son obligatorios");
        exit;
    }

    if ($password !== $password_confirm) {
        header("Location: /register?error=Las contraseñas no coinciden");
        exit;
    }

    // 2. Strong Password Policy Check
    // At least 8 chars, 1 uppercase, 1 lowercase, 1 number, 1 symbol
    if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/', $password)) {
        header("Location: /register?error=La contraseña es débil. Requiere 8+ caracteres, mayúsculas, minúsculas, números y símbolos.");
        exit;
    }

    $conn = getDBConnection();

    // 3. User Existence Check
    $stmt = $conn->prepare("SELECT id_usuario FROM usuarios WHERE nombre_usuario = ? OR email = ?");
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        header("Location: /register?error=Usuario o email ya registrados");
        exit;
    }
    $stmt->close();

    // 4. Create User
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    // 'estado' defaults to 'activo' as per schema
    $insert = $conn->prepare("INSERT INTO usuarios (nombre_usuario, email, contraseña) VALUES (?, ?, ?)");
    $insert->bind_param("sss", $username, $email, $hashed_password);

    if ($insert->execute()) {
        // Success: Redirect to login
        header("Location: /login?error=Registro exitoso. Inicia sesión.");
    } else {
        header("Location: /register?error=Error al registrar");
    }

    $insert->close();
    $conn->close();
}
