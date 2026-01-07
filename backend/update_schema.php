<?php
require_once 'db.php';

$conn = getDBConnection();

$sql = "ALTER TABLE usuarios 
        ADD COLUMN reset_token VARCHAR(64) NULL DEFAULT NULL,
        ADD COLUMN reset_expiry DATETIME NULL DEFAULT NULL";

if ($conn->query($sql) === TRUE) {
    echo "Columns added successfully";
} else {
    echo "Error updating record: " . $conn->error;
}

$conn->close();
