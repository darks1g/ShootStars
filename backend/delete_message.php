<?php
session_start();
require_once __DIR__ . '/db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['success' => false, 'error' => 'No autorizado']);
        exit;
    }

    $id_mensaje = $_POST['id_mensaje'] ?? null;
    $id_usuario = $_SESSION['user_id'];

    if (!$id_mensaje) {
        echo json_encode(['success' => false, 'error' => 'ID mensaje faltante']);
        exit;
    }

    $conn = getDBConnection();

    // Verify ownership
    $stmt = $conn->prepare("DELETE FROM mensajes WHERE id_mensaje = ? AND id_usuario = ?");
    $stmt->bind_param("ii", $id_mensaje, $id_usuario);
    
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Mensaje no encontrado o no eres el dueño']);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Error al eliminar']);
    }

    $stmt->close();
    $conn->close();
}
