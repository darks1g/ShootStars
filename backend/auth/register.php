<?php
session_start();
require_once __DIR__ . '/../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($username) || empty($email) || empty($password)) {
        header("Location: /register.php?error=Todos los campos son obligatorios");
        exit;
    }

    $conn = getDBConnection();

    // Check if user exists
    $stmt = $conn->prepare("SELECT id_usuario FROM usuarios WHERE nombre_usuario = ? OR email = ?");
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        header("Location: /register.php?error=Usuario o email ya registrados");
        exit;
    }
    $stmt->close();

    // Insert new user
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    // estado default is 'activo' per schema
    $insert = $conn->prepare("INSERT INTO usuarios (nombre_usuario, email, contraseña) VALUES (?, ?, ?)");
    $insert->bind_param("sss", $username, $email, $hashed_password);

    if ($insert->execute()) {
        // Auto login or redirect to login? 
        // Redirect to login for now
        header("Location: /login.php?error=Registro exitoso. Inicia sesión.");
    } else {
        header("Location: /register.php?error=Error al registrar");
    }

    $insert->close();
    $conn->close();
}
