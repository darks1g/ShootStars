<?php
header('Content-Type: application/json');

// Activar errores en desarrollo
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Cargar variables del entorno
$env = parse_ini_file(__DIR__ . "/../.env");

$host = $env['DB_HOST'];
$user = $env['DB_USER'];
$pass = $env['DB_PASS'];
$db   = $env['DB_NAME'];

// Conexión a la base de datos
$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    echo json_encode(["error" => "Error de conexión: " . $conn->connect_error]);
    exit;
}

// Query: mensaje aleatorio visible sin avatar
$sql = "SELECT 
            m.id_mensaje, 
            m.contenido, 
            m.fecha_creacion, 
            u.id_usuario, 
            u.nombre_usuario,
            m.me_gusta,
            m.risa,
            m.triste,
            m.enfado,
            m.caca,
            m.sorpresa,
            m.rezar,
            m.calavera,
            m.corazon
        FROM mensajes m
        JOIN usuarios u ON m.id_usuario = u.id_usuario
        WHERE m.visible = 1
        ORDER BY RAND()
        LIMIT 1";

$res = $conn->query($sql);

if ($res && $res->num_rows > 0) {
    $mensaje = $res->fetch_assoc();

    // Siempre usar avatar por defecto
    $mensaje['avatar'] = "imgs/avatar.png";

    echo json_encode($mensaje);
} else {
    echo json_encode(["error" => "No hay mensajes visibles en la base de datos"]);
}

$conn->close();
