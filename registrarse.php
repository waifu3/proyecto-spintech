<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Usuario - Spintech</title>
    <link rel="shortcut icon" href="./images/Spintech.png" type="image/x-icon">
    <link rel="stylesheet" href="./css/registrarse.css">
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <a href="#main-content" class="skip-link">Saltar al contenido principal</a>
    <div class="accessibility-controls">
        <button id="contrast-toggle" aria-label="Alternar contraste">Alternar contraste</button>
        <button id="font-size-toggle" aria-label="Cambiar tamaño de fuente">Cambiar tamaño de fuente</button>
    </div>

    <main id="main-content" class="main">
        <div class="login-container">
            <div class="logo">
                <i data-lucide="book-open" class="logo-icon"></i>
                <h1>Spintech</h1>
            </div>

            <h2>Registro de Usuario</h2>

            <form action="InicioSesion/registrarse.php" method="POST">
                <div class="form-group">
                    <label for="username">Nombre de usuario</label>
                    <div class="input-icon">
                        <i data-lucide="user" aria-hidden="true"></i>
                        <input type="text" id="username" name="username" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="password">Contraseña</label>
                    <div class="input-icon">
                        <i data-lucide="lock" aria-hidden="true"></i>
                        <input type="password" id="password" name="password" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="role_id">Rol</label>
                    <div class="input-icon">
                        <i data-lucide="users" aria-hidden="true"></i>
                        <select id="role_id" name="role_id" required>
                            <option value="">Seleccione un rol</option>
                            <option value="1">Admin</option>
                            <option value="2">Usuario</option>
                            <option value="3">Profesor</option>
                        </select>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Registrar</button>
            </form>
            <p class="signup-link">¿Ya tienes cuenta? <a href="login.php">Iniciar sesión</a></p>
        </div>
    </main>

    <script>
        const contrastToggle = document.getElementById('contrast-toggle');
        const fontSizeToggle = document.getElementById('font-size-toggle');
        const body = document.body;

        contrastToggle.addEventListener('click', () => {
            body.classList.toggle('high-contrast');
            const isHighContrast = body.classList.contains('high-contrast');
            localStorage.setItem('highContrast', isHighContrast);
        });

        fontSizeToggle.addEventListener('click', () => {
            body.classList.toggle('large-text');
            const isLargeText = body.classList.contains('large-text');
            localStorage.setItem('largeText', isLargeText);
        });

        // Restaurar preferencias del usuario
        window.addEventListener('load', () => {
            if (localStorage.getItem('highContrast') === 'true') {
                body.classList.add('high-contrast');
            }
            if (localStorage.getItem('largeText') === 'true') {
                body.classList.add('large-text');
            }
        });

        // Inicializar los iconos de Lucide
        lucide.createIcons();
    </script>
</body>
</html>