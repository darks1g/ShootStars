<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code = trim($_POST['code']);

        if (empty($code)) {
        header("Location: /verify_2fa.php?error=Ingresa el código");
        exit;
    }

    if (!isset($_SESSION['temp_user_id']) || !isset($_SESSION['2fa_code'])) {
        header("Location: /login.php?error=Sesión expirada");
        exit;
    }

    // Check code
    if ($code === $_SESSION['2fa_code']) {
        // Success
        $_SESSION['user_id'] = $_SESSION['temp_user_id'];
        $_SESSION['username'] = $_SESSION['temp_username'];
        $_SESSION['is_admin'] = $_SESSION['temp_is_admin'];

        // Clear temp vars
        unset($_SESSION['temp_user_id']);
        unset($_SESSION['temp_username']);
        unset($_SESSION['temp_is_admin']);
        unset($_SESSION['2fa_code']);

        header("Location: /index.php");
        exit;
    } else {
        header("Location: /verify_2fa.php?error=Código incorrecto");
        exit;
    }
}
