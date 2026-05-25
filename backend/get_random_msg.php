<?php
/**
 * Endpoint mensaje aleatorio
 * Obtiene un mensaje aleatorio y visible
 * Metodo: GET
 */
header('Content-Type: application/json');

ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);

// Cargar .env
$env = parse_ini_file(__DIR__ . "/../.env");

$host = $env['DB_HOST'];
$user = $env['DB_USER'];
$pass = $env['DB_PASS'];
$db   = $env['DB_NAME'];

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["error" => "Error de conexión"]);
    exit;
}

// Obtener mensaje aleatorio visible
$sql = "SELECT 
            m.id_mensaje, 
            m.contenido, 
            m.fecha_creacion, 
            u.id_usuario, 
            u.nombre_usuario,
            u.avatar,
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

    // Avatar por defecto
    if (empty($mensaje['avatar'])) {
        $mensaje['avatar'] = "imgs/default-pfp.jpg";
    }

    echo json_encode($mensaje);
} else {
    echo json_encode(["error" => "No hay mensajes visibles"]);
}

$conn->close();
