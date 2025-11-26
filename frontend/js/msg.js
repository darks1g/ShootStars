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

        if (modalBox.contains(e.target)) {
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
            const reacciones = ['me_gusta','risa','triste','enfado','caca','sorpresa','rezar','calavera','corazon'];
            reacciones.forEach(r => {
                const el = document.querySelector(`.reaccion.${r}`);
                if (el) {
                    const original = el.textContent.replace(/[0-9]+ /, '');
                    el.textContent = `${data[r] || 0} ${original}`;
                }
            });

            // Mostrar modal
            document.getElementById("mensajeContainer").classList.remove("hidden");
        })
        .catch(err => console.error("Error en AJAX:", err));
}
