<?php
/**
 * Create Message Endpoint
 * 
 * Allows authenticated users to post new messages.
 * 
 * Method: POST
 * Input: `contenido` (string, 1-500 chars)
 * Output: JSON { success: true } or { success: false, error: string }
 * 
 * @package ShootStars\Messages
 */
session_start();
require_once __DIR__ . '/db.php';

header('Content-Type: application/json');

// 1. Auth Check
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'No autorizado']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $contenido = trim($_POST['contenido'] ?? '');

    // 2. Validation
    if (mb_strlen($contenido) < 1) {
        http_response_code(400); // Bad Request
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

    // 3. Insert Message
    $stmt = $conn->prepare("INSERT INTO mensajes (id_usuario, contenido) VALUES (?, ?)");
    $stmt->bind_param("is", $userId, $contenido);

    if ($stmt->execute()) {
        http_response_code(201); // Created
        echo json_encode(['success' => true]);
    } else {
        http_response_code(500); // Service Unavailable
        echo json_encode(['success' => false, 'error' => 'Error al guardar el mensaje']);
    }

    $stmt->close();
    $conn->close();
} else {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['success' => false, 'error' => 'Método no permitido']);
}
