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
</head>

<body>
<!-- CONTENEDOR DEL MENSAJE -->
<div id="mensajeContainer" class="mensaje-container hidden">
    <div class="mensaje-box">

        <h1 class="logo">ShootStars</h1>

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