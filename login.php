<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Spintech</title>
    <link rel="shortcut icon" href="./images/Spintech.png" type="image/x-icon">
    <link rel="stylesheet" href="./css/login.css">
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body>
    <a href="#main-content" class="skip-link">Saltar al contenido principal</a>
    <div class="accessibility-controls">
        <button id="contrast-toggle" aria-label="Alternar contraste">Alternar contraste</button>
        <button id="font-size-toggle" aria-label="Cambiar tamaño de fuente">Cambiar tamaño de fuente</button>
    </div>

    <a href="index.php" class="back-button" aria-label="Volver a la página principal">
        <i data-lucide="arrow-left"></i>
        <span>Volver</span>
    </a>

    <main id="main-content" class="main">
        <div class="login-container">
            <div class="logo">
                <i data-lucide="book-open" class="logo-icon"></i>
                <h1>Spintech</h1>
            </div>

            <h2>Iniciar Sesión</h2>

            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-error" role="alert">
                    <?php echo htmlspecialchars($_GET['error']); ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['message'])): ?>
                <div class="alert alert-success" role="alert">
                    <?php echo htmlspecialchars($_GET['message']); ?>
                </div>
            <?php endif; ?>

            <form action="./InicioSesion/InicioSesion.php" method="POST">
                <div class="form-group">
                    <label for="username">Correo o usuario de red</label>
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
                <div class="form-options">
                    <div class="checkbox">
                        <input type="checkbox" id="remember-me" name="remember-me">
                        <label for="remember-me">Recordar</label>
                         </div>
                    <a href="forgot_password.php" class="forgot-password">¿Olvidó su contraseña?</a>
                </div>
                <button type="submit" class="btn btn-primary">Ingresar</button>
            </form>
            <p class="signup-link">¿No tienes una cuenta? <a href="registrarse.php">Regístrate ahora</a></p>
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