<?php
/**
 * Manejador de reacciones
 * Registra reacciones a mensajes (usuarios y visitantes)
 * Previene reacciones duplicadas
 * Metodo: POST
 */
session_start();
require_once __DIR__ . '/db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_mensaje = $_POST['id_mensaje'] ?? null;
    $tipo = $_POST['tipo'] ?? null;
    
    // Tipos de reaccion permitidos
    $allowed = ['me_gusta','risa','triste','enfado','caca','sorpresa','rezar','calavera','corazon'];

    if (!$id_mensaje || !$tipo || !in_array($tipo, $allowed)) {
        http_response_code(400);
        echo json_encode(['error' => 'Datos inválidos']);
        exit;
    }

    $conn = getDBConnection();

    // Identificar usuario (autenticado o visitante)
    $id_usuario = $_SESSION['user_id'] ?? null;
    $cookie_id = null;

    if (!$id_usuario) {
        if (!isset($_COOKIE['guest_id'])) {
            $guest_id = bin2hex(random_bytes(16));
            setcookie('guest_id', $guest_id, time() + (86400 * 365), "/");
            $cookie_id = $guest_id;
        } else {
            $cookie_id = $_COOKIE['guest_id'];
        }
    }

    // Verificar reaccion duplicada
    if ($id_usuario) {
        $check = $conn->prepare("SELECT id_reaccion FROM reacciones WHERE id_mensaje = ? AND id_usuario = ?");
        $check->bind_param("ii", $id_mensaje, $id_usuario);
    } else {
        $check = $conn->prepare("SELECT id_reaccion FROM reacciones WHERE id_mensaje = ? AND cookie_id = ?");
        $check->bind_param("is", $id_mensaje, $cookie_id);
    }

    $check->execute();
    $check_res = $check->get_result();

    if ($check_res->num_rows > 0) {
        http_response_code(409);
        echo json_encode(['success' => false, 'error' => 'Ya has reaccionado a este mensaje']);
        $conn->close();
        exit;
    }

    // Registrar reaccion
    if ($id_usuario) {
        $insert = $conn->prepare("INSERT INTO reacciones (id_mensaje, id_usuario, tipo) VALUES (?, ?, ?)");
        $insert->bind_param("iis", $id_mensaje, $id_usuario, $tipo);
    } else {
        $insert = $conn->prepare("INSERT INTO reacciones (id_mensaje, cookie_id, tipo) VALUES (?, ?, ?)");
        $insert->bind_param("iss", $id_mensaje, $cookie_id, $tipo);
    }

    if ($insert->execute()) {
        // Actualizar contadores en mensaje
        $stmt = $conn->prepare("UPDATE mensajes SET `$tipo` = `$tipo` + 1 WHERE id_mensaje = ?");
        $stmt->bind_param("i", $id_mensaje);
        $stmt->execute();
        
        // Obtener nuevo conteo para actualizar UI
        $get = $conn->prepare("SELECT `$tipo` FROM mensajes WHERE id_mensaje = ?");
        $get->bind_param("i", $id_mensaje);
        $get->execute();
        $res = $get->get_result();
        $row = $res->fetch_assoc();
        
        echo json_encode(['success' => true, 'nuevos_votos' => $row[$tipo]]);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Error al guardar reacción']);
    }
    
    $conn->close();
} else {
    http_response_code(405);
}
