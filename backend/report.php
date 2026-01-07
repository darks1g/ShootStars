<?php
session_start();
require_once __DIR__ . '/db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['error' => 'Debes iniciar sesión para reportar', 'redirect' => '/login.php']);
        exit;
    }

    $id_mensaje = $_POST['id_mensaje'] ?? null;
    $motivo = $_POST['motivo'] ?? '';
    $id_usuario = $_SESSION['user_id'];

    if (!$id_mensaje) {
        echo json_encode(['error' => 'Falta ID mensaje']);
        exit;
    }

    $conn = getDBConnection();

    // Check if duplicate report
    $check = $conn->prepare("SELECT id_reporte FROM reportes WHERE id_usuario = ? AND id_mensaje = ?");
    $check->bind_param("ii", $id_usuario, $id_mensaje);
    $check->execute();
    if ($check->get_result()->num_rows > 0) {
        echo json_encode(['error' => 'Ya has reportado este mensaje']);
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO reportes (id_usuario, id_mensaje, motivo) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $id_usuario, $id_mensaje, $motivo);

    if ($stmt->execute()) {
        // Check report count
        $countSql = $conn->prepare("SELECT COUNT(*) as total FROM reportes WHERE id_mensaje = ?");
        $countSql->bind_param("i", $id_mensaje);
        $countSql->execute();
        $cntVal = $countSql->get_result()->fetch_assoc()['total'];

        // Threshold = 5
        if ($cntVal >= 5) {
            require_once __DIR__ . '/email_helper.php';
            
            // Get Admin Email from .env or default
            $envPath = __DIR__ . '/../.env';
            if (file_exists($envPath)) {
                $env = parse_ini_file($envPath);
            }
            $adminEmail = $env['ADMIN_EMAIL'] ?? 'fsanjosehernan@hotmail.com'; // Default to author logic or env

            $subject = "Alerta: Mensaje muy reportado (ID: $id_mensaje)";
            $body = "<h1>Alerta de Moderación</h1>
                     <p>El mensaje con ID <b>$id_mensaje</b> ha recibido <b>$cntVal</b> reportes.</p>
                     <p>Por favor revisa el panel de administración.</p>";
            
            // Send async or sync? Sync for now.
            sendEmail($adminEmail, $subject, $body);
        }

        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['error' => 'Error al guardar reporte']);
    }

    $conn->close();
}
