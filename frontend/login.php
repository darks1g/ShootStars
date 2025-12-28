<!DOCTYPE html>
<html lang="es" style="background-color: #000010;">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - ShootStars</title>
    <link rel="icon" type="image/png" href="imgs/logo.png">
    <link rel="stylesheet" href="css/main.css">
</head>
<body>
    <div class="auth-container">
        <h1 class="logo">ShootStars</h1>
        <div class="auth-box">
            <h2>Iniciar Sesión</h2>
            <form action="/backend/auth/login.php" method="POST">
                
                <div class="form-group">
                    <label for="email">Email o Usuario</label>
                    <input type="text" id="email" name="email" required>
                </div>

                <div class="form-group">
                    <label for="password">Contraseña</label>
                    <input type="password" id="password" name="password" required>
                </div>

                <?php if (isset($_GET['error'])): ?>
                    <p class="error-msg"><?php echo htmlspecialchars($_GET['error']); ?></p>
                <?php endif; ?>

                <button type="submit" class="btn-primary">Entrar</button>
            </form>
            <p>¿No tienes cuenta? <a href="register.php">Regístrate</a></p>
            <p><a href="index.php">Volver al inicio</a></p>
        </div>
    </div>
    <canvas id="space"></canvas>
    <script src="js/bg.js"></script>
</body>
</html>
