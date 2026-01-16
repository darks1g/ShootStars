<?php
/**
 * Admin Actions Handler
 * 
 * Handles administrative actions on messages:
 * - Delete Message
 * - Delete Message & Block User
 * - Ignore Reports (Clear report log for a message)
 * 
 * Method: POST
 * Input: `action`, `id_mensaje`, [optional] `block_user`
 * 
 * @package ShootStars\Admin
 */
session_start();
require_once __DIR__ . '/db.php';

header('Content-Type: application/json');

// 1. Admin Auth Check
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'No autorizado']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $id = $_POST['id'] ?? 0;
    $tipo = $_POST['tipo'] ?? 'mensaje';

    if (!$id) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'ID faltante']);
        exit;
    }

    $conn = getDBConnection();

    if ($action === 'delete') {
        // --- DELETE ACTION ---
        
        if ($tipo === 'eco') {
            $qry = $conn->prepare("SELECT id_usuario FROM ecos WHERE id_eco = ?");
        } else {
            $qry = $conn->prepare("SELECT id_usuario FROM mensajes WHERE id_mensaje = ?");
        }
        
        $qry->bind_param("i", $id);
        $qry->execute();
        $res = $qry->get_result();
        
        if ($res->num_rows == 0) {
            http_response_code(404);
            echo json_encode(['success' => false, 'error' => ucfirst($tipo) . ' no encontrado']);
            exit;
        }
        $authorId = $res->fetch_assoc()['id_usuario'];

        // 2. Delete the item
        if ($tipo === 'eco') {
            $del = $conn->prepare("DELETE FROM ecos WHERE id_eco = ?");
        } else {
            $del = $conn->prepare("DELETE FROM mensajes WHERE id_mensaje = ?");
        }
        $del->bind_param("i", $id);
        
        if ($del->execute()) {
            // 3. Optional: Block the User
            $blockUser = $_POST['block_user'] ?? 'false';
            if ($blockUser === 'true') {
                 $block = $conn->prepare("UPDATE usuarios SET estado = 'suspendido' WHERE id_usuario = ?");
                 $block->bind_param("i", $authorId);
                 $block->execute();
            }
            echo json_encode(['success' => true]);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Error al eliminar: ' . $conn->error]);
        }

    } elseif ($action === 'ignore') {
        // --- IGNORE ACTION ---
        if ($tipo === 'eco') {
            $delRep = $conn->prepare("DELETE FROM reportes WHERE id_eco = ?");
        } else {
            $delRep = $conn->prepare("DELETE FROM reportes WHERE id_mensaje = ?");
        }
        $delRep->bind_param("i", $id);
        
        if ($delRep->execute()) {
             echo json_encode(['success' => true]);
        } else {
             http_response_code(500);
             echo json_encode(['success' => false, 'error' => 'Error al limpiar reportes']);
        }
    } else {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Acción desconocida']);
    }

    $conn->close();
}
