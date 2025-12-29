<?php
require_once __DIR__ . '/../backend/db.php';

$conn = getDBConnection();

$sql = "
CREATE TABLE IF NOT EXISTS reacciones (
    id_reaccion INT AUTO_INCREMENT PRIMARY KEY,
    id_mensaje INT NOT NULL,
    id_usuario INT NULL,
    cookie_id VARCHAR(64) NULL,
    tipo VARCHAR(20) NOT NULL,
    fecha DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_mensaje) REFERENCES mensajes(id_mensaje) ON DELETE CASCADE,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE CASCADE,
    UNIQUE KEY unique_user_msg (id_mensaje, id_usuario),
    UNIQUE KEY unique_guest_msg (id_mensaje, cookie_id)
)";

if ($conn->query($sql) === TRUE) {
    echo "Table 'reacciones' created successfully.";
} else {
    echo "Error creating table: " . $conn->error;
}

$conn->close();
