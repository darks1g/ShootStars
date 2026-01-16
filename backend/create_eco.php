<?php
session_start();
require_once __DIR__ . '/db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Debes estar logueado para enviar un eco']);
    exit;
}

$id_mensaje = $_POST['id_mensaje'] ?? null;
$contenido = $_POST['contenido'] ?? '';
$id_usuario = $_SESSION['user_id'];

if (!$id_mensaje || empty(trim($contenido))) {
    http_response_code(400);
    echo json_encode(['error' => 'Datos insuficientes']);
    exit;
}

// Limitar longitud del eco (por ejemplo 150 caracteres para mantenerlo breve)
if (mb_strlen($contenido) > 150) {
    http_response_code(400);
    echo json_encode(['error' => 'El eco es demasiado largo (máx 150 caracteres)']);
    exit;
}

$conn = getDBConnection();
$stmt = $conn->prepare("INSERT INTO ecos (id_mensaje, id_usuario, contenido) VALUES (?, ?, ?)");
$stmt->bind_param("iis", $id_mensaje, $id_usuario, $contenido);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Error al guardar el eco: ' . $conn->error]);
}

$stmt->close();
$conn->close();
