<?php
require_once __DIR__ . '/db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_mensaje = $_POST['id_mensaje'] ?? null;
    $tipo = $_POST['tipo'] ?? null;
    
    // Allowed columns
    $allowed = ['me_gusta','risa','triste','enfado','caca','sorpresa','rezar','calavera','corazon'];

    if (!$id_mensaje || !$tipo || !in_array($tipo, $allowed)) {
        echo json_encode(['error' => 'Datos inválidos']);
        exit;
    }

    $conn = getDBConnection();

    // Increment counter
    // Use safe query
    $stmt = $conn->prepare("UPDATE mensajes SET `$tipo` = `$tipo` + 1 WHERE id_mensaje = ?");
    $stmt->bind_param("i", $id_mensaje);
    
    if ($stmt->execute()) {
        // Get new count
        $get = $conn->prepare("SELECT `$tipo` FROM mensajes WHERE id_mensaje = ?");
        $get->bind_param("i", $id_mensaje);
        $get->execute();
        $res = $get->get_result();
        $row = $res->fetch_assoc();
        
        echo json_encode(['success' => true, 'nuevos_votos' => $row[$tipo]]);
    } else {
        echo json_encode(['error' => 'Error al actualizar DB']);
    }
    
    $conn->close();
}
