<?php
/**
 * Sync Reactions Counter Script
 * Recalculates all reaction counts from the 'reacciones' table and updates 'mensajes'.
 * Run this to fix data consistencies.
 */

require_once __DIR__ . '/db.php';
$conn = getDBConnection();

header('Content-Type: text/plain');

echo "Starting Sync Process...\n";

$reaction_types = ['me_gusta', 'risa', 'triste', 'enfado', 'caca', 'sorpresa', 'rezar', 'calavera', 'corazon'];

// Get all message IDs that have reactions
$sql = "SELECT DISTINCT id_mensaje FROM reacciones";
$res = $conn->query($sql);

$total_updated = 0;

if ($res->num_rows > 0) {
    while ($row = $res->fetch_assoc()) {
        $mid = $row['id_mensaje'];
        
        // Build the update query dynamically
        $updates = [];
        
        foreach ($reaction_types as $type) {
            // Count for this specific message and type
            $countSql = "SELECT COUNT(*) as c FROM reacciones WHERE id_mensaje = $mid AND tipo = '$type'";
            $cRes = $conn->query($countSql);
            $count = $cRes->fetch_assoc()['c'];
            
            $updates[] = "$type = $count";
        }
        
        $updateStr = implode(", ", $updates);
        $updateSql = "UPDATE mensajes SET $updateStr WHERE id_mensaje = $mid";
        
        if ($conn->query($updateSql)) {
            $total_updated++;
        } else {
            echo "Error updating Message $mid: " . $conn->error . "\n";
        }
    }
}

echo "Sync Complete. Updated $total_updated messages.\n";
$conn->close();
