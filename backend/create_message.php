<?php
/**
 * Endpoint para crear mensajes
 * Permite a usuarios autenticados crear nuevos mensajes.
 * Metodo: POST
 */
session_start();
require_once __DIR__ . '/db.php';

header('Content-Type: application/json');

// Verificar autenticacion
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'No autorizado']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $contenido = trim($_POST['contenido'] ?? '');

    // Validar
    if (mb_strlen($contenido) < 1) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'El mensaje no puede estar vacío']);
        exit;
    }

    if (mb_strlen($contenido) > 500) {
         http_response_code(400);
         echo json_encode(['success' => false, 'error' => 'El mensaje es demasiado largo (max 500 caracteres)']);
         exit;
    }

    $userId = $_SESSION['user_id'];
    $conn = getDBConnection();

    // Insertar mensaje
    $stmt = $conn->prepare("INSERT INTO mensajes (id_usuario, contenido) VALUES (?, ?)");
    $stmt->bind_param("is", $userId, $contenido);

    if ($stmt->execute()) {
        http_response_code(201);
        echo json_encode(['success' => true]);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Error al guardar el mensaje']);
    }

    $stmt->close();
    $conn->close();
} else {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Método no permitido']);
}
