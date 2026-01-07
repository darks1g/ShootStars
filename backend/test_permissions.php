<?php
require_once 'db.php';

$conn = getDBConnection();

$sql = "CREATE TABLE IF NOT EXISTS password_resets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL,
    token VARCHAR(64) NOT NULL,
    expiry DATETIME NOT NULL,
    INDEX (email),
    INDEX (token)
)";

if ($conn->query($sql) === TRUE) {
    echo "Table 'password_resets' created successfully";
} else {
    echo "Error creating table: " . $conn->error;
}

$conn->close();
