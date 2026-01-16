document.addEventListener("DOMContentLoaded", () => {

    let modalAbierto = false;
    const modal = document.getElementById("mensajeContainer");
    const modalBox = document.querySelector(".mensaje-box");

    // Clic global
    document.addEventListener("click", (e) => {

        if (!modalAbierto) {
            modalAbierto = true;
            document.getElementById("heroOverlay").classList.add("hidden"); // Hide hero
            cargarMensajeAleatorio();
            return;
        }

        if (modalBox.contains(e.target) || e.target.closest('.header-content')) {
            return;
        }

        cerrarModal();
    });

    // Botón de cerrar específico
    const closeBtn = document.getElementById("closeMsgBtn");
    if (closeBtn) {
        closeBtn.addEventListener("click", (e) => {
            e.stopPropagation(); // Evitar que el clic llegue al document y reabra
            cerrarModal();
        });
    }

    function cerrarModal() {
        modal.classList.add("hidden");
        document.getElementById("heroOverlay").classList.remove("hidden"); // Show hero
        modalAbierto = false;
    }

});


// =====================================================
// AJAX
// =====================================================
function cargarMensajeAleatorio() {

    fetch("/backend/get_random_msg.php")
        .then(response => response.json())
        .then(data => {

            if (data.error) return console.error(data.error);

            // Texto
            document.querySelector(".nombre").textContent = data.nombre_usuario;
            document.querySelector(".fecha").textContent = data.fecha_creacion;
            document.querySelector(".mensaje-texto").textContent = data.contenido;

            // Avatar con fallback
            const avatar = document.querySelector(".avatar");

            avatar.src = (data.avatar && data.avatar.trim() !== "")
                ? data.avatar
                : "imgs/default-pfp.jpg";

            avatar.onerror = () => {
                avatar.src = "imgs/default-pfp.jpg";
            };

            // Reacciones
            const reacciones = ['me_gusta', 'risa', 'triste', 'enfado', 'caca', 'sorpresa', 'rezar', 'calavera', 'corazon'];
            reacciones.forEach(r => {
                const el = document.querySelector(`.reaccion.${r}`);
                if (el) {
                    const original = el.textContent.replace(/[0-9]+ /, '');
                    el.textContent = `${data[r] || 0} ${original}`;

                    // Clone to remove old listeners (simple way)
                    const newBtn = el.cloneNode(true);
                    el.parentNode.replaceChild(newBtn, el);

                    newBtn.addEventListener("click", (e) => {
                        e.stopPropagation();
                        enviarReaccion(r, data.id_mensaje, newBtn, original);
                    });
                }
            });

            // Ecos - Cargar ecos del mensaje
            cargarEcos(data.id_mensaje);

            // Report button
            const btnReport = document.querySelector(".report-btn");
            const newBtnReport = btnReport.cloneNode(true);
            btnReport.parentNode.replaceChild(newBtnReport, btnReport);

            newBtnReport.addEventListener("click", (e) => {
                e.stopPropagation();

                if (!window.isLoggedIn) {
                    alert("Debes iniciar sesión para reportar mensajes.");
                    return;
                }

                if (confirm("¿Quieres reportar este mensaje?")) {
                    reportarMensaje(data.id_mensaje);
                }
            });

            // Botón de enviar eco
            const btnEco = document.getElementById("sendEcoBtn");
            const inputEco = document.getElementById("ecoInput");
            if (btnEco && inputEco) {
                const newBtnEco = btnEco.cloneNode(true);
                btnEco.parentNode.replaceChild(newBtnEco, btnEco);
                newBtnEco.addEventListener("click", (e) => {
                    e.stopPropagation();
                    enviarEco(data.id_mensaje);
                });

                // Enviar con Enter
                inputEco.addEventListener("keydown", (e) => {
                    if (e.key === "Enter") {
                        e.preventDefault();
                        enviarEco(data.id_mensaje);
                    }
                });
            }

            // Mostrar modal
            document.getElementById("mensajeContainer").classList.remove("hidden");
        })
        .catch(err => console.error("Error en AJAX:", err));
}

let currentEcoOffset = 0;
const ecoLimit = 5;

