<?php
require_once __DIR__ . '/../db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$conn = getDBConnection();

$field = $_POST['field'] ?? ''; // 'username' or 'email'
$value = trim($_POST['value'] ?? '');

if (!$field || !$value) {
    echo json_encode(['exists' => false]);
    exit;
}

$column = ($field === 'email') ? 'email' : 'nombre_usuario';

$stmt = $conn->prepare("SELECT id_usuario FROM usuarios WHERE $column = ?");
$stmt->bind_param("s", $value);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    echo json_encode(['exists' => true]);
} else {
    echo json_encode(['exists' => false]);
}

$stmt->close();
$conn->close();
