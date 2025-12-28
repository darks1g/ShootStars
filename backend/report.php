<?php
session_start();
require_once __DIR__ . '/db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['error' => 'Debes iniciar sesión para reportar', 'redirect' => '/login.php']);
        exit;
    }

    $id_mensaje = $_POST['id_mensaje'] ?? null;
    $motivo = $_POST['motivo'] ?? '';
    $id_usuario = $_SESSION['user_id'];

    if (!$id_mensaje) {
        echo json_encode(['error' => 'Falta ID mensaje']);
        exit;
    }

    $conn = getDBConnection();

    // Check if duplicate report
    $check = $conn->prepare("SELECT id_reporte FROM reportes WHERE id_usuario = ? AND id_mensaje = ?");
    $check->bind_param("ii", $id_usuario, $id_mensaje);
    $check->execute();
    if ($check->get_result()->num_rows > 0) {
        echo json_encode(['error' => 'Ya has reportado este mensaje']);
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO reportes (id_usuario, id_mensaje, motivo) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $id_usuario, $id_mensaje, $motivo);

    if ($stmt->execute()) {
        // Auto-moderation check (e.g., if reports > 5 -> hide message)
        // For now, just insert.
        // TODO: Email admin if count > threshold (Anteproyecto feature)
        
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['error' => 'Error al guardar reporte']);
    }

    $conn->close();
}
