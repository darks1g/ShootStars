<?php
session_start();
require_once __DIR__ . '/db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'No autorizado']);
    exit;
}

$conn = getDBConnection();
$id_usuario = $_SESSION['user_id'];

$sql = "SELECT id_mensaje, contenido, fecha_creacion, visible, 
        me_gusta, risa, triste, enfado, caca, sorpresa, rezar, calavera, corazon 
        FROM mensajes 
        WHERE id_usuario = ? 
        ORDER BY fecha_creacion DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$res = $stmt->get_result();

$mensajes = [];
while ($row = $res->fetch_assoc()) {
    $mensajes[] = $row;
}

echo json_encode($mensajes);

$stmt->close();
$conn->close();
