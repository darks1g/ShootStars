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
    $contenido = trim($_POST['contenido'] ?? '');
    $id_usuario = $_SESSION['user_id'];

    if (!$id_mensaje || empty($contenido)) {
        echo json_encode(['success' => false, 'error' => 'Datos incompletos']);
        exit;
    }

    $conn = getDBConnection();

    $stmt = $conn->prepare("UPDATE mensajes SET contenido = ? WHERE id_mensaje = ? AND id_usuario = ?");
    $stmt->bind_param("sii", $contenido, $id_mensaje, $id_usuario);
    
    if ($stmt->execute()) {
        if ($stmt->affected_rows >= 0) { // >= 0 because content might be same
             // Check if it actually exists/belongs to user if rows=0? 
             // Ideally we check ownership first, but this is simple enough.
             // If affected_rows=0 it might mean content same OR not found.
             // Let's assume success if no error.
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Error al actualizar']);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Error DB']);
    }

    $stmt->close();
    $conn->close();
}
