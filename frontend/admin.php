<?php
session_start();
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header("Location: /");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es" style="background-color: #000010;">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel - ShootStars</title>
    <link rel="icon" type="image/png" href="imgs/logo.png">
    <link rel="stylesheet" href="css/main.css">
    <style>
        .admin-container {
            padding: 100px 20px 50px;
            max-width: 1000px;
            margin: 0 auto;
            color: white;
            font-family: sans-serif;
            position: relative;
            z-index: 10;
        }
        .report-card {
            background: rgba(100, 50, 50, 0.6);
            border: 1px solid #ff4444;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .btn-admin {
            padding: 8px 15px;
            border-radius: 5px;
            border: none;
            cursor: pointer;
            color: white;
            font-weight: bold;
            margin-right: 10px;
        }
        .btn-delete { background: #e63946; }
        .btn-ignore { background: #4cc9f0; }
        .btn-block { background: #000; border: 1px solid red; }
    </style>
</head>
<body>
    <header class="main-header">
        <div class="header-content">
            <span class="welcome">Admin: <?php echo htmlspecialchars($_SESSION['username']); ?></span>
            <a href="/" class="btn-nav">Inicio</a>
            <a href="dashboard" class="btn-nav">Mi Panel</a>
            <a href="/backend/auth/logout.php" class="btn-nav">Cerrar Sesión</a>
        </div>
    </header>

    <div class="admin-container">
        <h1>Centro de Moderación</h1>
        <div id="reports-list">
            <p>Cargando mensajes reportados...</p>
        </div>
    </div>
    
    <canvas id="space"></canvas>
    <script src="js/bg.js"></script>

    <script>
        /**
         * Loads reported messages via pagination.
         * 
         * @param {boolean} reset If true, clears list and resets to page 1.
         */
        let currentPage = 1;
        const limit = 5;
        const list = document.getElementById('reports-list');

        function loadReports(reset = false) {
            if (reset) {
                currentPage = 1;
                list.innerHTML = '';
            }

            fetch(`/backend/get_reported_messages.php?page=${currentPage}&limit=${limit}`)
                .then(res => res.json())
                .then(response => {
                    const data = response.data;
                    const pagination = response.pagination;
                    
                    if(reset && data.length === 0) {
                        list.innerHTML = '<p>No hay mensajes reportados.</p>';
                        return;
                    }
                    
                    data.forEach(item => {
                        const div = document.createElement('div');
                        div.className = 'report-card';
                        div.dataset.id = item.id_mensaje;
                        div.innerHTML = `
                            <h3>Reportes: ${item.total_reportes}</h3>
                            <div style="background: rgba(0,0,0,0.3); padding: 10px; margin: 10px 0;">
                                ${item.contenido}
                            </div>
                            <p><strong>Motivos:</strong> ${item.motivos}</p>
                            <p><small>Autor ID: ${item.id_usuario} | Fecha: ${item.fecha_creacion}</small></p>
                            <div class="actions">
                                <button class="btn-admin btn-delete" onclick="adminAction(${item.id_mensaje}, 'delete')">Eliminar Mensaje</button>
                                <button class="btn-admin btn-block" onclick="adminAction(${item.id_mensaje}, 'delete', true)">Eliminar y Bloquear Usuario</button>
                                <button class="btn-admin btn-ignore" onclick="adminAction(${item.id_mensaje}, 'ignore')">Ignorar (Borrar reportes)</button>
                            </div>
                        `;
                        list.appendChild(div);
                    });

                    // Load More Button
                    const oldBtn = document.getElementById('load-more-btn');
                    if (oldBtn) oldBtn.remove();

                    if (currentPage < pagination.total_pages) {
                        const btn = document.createElement('button');
                        btn.id = 'load-more-btn';
                        btn.innerText = 'Cargar más';
                        btn.className = 'btn-admin';
                        btn.style.width = '100%';
                        btn.style.marginTop = '20px';
                        btn.style.background = '#007bff';
                        btn.onclick = () => {
                            currentPage++;
                            loadReports(false);
                        };
                        list.appendChild(btn);
                    }
                });
        }
        
        loadReports(true);

        /**
         * Executes an administrative action.
         * 
         * Actions:
         * - 'delete': Delete message.
         * - 'ignore': Clear reports for message.
         * 
         * @param {number} id Message ID.
         * @param {string} action Action to perform.
         * @param {boolean} block If true, also suspends the user.
         */
        function adminAction(id, action, block = false) {
            if(!confirm("¿Estás seguro?")) return;

            const formData = new FormData();
            formData.append('id_mensaje', id);
            formData.append('action', action);
            if(block) formData.append('block_user', 'true');

            fetch('/backend/admin_actions.php', { method: 'POST', body: formData })
                .then(res => res.json())
                .then(data => {
                    if(data.success) {
                        alert("Acción realizada");
                        document.querySelector(`.report-card[data-id='${id}']`).remove();
                    } else {
                        alert("Error: " + data.error);
                    }
                });
        }
    </script>
</body>
</html>
