<?php
session_start();
require_once __DIR__ . '/../db.php';

// Check if logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: /login");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_SESSION['user_id'];
    $conn = getDBConnection();

    // Prepare Delete
    $stmt = $conn->prepare("DELETE FROM usuarios WHERE id_usuario = ?");
    $stmt->bind_param("i", $userId);

    if ($stmt->execute()) {
        // Success: Destroy session and redirect
        session_destroy();
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Error al eliminar cuenta']);
    }

    $stmt->close();
    $conn->close();
}
