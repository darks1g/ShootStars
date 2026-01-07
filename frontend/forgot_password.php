<!DOCTYPE html>
<html lang="es" style="background-color: #000010;">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Contraseña - ShootStars</title>
    <link rel="icon" type="image/png" href="imgs/logo.png">
    <link rel="stylesheet" href="css/main.css">
</head>
<body>
    <div class="auth-container">
        <h1 class="logo-big">ShootStars</h1>
        <div class="auth-box glass-panel">
            <h2>Recuperar Contraseña</h2>
            <p style="text-align: center; color: #b0b0b0; margin-bottom: 20px; font-size: 0.9em;">
                Introduce tu correo electrónico y te enviaremos un enlace para restablecer tu contraseña.
            </p>
            <form action="/backend/auth/send_reset.php" method="POST">
                
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>
                </div>

                <?php if (isset($_GET['error'])): ?>
                    <p class="error-msg"><?php echo htmlspecialchars($_GET['error']); ?></p>
                <?php endif; ?>
                <?php if (isset($_GET['success'])): ?>
                    <p style="background: rgba(76, 201, 240, 0.2); border: 1px solid #4cc9f0; color: #4cc9f0; padding: 10px; border-radius: 5px; font-size: 0.9em;">
                        <?php echo htmlspecialchars($_GET['success']); ?>
                    </p>
                <?php endif; ?>

                <button type="submit" class="btn-primary">Enviar Enlace</button>
            </form>
            <p><a href="login">Volver a Iniciar Sesión</a></p>
        </div>
    </div>
    <canvas id="space"></canvas>
    <script src="js/bg.js"></script>
</body>
</html>
