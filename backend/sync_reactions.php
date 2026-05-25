<?php
/**
 * Script para sincronizar contadores de reacciones
 * Recalcula todos los conteos de reacciones
 */

require_once __DIR__ . '/db.php';
$conn = getDBConnection();

header('Content-Type: text/plain');

echo "Iniciando sincronizacion...\n";

$reaction_types = ['me_gusta', 'risa', 'triste', 'enfado', 'caca', 'sorpresa', 'rezar', 'calavera', 'corazon'];

// Obtener mensajes con reacciones
$sql = "SELECT DISTINCT id_mensaje FROM reacciones";
$res = $conn->query($sql);

$total_updated = 0;

if ($res->num_rows > 0) {
    while ($row = $res->fetch_assoc()) {
        $mid = $row['id_mensaje'];
        
        // Construir consulta de actualizacion dinamica
        $updates = [];
        
        foreach ($reaction_types as $type) {
            // Contar para este mensaje y tipo
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
            echo "Error al actualizar mensaje $mid: " . $conn->error . "\n";
        }
    }
}

echo "Sincronizacion completa. Se actualizaron $total_updated mensajes.\n";
$conn->close();
