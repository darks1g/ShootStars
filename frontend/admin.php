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

            fetch(`/backend/get_reported_items.php?page=${currentPage}&limit=${limit}`)
                .then(res => res.json())
                .then(response => {
                    const data = response.data;
                    const pagination = response.pagination;
                    
                    if(reset && data.length === 0) {
                        list.innerHTML = '<p>No hay items reportados.</p>';
                        return;
                    }
                    
                    data.forEach(item => {
                        const div = document.createElement('div');
                        div.className = 'report-card';
                        div.dataset.id = item.id;
                        div.dataset.tipo = item.tipo;
                        
                        // Bordes de colores para diferenciar tipos
                        if (item.tipo === 'eco') {
                            div.style.borderLeft = "6px solid #7209b7";
                            div.style.background = "rgba(114, 9, 183, 0.05)";
                        } else {
                            div.style.borderLeft = "6px solid #e63946";
                            div.style.background = "rgba(230, 57, 70, 0.05)";
                        }

                        // Parse motivos to list
                        const motivosList = item.motivos ? item.motivos.split(' || ').map(m => `<li>${m || 'Sin motivo especificado'}</li>`).join('') : '<li>Sin motivos</li>';

                        div.innerHTML = `
                            <div style="display:flex; justify-content:space-between; align-items:center;">
                                <h3 style="color: #ff4444;">${item.tipo.toUpperCase()}: ${item.total_reportes} reportes</h3>
                                <span style="font-size:0.8rem; opacity:0.6;">ID: ${item.id}</span>
                            </div>
                            <div style="background: rgba(0,0,0,0.5); padding: 15px; margin: 10px 0; border-radius: 8px; border: 1px solid rgba(255,255,255,0.1); font-style: italic;">
                                "${item.contenido}"
                            </div>
                            <div style="margin-top: 10px;">
                                <strong>Motivos recibidos:</strong>
                                <ul style="margin-top: 5px; padding-left: 20px; font-size: 0.9rem; opacity: 0.9;">
                                    ${motivosList}
                                </ul>
                            </div>
                            <p style="margin-top: 15px;"><small>Autor ID: ${item.id_usuario} | Fecha: ${item.fecha_creacion}</small></p>
                            <div class="actions" style="margin-top: 20px; display: flex; gap: 10px; flex-wrap: wrap;">
                                <button class="btn-admin btn-delete" onclick="adminAction(${item.id}, 'delete', '${item.tipo}')">Eliminar ${item.tipo}</button>
                                <button class="btn-admin btn-block" onclick="adminAction(${item.id}, 'delete', '${item.tipo}', true)">Eliminar y Bloquear</button>
                                <button class="btn-admin btn-ignore" onclick="adminAction(${item.id}, 'ignore', '${item.tipo}')">Ignorar Reportes</button>
                            </div>
                        `;
                        list.appendChild(div);
                    });

                    // Pagination Controls
                    const oldPaginator = document.getElementById('admin-paginator');
                    if (oldPaginator) oldPaginator.remove();

                    if (pagination.total_pages > 1) {
                        const paginator = document.createElement('div');
                        paginator.id = 'admin-paginator';
                        paginator.style.marginTop = '30px';
                        paginator.style.display = 'flex';
                        paginator.style.justifyContent = 'center';
                        paginator.style.gap = '10px';
                        paginator.style.alignItems = 'center';

                        const prevBtn = document.createElement('button');
                        prevBtn.className = 'btn-admin';
                        prevBtn.innerText = '←';
                        prevBtn.disabled = currentPage === 1;
                        prevBtn.style.opacity = prevBtn.disabled ? '0.3' : '1';
                        prevBtn.onclick = () => { if(currentPage > 1) { currentPage--; loadReports(true); window.scrollTo(0,0); } };

                        const info = document.createElement('span');
                        info.innerText = `Página ${currentPage} de ${pagination.total_pages} (${pagination.total_items} items)`;

                        const nextBtn = document.createElement('button');
                        nextBtn.className = 'btn-admin';
                        nextBtn.innerText = '→';
                        nextBtn.disabled = currentPage === pagination.total_pages;
                        nextBtn.style.opacity = nextBtn.disabled ? '0.3' : '1';
                        nextBtn.onclick = () => { if(currentPage < pagination.total_pages) { currentPage++; loadReports(true); window.scrollTo(0,0); } };

                        paginator.appendChild(prevBtn);
                        paginator.appendChild(info);
                        paginator.appendChild(nextBtn);
                        list.parentNode.appendChild(paginator);
                    }
                });
        }
        
        loadReports(true);

        function adminAction(id, action, tipo, block = false) {
            if(!confirm(`¿Estás seguro de que quieres ${action === 'delete' ? 'eliminar' : 'ignorar'} este ${tipo}?`)) return;

            const formData = new FormData();
            formData.append('id', id);
            formData.append('tipo', tipo);
            formData.append('action', action);
            if(block) formData.append('block_user', 'true');

            fetch('/backend/admin_actions.php', { method: 'POST', body: formData })
                .then(res => res.json())
                .then(data => {
                    if(data.success) {
                        alert("Acción realizada con éxito");
                        document.querySelector(`.report-card[data-id='${id}'][data-tipo='${tipo}']`).remove();
                    } else {
                        alert("Error: " + data.error);
                    }
                });
        }
    </script>
</body>
</html>
