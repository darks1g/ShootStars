<!DOCTYPE html>
<html lang="es" style="background-color: #000010;">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - ShootStars</title>
    <link rel="icon" type="image/png" href="imgs/logo.png">
    <link rel="stylesheet" href="css/main.css">
</head>
<body>
    <div class="auth-container">
        <h1 class="logo-big">ShootStars</h1>
        <div class="auth-box glass-panel">
            <h2>Crear Cuenta</h2>
            <form action="/backend/auth/register.php" method="POST">
                
                <div class="form-group">
                    <label for="username">Nombre de Usuario</label>
                    <input type="text" id="username" name="username" required>
                    <small id="msg-user" style="font-size: 0.8em; display: block; margin-top: 5px; min-height: 1.2em;"></small>
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>
                    <small id="msg-email" style="font-size: 0.8em; display: block; margin-top: 5px; min-height: 1.2em;"></small>
                </div>

                <div class="form-group">
                    <label for="password">Contraseña</label>
                    <input type="password" id="password" name="password" required>
                    <small id="msg-pass-strength" style="font-size: 0.8em; display: block; margin-top: 5px;">
                        Mínimo 8 caracteres, mayúsculas, minúsculas, números y símbolos.
                    </small>
                </div>

                <div class="form-group">
                    <label for="password_confirm">Confirmar Contraseña</label>
                    <input type="password" id="password_confirm" name="password_confirm" required>
                    <small id="msg-pass-match" style="font-size: 0.8em; display: block; margin-top: 5px; min-height: 1.2em;"></small>
                </div>

                <?php if (isset($_GET['error'])): ?>
                    <p class="error-msg"><?php echo htmlspecialchars($_GET['error']); ?></p>
                <?php endif; ?>

                <button type="submit" class="btn-primary" id="btn-submit" disabled style="opacity: 0.5;">Registrarse</button>
            </form>
            <script>
                const userInput = document.getElementById("username");
                const emailInput = document.getElementById("email");
                const passInput = document.getElementById("password");
                const passConfirm = document.getElementById("password_confirm");
                const btnSubmit = document.getElementById("btn-submit");

                const msgUser = document.getElementById("msg-user");
                const msgEmail = document.getElementById("msg-email");
                const msgStrength = document.getElementById("msg-pass-strength");
                const msgMatch = document.getElementById("msg-pass-match");

                let userValid = false;
                let emailValid = false;
                let passStrong = false;
                let passMatch = false;

                function updateBtn() {
                    if (userValid && emailValid && passStrong && passMatch) {
                        btnSubmit.disabled = false;
                        btnSubmit.style.opacity = "1";
                    } else {
                        btnSubmit.disabled = true;
                        btnSubmit.style.opacity = "0.5";
                    }
                }

                // AJAX Check User
                userInput.addEventListener("blur", () => {
                    const val = userInput.value.trim();
                    if(val.length < 3) return;
                    
                    checkBackend('username', val, msgUser, (exists) => {
                        if (exists) {
                            setStatus(msgUser, "✖ El usuario ya existe", false);
                            userValid = false;
                        } else {
                            setStatus(msgUser, "✔ Usuario disponible", true);
                            userValid = true;
                        }
                        updateBtn();
                    });
                });

                // AJAX Check Email
                emailInput.addEventListener("blur", () => {
                    const val = emailInput.value.trim();
                    if(!val.includes('@')) return;

                    checkBackend('email', val, msgEmail, (exists) => {
                        if (exists) {
                            setStatus(msgEmail, "✖ El email ya está registrado", false);
                            emailValid = false;
                        } else {
                            setStatus(msgEmail, "✔ Email disponible", true);
                            emailValid = true;
                        }
                        updateBtn();
                    });
                });

                function checkBackend(field, value, msgEl, callback) {
                    msgEl.style.color = "white";
                    msgEl.textContent = "Comprobando...";
                    
                    const formData = new FormData();
                    formData.append('field', field);
                    formData.append('value', value);

                    fetch('/backend/auth/check_user.php', { method: 'POST', body: formData })
                        .then(res => res.json())
                        .then(data => {
                            callback(data.exists);
                        })
                        .catch(err => console.error(err));
                }

                function setStatus(el, text, isGood) {
                    el.textContent = text;
                    el.style.color = isGood ? "#4cc9f0" : "#e63946";
                }

                // Password Regex
                // Min 8 chars, 1 Upper, 1 Lower, 1 Number, 1 Special
                const strongRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/;

                passInput.addEventListener("keyup", () => {
                    const val = passInput.value;
                    if (strongRegex.test(val)) {
                        setStatus(msgStrength, "✔ Contraseña fuerte", true);
                        passStrong = true;
                    } else {
                        setStatus(msgStrength, "✖ Debe tener: 8+, Mayús, Minús, Núm, Símbolo", false);
                        passStrong = false;
                    }
                    checkMatch();
                });

                passConfirm.addEventListener("keyup", checkMatch);

                function checkMatch() {
                    if (passConfirm.value === "") {
                        msgMatch.textContent = "";
                        passMatch = false;
                    } else if (passInput.value === passConfirm.value) {
                        setStatus(msgMatch, "✔ Las contraseñas coinciden", true);
                        passMatch = true;
                    } else {
                        setStatus(msgMatch, "✖ No coinciden", false);
                        passMatch = false;
                    }
                    updateBtn();
                }

            </script>
            <p>¿Ya tienes cuenta? <a href="login">Inicia Sesión</a></p>
            <p><a href="/">Volver al inicio</a></p>
        </div>
    </div>
    <canvas id="space"></canvas>
    <script src="js/bg.js"></script>
</body>
</html>
