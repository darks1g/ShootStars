<!DOCTYPE html>
<html lang="es" style="background-color: #000010;">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificación 2FA - ShootStars</title>
    <link rel="icon" type="image/png" href="imgs/logo.png">
    <link rel="stylesheet" href="css/main.css">
</head>
<body>
    <div class="auth-container">
        <h1 class="logo-big">ShootStars</h1>
        <div class="auth-box glass-panel">
            <h2>Verificación de Dos Pasos</h2>
            <p style="margin-bottom: 15px; font-size: 0.9em;">Se ha enviado un código a tu correo.</p>
            <form action="/backend/auth/verify_2fa.php" method="POST">
                
                <div class="form-group">
                    <label for="code">Código de Verificación</label>
                    <input type="text" id="code" name="code" required autocomplete="off">
                </div>

                <?php if (isset($_GET['error'])): ?>
                    <p class="error-msg"><?php echo htmlspecialchars($_GET['error']); ?></p>
                <?php endif; ?>

                <button type="submit" class="btn-primary">Verificar</button>
            </form>
            <p><a href="login">Volver al Login</a></p>
        </div>
    </div>
    <canvas id="space"></canvas>
    <script src="js/bg.js"></script>
</body>
</html>
