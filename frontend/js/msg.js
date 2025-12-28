document.addEventListener("DOMContentLoaded", () => {

    let modalAbierto = false;
    const modal = document.getElementById("mensajeContainer");
    const modalBox = document.querySelector(".mensaje-box");

    // Clic global
    document.addEventListener("click", (e) => {

        if (!modalAbierto) {
            modalAbierto = true;
            cargarMensajeAleatorio();
            return;
        }

        if (modalBox.contains(e.target) || e.target.closest('.header-content')) {
            return;
        }

        cerrarModal();
    });

    function cerrarModal() {
        modal.classList.add("hidden");
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

            // Report button
            const btnReport = document.querySelector(".report-btn");
            const newBtnReport = btnReport.cloneNode(true);
            btnReport.parentNode.replaceChild(newBtnReport, btnReport);

            newBtnReport.addEventListener("click", (e) => {
                e.stopPropagation();
                if (confirm("¿Quieres reportar este mensaje? Necesitas iniciar sesión.")) {
                    reportarMensaje(data.id_mensaje);
                }
            });

            // Mostrar modal
            document.getElementById("mensajeContainer").classList.remove("hidden");
        })
        .catch(err => console.error("Error en AJAX:", err));
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
