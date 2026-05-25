<?php
function getDBConnection() {
    // Configurar ruta del .env
    $envPath = __DIR__ . '/../.env';
    
    if (!file_exists($envPath)) {
        die("Error: .env file not found.");
    }
    
    $env = parse_ini_file($envPath);

    $host = $env['DB_HOST'];
    $user = $env['DB_USER'];
    $pass = $env['DB_PASS'];
    $db   = $env['DB_NAME'];

    $conn = new mysqli($host, $user, $pass, $db);

    if ($conn->connect_error) {
        die("Error de conexion: " . $conn->connect_error);
    }
    
    $conn->set_charset("utf8mb4");
    
    return $conn;
}
