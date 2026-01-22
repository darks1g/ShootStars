<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es" style="background-color: #000010;">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Panel de usuario de ShootStars. Administra tus mensajes, visualiza reacciones y personaliza tu perfil.">
    <meta property="og:image" content="https://shootstars.sytes.net/imgs/logo.png">
    <title>Mi Panel - ShootStars</title>
    <link rel="icon" type="image/png" href="imgs/logo.png">
    <link rel="stylesheet" href="css/main.css">
</head>
<body>
    <!-- Header is defined below, do not include index.php which is a full page -->
    
    <header class="main-header">
        <div class="header-content">
            <!-- Avatar Upload Wrapper -->
            <div class="user-avatar-container" style="position: relative; display: inline-block; cursor: pointer; margin-right: 15px; vertical-align: middle;">
                <?php 
                    // Fetch current avatar from DB directly or session if updated
                    $avatarPath = isset($_SESSION['avatar']) && !empty($_SESSION['avatar']) ? $_SESSION['avatar'] : 'imgs/default-pfp.jpg';
                ?>
                <img src="<?php echo htmlspecialchars($avatarPath); ?>" id="current-avatar" alt="Avatar" style="width: 40px; height: 40px; border-radius: 50%; border: 2px solid #4cc9f0; object-fit: cover;">
                <div class="avatar-overlay" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); border-radius: 50%; display: flex; align-items: center; justify-content: center; opacity: 0; transition: opacity 0.3s; color: white; font-size: 10px; text-align: center;">
                    📷
                </div>
                <input type="file" id="avatar-input" style="display: none;" accept="image/jpeg, image/png">
            </div>

            <span class="welcome">Hola, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
            <a href="/" class="btn-nav">Inicio</a>
            <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1): ?>
                <a href="admin" class="btn-nav">Admin Panel</a>
            <?php endif; ?>
            <a href="/backend/auth/logout.php" class="btn-nav">Cerrar Sesión</a>
            
            <button onclick="deleteAccount()" style="background: transparent; border: 1px solid #e63946; color: #e63946; padding: 5px 10px; border-radius: 5px; cursor: pointer; margin-left: 10px; font-size: 0.8em;">
                Eliminar Cuenta
            </button>
        </div>
    </header>

    <div class="dashboard-container">
        <!-- New Message Form -->
        <div class="msg-card">
            <h3>Publicar nuevo mensaje</h3>
            <div class="msg-body" contenteditable="true" id="new-msg-content" style="background: white; color: black; min-height: 60px;" data-placeholder="Escribe tu mensaje aquí..."></div>
            <button class="btn-edit" onclick="createMsg()" style="background: #4cc9f0; border-color: #4cc9f0; margin-top: 10px;">Publicar al Universo</button>
        </div>

        <h1>Mis Mensajes</h1>
        <div id="messages-list">
            <p>Cargando mensajes...</p>
        </div>
    </div>

    <canvas id="space"></canvas>
    <script src="js/bg.js"></script>
    <script>
        let currentPage = 1;
        const limit = 5; // Low limit to test pagination easily
        const list = document.getElementById('messages-list');
        let isLoading = false;

        /**
         * Loads messages for the current user with pagination.
         * Appends new messages to the list unless reset is true.
         * 
         * @param {boolean} reset If true, clears the list and starts from page 1.
         */
        function loadMessages(reset = false) {
            if (isLoading) return;
            isLoading = true;

            if (reset) {
                currentPage = 1;
                list.innerHTML = '';
            }

            fetch(`/backend/get_user_messages.php?page=${currentPage}&limit=${limit}`)
                .then(res => res.json())
                .then(response => {
                    const data = response.data;
                    const pagination = response.pagination;

                    if (reset && data.length === 0) {
                        list.innerHTML = '<p>No tienes mensajes aún.</p>';
                        isLoading = false;
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
                                <span>😢 ${msg.triste}</span>
                                <span>😡 ${msg.enfado}</span>
                                <span>🤢 ${msg.caca}</span>
                                <span>😱 ${msg.sorpresa}</span>
                                <span>🙏 ${msg.rezar}</span>
                                <span>💀 ${msg.calavera}</span>
                                <span>❤️ ${msg.corazon}</span>
                            </div>
                            <div class="msg-actions" style="margin-top: 10px;">
                                <button class="btn-edit" onclick="toggleEdit(${msg.id_mensaje}, this)">Editar</button>
                                <button class="btn-delete" onclick="deleteMsg(${msg.id_mensaje})">Eliminar</button>
                                <button class="btn-save hidden" onclick="saveMsg(${msg.id_mensaje}, this)" style="background: #4cc9f0; color: white; border:none; padding: 5px 10px; border-radius: 5px;">Guardar</button>
                                <button class="btn-eco-dashboard" onclick="toggleEcos(${msg.id_mensaje}, this)">Ver Ecos</button>
                            </div>
                            <div id="ecos-container-${msg.id_mensaje}" class="ecos-dashboard-list hidden" style="margin-top: 15px; padding-top: 15px; border-top: 1px solid rgba(255,255,255,0.1);"></div>
                        `;
                        list.appendChild(div);
                    });

                    // Remove old button if exists
                    const oldBtn = document.getElementById('load-more-btn');
                    if (oldBtn) oldBtn.remove();

                    // Check if more pages exist
                    if (currentPage < pagination.total_pages) {
                        const btn = document.createElement('button');
                        btn.id = 'load-more-btn';
                        btn.innerText = 'Cargar más mensajes';
                        btn.className = 'btn-admin'; // Estilo unificado
                        btn.style.width = '100%';
                        btn.style.marginTop = '20px';
                        btn.onclick = () => {
                            currentPage++;
                            loadMessages(false);
                        };
                        list.appendChild(btn);
                    }
                    
                    isLoading = false;
                })
                .catch(err => {
                    console.error(err);
                    isLoading = false;
                });
        }
        
        // Initial Load
        loadMessages(true);

        /**
         * Creates a new message.
         * Sends POST request to backend and reloads page on success.
         */
        function createMsg() {
            const input = document.getElementById('new-msg-content');
            const content = input.innerText.trim();
            
            if (!content) {
                alert("Por favor escribe un mensaje");
                return;
            }

            const formData = new FormData();
            formData.append('contenido', content);

            fetch('/backend/create_message.php', { method: 'POST', body: formData })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        alert("¡Mensaje enviado al espacio!");
                        input.innerText = ''; // Clear input
                        // Reload messages
                        location.reload(); 
                    } else {
                        alert(data.error);
                    }
                })
                .catch(err => console.error(err));
        }

        /**
         * Deletes a message by ID.
         * Requires user confirmation.
         * @param {number} id Message ID
         */
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

        /**
         * Toggles edit mode for a message card.
         * @param {number} id Message ID
         * @param {HTMLElement} btn The button element clicked
         */
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

        /**
         * Saves changes to an edited message.
         * @param {number} id Message ID
         * @param {HTMLElement} btn Save button element
         */
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

        // --- Avatar Upload Logic ---
        const avatarContainer = document.querySelector('.user-avatar-container');
        const avatarInput = document.getElementById('avatar-input');
        const currentAvatar = document.getElementById('current-avatar');
        
        avatarContainer.addEventListener('mouseenter', () => {
            document.querySelector('.avatar-overlay').style.opacity = '1';
        });
        
        avatarContainer.addEventListener('mouseleave', () => {
            document.querySelector('.avatar-overlay').style.opacity = '0';
        });

        avatarContainer.addEventListener('click', () => {
            avatarInput.click();
        });

        avatarInput.addEventListener('change', () => {
            const file = avatarInput.files[0];
            if (!file) return;

            const formData = new FormData();
            formData.append('avatar', file);

            // Optional: Show loading state
            currentAvatar.style.opacity = '0.5';

            fetch('/backend/upload_avatar.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                currentAvatar.style.opacity = '1';
                if (data.success) {
                    // Update image source with timestamp to force reload
                    currentAvatar.src = data.avatar + '?t=' + new Date().getTime();
                } else {
                    alert("Error: " + data.error);
                }
            })
            .catch(err => {
                console.error(err);
                currentAvatar.style.opacity = '1';
                alert("Error al subir la imagen");
            });
        });

        /**
         * Deletes user account.
         * Double confirmation required.
         */
        function deleteAccount() {
            if (confirm("⚠️ ¿ADVERTENCIA: Estás seguro de que quieres eliminar tu cuenta?\n\nEsta acción es irreversible y borrará todos tus mensajes.")) {
                if (confirm("¿De verdad? Es tu última oportunidad.")) {
                    fetch('/backend/auth/delete_account.php', { method: 'POST' })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                alert("Cuenta eliminada. Hasta siempre, vaquero espacial.");
                                window.location.href = "/";
                            } else {
                                alert("Error: " + data.error);
                            }
                        });
                }
            }
        }
        

        /**
         * Toggles the visibility of Ecos for a specific message.
         * Fetches them from backend if not already loaded.
         * @param {number} id Message ID
         * @param {HTMLElement} btn The toggle button
         */
        function toggleEcos(id, btn) {
            const container = document.getElementById(`ecos-container-${id}`);
            
            if (!container.classList.contains('hidden')) {
                // Hide
                container.classList.add('hidden');
                btn.textContent = "Ver Ecos";
                return;
            }

            // Show
            container.classList.remove('hidden');
            btn.textContent = "Ocultar";

            // Check if already loaded using dataset
            if (container.dataset.loaded === "true") return;

            container.innerHTML = '<p style="font-size: 0.9em; opacity: 0.7;">Cargando ecos...</p>';

            fetch(`/backend/get_ecos.php?id_mensaje=${id}&limit=50`) // Get a good amount
                .then(res => res.json())
                .then(data => {
                    container.innerHTML = ''; // Clear loading text
                    container.dataset.loaded = "true"; // Mark as loaded

                    if (data.ecos && data.ecos.length > 0) {
                        data.ecos.forEach(eco => {
                            const ecoDiv = document.createElement('div');
                            ecoDiv.className = 'dashboard-eco-item';
                            // Styles moved to main.css
                            
                            ecoDiv.innerHTML = `
                                <div class="eco-content-footer">
                                    <span style="color: #4cc9f0;">${eco.fecha_creacion}</span>
                                </div>
                                <div style="margin-top: 5px;">${eco.contenido}</div>
                            `;
                            container.appendChild(ecoDiv);
                        });
                    } else {
                        container.innerHTML = '<p style="font-size: 0.9em; opacity: 0.7; font-style: italic;">No hay ecos en este mensaje.</p>';
                    }
                })
                .catch(err => {
                    console.error(err);
                    container.innerHTML = '<p style="color: #ff4444;">Error al cargar ecos.</p>';
                });
        }
    </script>
</body>
</html>
