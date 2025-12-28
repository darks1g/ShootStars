<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es" style="background-color: #000010;">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Panel - ShootStars</title>
    <link rel="icon" type="image/png" href="imgs/logo.png">
    <link rel="stylesheet" href="css/main.css">
    <style>
        .dashboard-container {
            padding: 100px 20px 50px;
            max-width: 800px;
            margin: 0 auto;
            color: white;
            font-family: sans-serif;
            position: relative;
            z-index: 10;
        }
        .msg-card {
            background: rgba(80, 70, 110, 0.8);
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            backdrop-filter: blur(5px);
        }
        .msg-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            font-size: 0.9em;
            opacity: 0.8;
        }
        .msg-body {
            font-size: 1.1em;
            margin-bottom: 15px;
            background: rgba(255,255,255,0.1);
            padding: 10px;
            border-radius: 5px;
        }
        .msg-stats {
            display: flex;
            gap: 10px;
            font-size: 0.9em;
        }
        .btn-edit, .btn-delete {
            background: transparent;
            border: 1px solid rgba(255,255,255,0.3);
            color: white;
            padding: 5px 10px;
            border-radius: 5px;
            cursor: pointer;
            margin-right: 5px;
        }
        .btn-delete:hover {
            background: #e63946;
            border-color: #e63946;
        }
        .btn-edit:hover {
             background: #4cc9f0;
             border-color: #4cc9f0;
        }
    </style>
</head>
<body>
    <?php include 'index.php'; /* Hack reuse header? No, index.php has body content. Manual header. */ ?>
    
    <header class="main-header">
        <div class="header-content">
            <span class="welcome">Hola, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
            <a href="index.php" class="btn-nav">Inicio</a>
            <a href="/backend/auth/logout.php" class="btn-nav">Cerrar Sesión</a>
        </div>
    </header>

    <div class="dashboard-container">
        <h1>Mis Mensajes</h1>
        <div id="messages-list">
            <p>Cargando mensajes...</p>
        </div>
    </div>

    <canvas id="space"></canvas>
    <script src="js/bg.js"></script>
    <script>
        fetch('/backend/get_user_messages.php')
            .then(res => res.json())
            .then(data => {
                const list = document.getElementById('messages-list');
                list.innerHTML = '';
                
                if (data.length === 0) {
                    list.innerHTML = '<p>No tienes mensajes aún.</p>';
                    return;
                }

                data.forEach(msg => {
                    const div = document.createElement('div');
                    div.className = 'msg-card';
                    div.dataset.id = msg.id_mensaje;
                    div.innerHTML = `
                        <div class="msg-header">
                            <span>${msg.fecha_creacion}</span>
                            <span>${msg.visible == 1 ? 'Visible' : 'Oculto'}</span>
                        </div>
                        <div class="msg-body" contenteditable="false">
                            ${msg.contenido}
                        </div>
                        <div class="msg-stats">
                            <span>👍 ${msg.me_gusta}</span>
                            <span>😂 ${msg.risa}</span>
                            <span>❤️ ${msg.corazon}</span>
                        </div>
                        <div class="msg-actions" style="margin-top: 10px;">
                            <button class="btn-edit" onclick="toggleEdit(${msg.id_mensaje}, this)">Editar</button>
                            <button class="btn-delete" onclick="deleteMsg(${msg.id_mensaje})">Eliminar</button>
                            <button class="btn-save hidden" onclick="saveMsg(${msg.id_mensaje}, this)" style="background: #4cc9f0; color: white; border:none; padding: 5px 10px; border-radius: 5px;">Guardar</button>
                        </div>
                    `;
                    list.appendChild(div);
                });
            })
            .catch(err => console.error(err));

        function deleteMsg(id) {
            if(!confirm("¿Seguro que quieres eliminar este mensaje?")) return;
            
            const formData = new FormData();
            formData.append('id_mensaje', id);
            
            fetch('/backend/delete_message.php', { method: 'POST', body: formData })
                .then(res => res.json())
                .then(data => {
                    if(data.success) {
                        document.querySelector(`.msg-card[data-id='${id}']`).remove();
                    } else {
                        alert(data.error);
                    }
                });
        }

        function toggleEdit(id, btn) {
            const card = document.querySelector(`.msg-card[data-id='${id}']`);
            const body = card.querySelector('.msg-body');
            const saveBtn = card.querySelector('.btn-save');
            
            if (body.isContentEditable) {
                // Cancel edit
                body.contentEditable = "false";
                body.style.background = "rgba(255,255,255,0.1)";
                body.style.color = "white"; // Reset color
                btn.textContent = "Editar";
                saveBtn.classList.add("hidden");
            } else {
                // Start edit
                body.contentEditable = "true";
                body.style.background = "white";
                body.style.color = "black";
                body.focus();
                btn.textContent = "Cancelar";
                saveBtn.classList.remove("hidden");
            }
        }

        function saveMsg(id, btn) {
            const card = document.querySelector(`.msg-card[data-id='${id}']`);
            const body = card.querySelector('.msg-body');
            const content = body.innerText;
            
            const formData = new FormData();
            formData.append('id_mensaje', id);
            formData.append('contenido', content);
            
            fetch('/backend/edit_message.php', { method: 'POST', body: formData })
                .then(res => res.json())
                .then(data => {
                    if(data.success) {
                        alert("Mensaje actualizado");
                        // Reset view
                        const editBtn = card.querySelector('.btn-edit');
                        toggleEdit(id, editBtn); 
                    } else {
                        alert(data.error);
                    }
                });
        }
    </script>
</body>
</html>