function cargarEcos(idMensaje, append = false) {
    if (!append) {
        currentEcoOffset = 0;
        const container = document.getElementById("ecosContainer");
        container.innerHTML = '<p style="text-align:center; opacity:0.5;">Sintonizando ecos...</p>';
    }

    fetch(`/backend/get_ecos.php?id_mensaje=${idMensaje}&limit=${ecoLimit}&offset=${currentEcoOffset}`)
        .then(res => res.json())
        .then(data => {
            const container = document.getElementById("ecosContainer");
            const ecos = data.ecos;
            const total = data.total;

            if (!append) container.innerHTML = "";

            if (ecos.length === 0 && !append) {
                container.innerHTML = '<p style="text-align:center; opacity:0.3; font-style:italic;">Silencio absoluto. Sé el primer eco.</p>';
                return;
            }

            // Remove existing load more button if any
            const existingBtn = document.getElementById("btnLoadMoreEcos");
            if (existingBtn) existingBtn.remove();

            ecos.forEach(eco => {
                const div = document.createElement("div");
                div.className = "eco-item";
                div.innerHTML = `
                    <div class="eco-header">
                        <span class="eco-date">${eco.fecha_creacion}</span>
                        <button class="eco-report-btn" title="Reportar eco">⚠</button>
                    </div>
                    <p class="eco-content">${escapeHTML(eco.contenido)}</p>
                `;

                // Evento reporte eco
                const btnReportEco = div.querySelector(".eco-report-btn");
                btnReportEco.addEventListener("click", (e) => {
                    e.stopPropagation();
                    if (!window.isLoggedIn) {
                        alert("Debes iniciar sesión para reportar.");
                        return;
                    }
                    if (confirm("¿Quieres reportar este eco?")) {
                        reportarEco(eco.id_eco);
                    }
                });

                container.appendChild(div);
            });

            currentEcoOffset += ecos.length;

            if (currentEcoOffset < total) {
                const btnLoadMore = document.createElement("button");
                btnLoadMore.id = "btnLoadMoreEcos";
                btnLoadMore.className = "btn-load-more-ecos";
                btnLoadMore.textContent = `Cargar más ecos (${total - currentEcoOffset} restantes)`;
                btnLoadMore.onclick = (e) => {
                    e.stopPropagation();
                    cargarEcos(idMensaje, true);
                };
                container.appendChild(btnLoadMore);
            }

            if (!append) container.scrollTop = 0;
        })
        .catch(err => console.error("Error al cargar ecos:", err));
}

function enviarEco(idMensaje) {
    const input = document.getElementById("ecoInput");
    const contenido = input.value.trim();

    if (!contenido) return;

    const formData = new FormData();
    formData.append("id_mensaje", idMensaje);
    formData.append("contenido", contenido);

    fetch("/backend/create_eco.php", {
        method: "POST",
        body: formData
    })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                input.value = "";
                cargarEcos(idMensaje);
            } else {
                alert(data.error || "Error al enviar el eco");
            }
        })
        .catch(err => {
            console.error("Fetch error:", err);
            alert("Error de red: " + err.message);
        });
}

function escapeHTML(str) {
    const p = document.createElement("p");
    p.textContent = str;
    return p.innerHTML;
}

function enviarReaccion(tipo, idMensaje, btn, icon) {
    const formData = new FormData();
    formData.append("id_mensaje", idMensaje);
    formData.append("tipo", tipo);

    fetch("/backend/reaction.php", {
        method: "POST",
        body: formData
    })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                btn.textContent = `${data.nuevos_votos} ${icon}`;
            } else {
                console.error("Error reaction:", data.error);
            }
        })
        .catch(err => console.error(err));
}

function reportarEco(idEco) {
    const motivo = prompt("Motivo del reporte (opcional):");

    const formData = new FormData();
    formData.append("id_eco", idEco);
    formData.append("motivo", motivo || "");

    fetch("/backend/report.php", {
        method: "POST",
        body: formData
    })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                alert("Reporte de eco enviado.");
            } else {
                alert("Error: " + (data.error || "Desconocido"));
            }
        })
        .catch(err => console.error(err));
}

function reportarMensaje(idMensaje) {
    const motivo = prompt("Motivo del reporte (opcional):");

    const formData = new FormData();
    formData.append("id_mensaje", idMensaje);
    formData.append("motivo", motivo || "");

    fetch("/backend/report.php", {
        method: "POST",
        body: formData
    })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                alert("Reporte enviado correctamente.");
            } else {
                alert("Error: " + (data.error || "Desconocido"));
            }
        })
        .catch(err => console.error(err));
}
