document.addEventListener('DOMContentLoaded', initStars);

function initStars() {
    const canvas = document.getElementById('space');
    const ctx = canvas.getContext('2d');
    let stars = [];
    const numStars = 200;

    function resize() {
        canvas.width = window.innerWidth;
        canvas.height = window.innerHeight;
    }
    window.addEventListener('resize', resize);
    resize();

    function createStars() {
        stars = [];
        for (let i = 0; i < numStars; i++) {
            stars.push({
                x: Math.random() * canvas.width,
                y: Math.random() * canvas.height,
                radius: Math.random() * 1.5 + 0.5,
                speed: Math.random() * 0.5 + 0.05,
                alpha: Math.random(), // opacidad inicial
                alphaDir: Math.random() > 0.5 ? 0.01 : -0.01 // dirección de parpadeo
            });
        }
    }
    createStars();

    function animate() {
        ctx.clearRect(0, 0, canvas.width, canvas.height);

        for (let star of stars) {
            ctx.beginPath();
            ctx.arc(star.x, star.y, star.radius, 0, Math.PI * 2);
            ctx.fillStyle = `rgba(255, 255, 255, ${star.alpha})`;
            ctx.fill();

            // mover horizontalmente
            star.x += star.speed;

            // parpadeo
            star.alpha += star.alphaDir;
            if(star.alpha <= 0.1 || star.alpha >= 1){
                star.alphaDir *= -1; // invertir dirección
            }

            // reiniciar si sale por la derecha
            if (star.x > canvas.width) {
                star.x = 0;
                star.y = Math.random() * canvas.height;
            }
        }

        requestAnimationFrame(animate);
    }

    animate();

    // fade-in
    requestAnimationFrame(() => {
        canvas.classList.add('fade-in');
    });
}
