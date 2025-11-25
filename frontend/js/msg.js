document.addEventListener("DOMContentLoaded", function () {

    // Cargar mensaje al hacer clic en cualquier parte
    document.addEventListener("click", function () {
        cargarMensajeAleatorio();
    });

    // Cerrar modal al hacer clic fuera
    document.getElementById("mensajeContainer").addEventListener("click", function (e) {
        if (e.target != this) {
            this.classList.add("hidden");
        }
    });

});

function cargarMensajeAleatorio() {

    fetch("/backend/get_random_msg.php")
        .then(response => response.json())
        .then(data => {

            if (data.error) {
                console.error(data.error);
                return;
            }

            // Rellenar modal
            document.querySelector(".nombre").textContent = data.nombre_usuario;
            document.querySelector(".fecha").textContent = data.fecha_creacion;
            document.querySelector(".mensaje-texto").textContent = data.contenido;
            document.querySelector(".avatar").src = data.avatar; // siempre por defecto

            // Reacciones
            const reacciones = ['me_gusta','risa','triste','enfado','caca','sorpresa','rezar','calavera','corazon'];
            reacciones.forEach(r => {
                const el = document.querySelector(`.reaccion.${r}`);
                if(el) el.textContent = `${data[r] || 0} ${el.textContent.replace(/[0-9]+ /,'')}`;
            });

            // Mostrar modal
            document.getElementById("mensajeContainer").classList.remove("hidden");
        })
        .catch(err => {
            console.error("Error en AJAX:", err);
        });
}
