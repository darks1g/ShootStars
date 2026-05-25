<?php
/**
 * Obtener mensajes del usuario
 * Obtiene mensajes del usuario autenticado con paginacion
 * Metodo: GET
 */
session_start();
require_once __DIR__ . '/db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'No autorizado']);
    exit;
}

$conn = getDBConnection();
$userId = $_SESSION['user_id'];

// Parametros paginacion
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
$offset = ($page - 1) * $limit;

// Contar total
$countSql = "SELECT COUNT(*) as total FROM mensajes WHERE id_usuario = ?";
$stmtCount = $conn->prepare($countSql);
$stmtCount->bind_param("i", $userId);
$stmtCount->execute();
$totalRes = $stmtCount->get_result();
$totalRows = $totalRes->fetch_assoc()['total'];
$stmtCount->close();

// Obtener mensajes
$sql = "SELECT id_mensaje, contenido, fecha_creacion, visible, 
        me_gusta, risa, triste, enfado, caca, sorpresa, rezar, calavera, corazon 
        FROM mensajes 
        WHERE id_usuario = ? 
        ORDER BY fecha_creacion DESC 
        LIMIT ? OFFSET ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("iii", $userId, $limit, $offset);
$stmt->execute();
$result = $stmt->get_result();

$mensajes = [];
while ($row = $result->fetch_assoc()) {
    $mensajes[] = $row;
}

// Devolver datos y paginacion
echo json_encode([
    'data' => $mensajes,
    'pagination' => [
        'current_page' => $page,
        'total_pages' => ceil($totalRows / $limit),
        'total_items' => $totalRows
    ]
]);

$stmt->close();
$conn->close();
