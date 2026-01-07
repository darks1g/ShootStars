<?php session_start(); ?>
<!DOCTYPE html>
<html lang="es" style="background-color: #000010;">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ShootStars</title>
    <link rel="icon" type="image/png" href="imgs/logo.png">
    <link rel="stylesheet" href="css/main.css">
    <script type="text/javascript" src="js/bg.js"></script>
    <script type="text/javascript" src="js/msg.js"></script>
    <script>
        const isLoggedIn = <?php echo isset($_SESSION['user_id']) ? 'true' : 'false'; ?>;
        const isAdmin = <?php echo (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1) ? 'true' : 'false'; ?>;
    </script>
</head>

<body>
    <header class="main-header">
        <div class="header-content">
            <?php if (isset($_SESSION['user_id'])): ?>
                <span class="welcome">Hola, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
                <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1): ?>
                    <a href="admin" class="btn-nav">Admin Panel</a>
                <?php endif; ?>
                <a href="dashboard" class="btn-nav">Mi Panel</a>
                <a href="/backend/auth/logout.php" class="btn-nav">Cerrar Sesión</a>
            <?php else: ?>
                <a href="login" class="btn-nav">Iniciar Sesión</a>
                <a href="register" class="btn-nav">Registrarse</a>
            <?php endif; ?>
        </div>
    </header>
    
    <!-- HERO SECTION -->
    <div class="hero-overlay" id="heroOverlay">
        <h1 class="hero-title">ShootStars</h1>
        <p class="hero-subtitle">Haz clic en el firmamento para leer una estrella</p>
    </div>
<!-- CONTENEDOR DEL MENSAJE -->
<div id="mensajeContainer" class="mensaje-container hidden">
    <div class="mensaje-box glass-panel">

        <h1 class="logo" style="display:none;">ShootStars</h1>

        <!-- Información del usuario -->
        <div class="top-info">
            <img src="imgs/avatar.png" class="avatar" alt="Avatar del usuario">

            <div class="user-info">
                <span class="nombre">Usuario</span>
                <span class="fecha">Fecha de envío</span>
            </div>

            <button class="report-btn">⚠</button>
        </div>

        <!-- Contenido del mensaje -->
        <div class="mensaje-texto">
            Mensaje de prueba
        </div>

        <!-- Reacciones -->
        <div class="reacciones">
            <button class="reaccion me_gusta">0 👍</button>
            <button class="reaccion risa">0 😂</button>
            <button class="reaccion triste">0 😢</button>
            <button class="reaccion enfado">0 😡</button>
            <button class="reaccion caca">0 🤢</button>
            <button class="reaccion sorpresa">0 😱</button>
            <button class="reaccion rezar">0 🙏</button>
            <button class="reaccion calavera">0 💀</button>
            <button class="reaccion corazon">0 ❤️</button>
        </div>

    </div>
</div>


    <canvas id="space"></canvas>
    <?php include 'includes/footer.php'; ?>
</body>

</html>