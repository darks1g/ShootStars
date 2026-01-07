<?php
require_once __DIR__ . '/../backend/auth/TokenManager.php';

$token = $_GET['token'] ?? '';
$tm = new TokenManager();
$email = $tm->validateToken($token);

if (!$email) {
    die("Error: El enlace es inválido o ha expirado. <a href='forgot_password'>Solicitar uno nuevo</a>");
}
?>
<!DOCTYPE html>
<html lang="es" style="background-color: #000010;">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva Contraseña - ShootStars</title>
    <link rel="icon" type="image/png" href="imgs/logo.png">
    <link rel="stylesheet" href="css/main.css">
</head>
<body>
    <div class="auth-container">
        <h1 class="logo-big">ShootStars</h1>
        <div class="auth-box glass-panel">
            <h2>Nueva Contraseña</h2>
            <form action="/backend/auth/process_reset.php" method="POST">
                
                <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">

                <div class="form-group">
                    <label for="password">Nueva Contraseña</label>
                    <input type="password" id="password" name="password" required>
                    <small style="color: grey;">Mínimo 8 caracteres</small>
                </div>
                
                <div class="form-group">
                    <label for="confirm_password">Confirmar Contraseña</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                </div>

                <?php if (isset($_GET['error'])): ?>
                    <p class="error-msg"><?php echo htmlspecialchars($_GET['error']); ?></p>
                <?php endif; ?>

                <button type="submit" class="btn-primary">Cambiar Contraseña</button>
            </form>
        </div>
    </div>
    <canvas id="space"></canvas>
    <script src="js/bg.js"></script>
</body>
</html>
