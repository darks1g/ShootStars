<?php
require_once __DIR__ . '/backend/db.php';
session_start();

// Mock login (or create a user if needed, but we assume data exists or we mock it)
// Let's just use DB directly to test storage capability.
$conn = getDBConnection();

$test_content = "Hello World 🌍🚀⭐";
$user_id = 1; // Assuming user 1 exists, if not we might fail FK but let's try.

// Check if user 1 exists, if not create dummy
$check_user = $conn->query("SELECT id_usuario FROM usuarios LIMIT 1");
if ($check_user->num_rows > 0) {
    $row = $check_user->fetch_assoc();
    $user_id = $row['id_usuario'];
} else {
    die("No users found to attach message to.");
}

$stmt = $conn->prepare("INSERT INTO mensajes (id_usuario, contenido) VALUES (?, ?)");
$stmt->bind_param("is", $user_id, $test_content);

if ($stmt->execute()) {
    $msg_id = $stmt->insert_id;
    echo "Inserted message ID: $msg_id\n";
    
    // Read back
    $res = $conn->query("SELECT contenido FROM mensajes WHERE id_mensaje = $msg_id");
    $row = $res->fetch_assoc();
    
    echo "Original: " . $test_content . "\n";
    echo "Stored:   " . $row['contenido'] . "\n";
    
    if ($test_content === $row['contenido']) {
        echo "SUCCESS: Emojis preserved.\n";
    } else {
        echo "FAILURE: Content mismatch.\n";
    }
    
    // Cleanup
    $conn->query("DELETE FROM mensajes WHERE id_mensaje = $msg_id");
    
} else {
    echo "Insert failed: " . $conn->error . "\n";
}
