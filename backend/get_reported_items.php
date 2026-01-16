<?php
/**
 * Get Reported Items (Messages & Ecos)
 * 
 * Fetches both messages and ecos that have >= 1 report.
 * Used by the Admin Panel.
 * 
 * Method: GET
 * Params: page, limit
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

// 2. Count Total Unique Reported Items
$totalSql = "SELECT 
                (SELECT COUNT(DISTINCT id_mensaje) FROM reportes WHERE id_mensaje IS NOT NULL) + 
                (SELECT COUNT(DISTINCT id_eco) FROM reportes WHERE id_eco IS NOT NULL) as total";
$totalRes = $conn->query($totalSql);
$totalItems = $totalRes->fetch_assoc()['total'];

// 3. Fetch Data (Union of Messages and Ecos)
$sql = "(SELECT 'mensaje' as tipo, m.id_mensaje as id, m.contenido, m.id_usuario, m.fecha_creacion, 
                COUNT(r.id_reporte) as total_reportes,
                GROUP_CONCAT(r.motivo SEPARATOR ' || ') as motivos
         FROM mensajes m
         JOIN reportes r ON m.id_mensaje = r.id_mensaje
         GROUP BY m.id_mensaje)
        UNION ALL
        (SELECT 'eco' as tipo, e.id_eco as id, e.contenido, e.id_usuario, e.fecha_creacion, 
                COUNT(r.id_reporte) as total_reportes,
                GROUP_CONCAT(r.motivo SEPARATOR ' || ') as motivos
         FROM ecos e
         JOIN reportes r ON e.id_eco = r.id_eco
         GROUP BY e.id_eco)
        ORDER BY total_reportes DESC
        LIMIT $limit OFFSET $offset";

$res = $conn->query($sql);
if (!$res) {
    echo json_encode(['error' => 'Error en la consulta: ' . $conn->error]);
    exit;
}

$data = [];
while ($row = $res->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode([
    'data' => $data,
    'pagination' => [
        'current_page' => $page,
        'total_pages' => ceil($totalItems / $limit),
        'total_items' => $totalItems
    ]
]);

$conn->close();
