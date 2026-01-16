<?php
require_once __DIR__ . '/db.php';

header('Content-Type: application/json');

$id_mensaje = $_GET['id_mensaje'] ?? null;
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 5;
$offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;

if (!$id_mensaje) {
    http_response_code(400);
    echo json_encode(['error' => 'ID de mensaje no proporcionado']);
    exit;
}

$conn = getDBConnection();
$stmt = $conn->prepare("SELECT id_eco, contenido, fecha_creacion FROM ecos WHERE id_mensaje = ? AND visible = 1 ORDER BY fecha_creacion DESC LIMIT ? OFFSET ?");
$stmt->bind_param("iii", $id_mensaje, $limit, $offset);
$stmt->execute();
$result = $stmt->get_result();

$ecos = [];
while ($row = $result->fetch_assoc()) {
    $ecos[] = $row;
}

// También devolver el total para saber si hay más
$stmt_total = $conn->prepare("SELECT COUNT(*) as total FROM ecos WHERE id_mensaje = ? AND visible = 1");
$stmt_total->bind_param("i", $id_mensaje);
$stmt_total->execute();
$total_res = $stmt_total->get_result()->fetch_assoc();

echo json_encode([
    'ecos' => $ecos,
    'total' => (int)$total_res['total']
]);

$stmt->close();
$stmt_total->close();
$conn->close();
