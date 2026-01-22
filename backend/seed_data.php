<?php
/**
 * Database Seeder for ShootStars
 * Populates database with diverse simulated activity using direct SQL for speed.
 * 
 * Usage: php backend/seed_data.php
 */

require_once __DIR__ . '/db.php';
$conn = getDBConnection();

// DATASETS

$users = [
    ['CosmicWanderer', 'wanderer@example.com'],
    ['StardustDreamer', 'dreamer@example.com'],
    ['VoidGazer', 'void@example.com'],
    ['LunaLove', 'luna@example.com'],
    ['SolarFlare', 'solar@example.com'],
    ['NebulaNavigator', 'nebula@example.com'],
    ['BlackHoleHeart', 'voidheart@example.com'],
    ['GalacticJester', 'jester@example.com'],
    ['AstroPhilosopher', 'astro@example.com'],
    ['RocketMan', 'rocket@example.com'],
    ['StarryEyes', 'eyes@example.com'],
    ['MeteorShower', 'meteor@example.com'],
    ['SpaceOddity', 'bowie@example.com'],
    ['QuantumLeap', 'quantum@example.com'],
    ['EventHorizon', 'horizon@example.com']
];

$messages = [
    // Deep / Philosophical
    "A veces miro al cielo y me pregunto si alguien más está mirando la misma estrella y sintiendo la misma soledad.",
    "El universo no es hostil, ni amigable. Es simplemente indiferente.",
    "Somos polvo de estrellas que piensa acerca de las estrellas.",
    "¿Y si el silencio del espacio es simplemente que todos están escondidos?",
    "La entropía es el precio que pagamos por el tiempo.",
    
    // Sad / Emotional
    "Hoy te extrañé un poco más que ayer, y un poco menos que mañana.",
    "Desearía poder enviar mi tristeza en un cohete y que se pierda en la nada.",
    "El vacío de ahí fuera se parece mucho al que siento aquí dentro.",
    "Llorar en el espacio es imposible, las lágrimas no caen. Como mis sentimientos.",
    "Me rompieron el corazón y ahora orbito solo en la oscuridad.",

    // Happy / Hopeful
    "¡He conseguido el trabajo de mis sueños! El universo a veces conspira a favor.",
    "La vida es bella, como una supernova explotando en colores.",
    "Hoy sonreí a un extraño y me devolvió la sonrisa. Pequeños milagros.",
    "Amo el olor a lluvia, es como si la Tierra suspirara aliviada.",
    "Todo va a salir bien. Confía en el proceso.",

    // Shitposting / Random
    "¿Creen que los alienígenas evitan la Tierra porque somos el reality show del universo?",
    "Si el universo es infinito, ¿hay un planeta hecho de queso? Es para una tarea.",
    "He comido pizza fría y es mejor que el amor verdadero.",
    "Mi gato me mira como si supiera los secretos del Big Bang y no me los quisiera contar.",
    "¿Por qué todo junto se escribe separado y separado se escribe todo junto?",
    "Auxilio, mandé un mensaje borracho a mi ex y ahora quiero que me trague un agujero negro.",

    // Short
    "Te quiero.",
    "Tengo hambre.",
    "Miedo.",
    "Esperanza.",
    "Silencio.",
    "Ruido blanco."
];

$ecos_pool = [
    // Supportive
    "Ánimo, tú puedes con todo.",
    "Te entiendo perfectamente.",
    "Un abrazo a la distancia.",
    "No estás sol@ en esto.",
    "Sigue brillando, estrella.",
    
    // Philosophical Reply
    "Quizás tienes razón.",
    "Es una perspectiva interesante.",
    "La verdad está ahí fuera.",
    "Profundo.",
    
    // Funny / Random Reply
    "JAJAJAJA literal yo.",
    "Señor, esto es un Wendy's.",
    "x2",
    "Basado.",
    "¿Me das de lo que fumas?",
    
    // Questioning
    "¿Por qué piensas eso?",
    "¿Estás bien?",
    "Cuéntame más.",
    "¿Seguro?"
];

// 1. CREATE USERS
echo "Creating User Accounts...\n";
$user_ids = [];
$default_pass = password_hash('password123', PASSWORD_DEFAULT);

foreach ($users as $u) {
    // Check exist
    $chk = $conn->prepare("SELECT id_usuario FROM usuarios WHERE nombre_usuario = ?");
    $chk->bind_param("s", $u[0]);
    $chk->execute();
    $res = $chk->get_result();
    
    if ($res->num_rows > 0) {
        $user_ids[] = $res->fetch_assoc()['id_usuario'];
    } else {
        $stmt = $conn->prepare("INSERT INTO usuarios (nombre_usuario, email, contraseña, estado) VALUES (?, ?, ?, 'activo')");
        $stmt->bind_param("sss", $u[0], $u[1], $default_pass);
        $stmt->execute();
        $user_ids[] = $conn->insert_id;
        $stmt->close();
    }
    $chk->close();
}

// 2. CREATE MESSAGES
echo "Creating Messages...\n";
$msg_ids = [];

// Create 50 random messages
for ($i = 0; $i < 50; $i++) {
    $uid = $user_ids[array_rand($user_ids)];
    $content = $messages[array_rand($messages)];
    
    // Vary date slightly (-1 to -30 days)
    $days_ago = rand(0, 30);
    $date = date('Y-m-d H:i:s', strtotime("-$days_ago days" . rand(0, 23) . " hours" . rand(0, 59) . " minutes"));

    $stmt = $conn->prepare("INSERT INTO mensajes (id_usuario, contenido, fecha_creacion, visible) VALUES (?, ?, ?, 1)");
    $stmt->bind_param("iss", $uid, $content, $date);
    $stmt->execute();
    $msg_ids[] = $conn->insert_id;
    $stmt->close();
}

// 3. CREATE ECOS & REACTIONS
echo "Dispensing Ecos & Reactions...\n";

// Fetch ALL messages to ensure older ones get love too
$result = $conn->query("SELECT id_mensaje FROM mensajes");
$all_msg_ids = [];
while ($row = $result->fetch_assoc()) {
    $all_msg_ids[] = $row['id_mensaje'];
}

$reaction_types = ['me_gusta', 'risa', 'triste', 'enfado', 'caca', 'sorpresa', 'rezar', 'calavera', 'corazon'];

foreach ($all_msg_ids as $mid) {
    // Add random Ecos (0 to 8 per message)
    $num_ecos = rand(0, 8);
    for ($j = 0; $j < $num_ecos; $j++) {
        $uid = $user_ids[array_rand($user_ids)];
        $content = $ecos_pool[array_rand($ecos_pool)];
        
        $stmt = $conn->prepare("INSERT INTO ecos (id_mensaje, id_usuario, contenido) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $mid, $uid, $content);
        $stmt->execute();
        $stmt->close();
    }

    // Add random Reactions (5 to 50 per message)
    $num_reactions = rand(5, 50);
    for ($k = 0; $k < $num_reactions; $k++) {
        $rtype = $reaction_types[array_rand($reaction_types)];
        $uid = $user_ids[array_rand($user_ids)]; // Some reactions from registered users

        // Check if reaction exists (since insert ignore is safer or checks)
        // For speed in this seeder, we'll try insert ignore
        $sql = "INSERT IGNORE INTO reacciones (id_mensaje, id_usuario, tipo) VALUES ($mid, $uid, '$rtype')";
        $conn->query($sql);
    }
}

echo "Seeding Complete! Created 15 Users, 50 Messages, Hundreds of Ecos/Reactions.\n";
echo "Running Sync to update counters...\n";
require_once __DIR__ . '/sync_reactions.php';
