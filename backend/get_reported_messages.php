<?php
/**
 * Get Reported Messages
 * 
 * Fetches messages that have >= 1 report, with pagination.
 * Used by the Admin Panel.
 * 
 * Method: GET
 * Params: page, limit
 * 
 * @package ShootStars\Admin
 */
session_start();
require_once __DIR__ . '/db.php';
header('Content-Type: application/json');

// Auth Check (Admin Only)
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    http_response_code(403);
    echo json_encode([]);
    exit;
}

$conn = getDBConnection();

// 1. Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
$offset = ($page - 1) * $limit;

// 2. Count Total Reports (Distinct messages)
$countSql = "SELECT COUNT(DISTINCT id_mensaje) as total FROM reportes";
$totalRes = $conn->query($countSql);
$totalRows = $totalRes->fetch_assoc()['total'];

// 3. Fetch Data with Aggregated Report Reasons
$sql = "SELECT m.id_mensaje, m.contenido, m.id_usuario, m.fecha_creacion, 
               COUNT(r.id_reporte) as total_reportes,
               GROUP_CONCAT(r.motivo SEPARATOR ' || ') as motivos
        FROM mensajes m
        JOIN reportes r ON m.id_mensaje = r.id_mensaje
        GROUP BY m.id_mensaje
        ORDER BY total_reportes DESC
        LIMIT $limit OFFSET $offset";

$res = $conn->query($sql);
$data = [];
while ($row = $res->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode([
    'data' => $data,
    'pagination' => [
        'current_page' => $page,
        'total_pages' => ceil($totalRows / $limit),
        'total_items' => $totalRows
    ]
]);

$conn->close();
