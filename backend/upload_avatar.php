<?php
session_start();
require_once __DIR__ . '/db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Usuario no autenticado']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Método no permitido']);
    exit;
}

if (!isset($_FILES['avatar']) || $_FILES['avatar']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'error' => 'Error al subir la imagen']);
    exit;
}

$file = $_FILES['avatar'];
$allowedTypes = ['image/jpeg', 'image/png'];
$maxSize = 2 * 1024 * 1024; // 2MB

// Validate Type
$finfo = new finfo(FILEINFO_MIME_TYPE);
$mime = $finfo->file($file['tmp_name']);

if (!in_array($mime, $allowedTypes)) {
    echo json_encode(['success' => false, 'error' => 'Solo se permiten imágenes JPG o PNG']);
    exit;
}

// Validate Size
if ($file['size'] > $maxSize) {
    echo json_encode(['success' => false, 'error' => 'La imagen no debe superar los 2MB']);
    exit;
}

// Generate Name
$ext = ($mime === 'image/png') ? 'png' : 'jpg';
$userId = $_SESSION['user_id'];
$newFileName = uniqid('avatar_') . '_' . $userId . '.' . $ext;
$uploadDir = __DIR__ . '/../frontend/avatars/';
$uploadPath = $uploadDir . $newFileName;
$publicPath = 'avatars/' . $newFileName;

// Create dir if not exists (redundant if ran command, but safe)
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

// Move File
if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
    
    // Update DB
    $conn = getDBConnection();
    
    // First, optional: Delete old avatar if distinct from default? 
    // Implementation for simplicity: Just overwrite DB reference.
    
    $stmt = $conn->prepare("UPDATE usuarios SET avatar = ? WHERE id_usuario = ?");
    $stmt->bind_param("si", $publicPath, $userId);
    
    if ($stmt->execute()) {
        $_SESSION['avatar'] = $publicPath; // Update session
        echo json_encode(['success' => true, 'avatar' => $publicPath]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Error al actualizar base de datos']);
    }
    
    $stmt->close();
    $conn->close();

} else {
    echo json_encode(['success' => false, 'error' => 'Error al guardar el archivo en el servidor']);
}
