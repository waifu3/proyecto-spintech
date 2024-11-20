<?php
session_start();
require_once './config/Connection.php';

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['username'])) {
    header('Location: ../login.php');
    exit;
}

$conn = new Connection();
$db = $conn->connect();

$username = $_SESSION['username'];
$query = "SELECT * FROM usuarios WHERE username = :username";
$stmt = $db->prepare($query);
$stmt->bindParam(':username', $username, PDO::PARAM_STR);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['update_info'])) {
        // Actualizar información personal
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $telefono = filter_input(INPUT_POST, 'telefono', FILTER_SANITIZE_STRING);
        $direccion = filter_input(INPUT_POST, 'direccion', FILTER_SANITIZE_STRING);

        $query = "UPDATE usuarios SET email = :email, telefono = :telefono, direccion = :direccion WHERE username = :username";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->bindParam(':telefono', $telefono, PDO::PARAM_STR);
        $stmt->bindParam(':direccion', $direccion, PDO::PARAM_STR);
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        
        if ($stmt->execute()) {
            $message = "Información personal actualizada correctamente.";
            $messageType = "success";
        } else {
            $message = "Error al actualizar la información personal.";
            $messageType = "error";
        }
    }

    if (isset($_POST['change_password'])) {
        // Cambiar la contraseña
        $password_actual = $_POST['password_actual'];
        $nueva_password = $_POST['nueva_password'];
        $confirmar_password = $_POST['confirmar_password'];

        if (password_verify($password_actual, $user['password'])) {
            if ($nueva_password == $confirmar_password) {
                $password_hash = password_hash($nueva_password, PASSWORD_BCRYPT);
                $query = "UPDATE usuarios SET password = :password WHERE username = :username";
                $stmt = $db->prepare($query);
                $stmt->bindParam(':password', $password_hash, PDO::PARAM_STR);
                $stmt->bindParam(':username', $username, PDO::PARAM_STR);
                
                if ($stmt->execute()) {
                    $message = "Contraseña actualizada correctamente.";
                    $messageType = "success";
                } else {
                    $message = "Error al actualizar la contraseña.";
                    $messageType = "error";
                }
            } else {
                $message = "Las contraseñas no coinciden.";
                $messageType = "error";
            }
        } else {
            $message = "La contraseña actual es incorrecta.";
            $messageType = "error";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuración de Usuario - Spintech</title>
    <link rel="shortcut icon" href="./images/Spintech.png" type="image/x-icon">
    <link rel="stylesheet" href="./css/configuracion.css">
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body>
    <a href="#main-content" class="skip-link">Saltar al contenido principal</a>
    <div class="accessibility-controls">
        <button id="contrast-toggle" aria-label="Alternar contraste">Alternar contraste</button>
        <button id="font-size-toggle" aria-label="Cambiar tamaño de fuente">Cambiar tamaño de fuente</button>
    </div>
    <div class="sidebar">
        <h2>
            <i data-lucide="book-open" class="nav-icon"></i>
            Spintech
        </h2>
        <nav aria-label="Navegación principal">
            <a href="./Home/dashboard.php">
                <i data-lucide="home" class="nav-icon"></i>
                Inicio
            </a>
            <a href="./php/perfil.php">
                <i data-lucide="user" class="nav-icon"></i>
                Perfil
            </a>
            <a href="cursos.php">
                <i data-lucide="book-open" class="nav-icon"></i>
                Cursos
            </a>
            <a href="configuracion.php" aria-current="page">
                <i data-lucide="settings" class="nav-icon"></i>
                Configuración
            </a>
            <a href="./InicioSesion/CerrarSesion.php">
                <i data-lucide="log-out" class="nav-icon"></i>
                Cerrar sesión
            </a>
        </nav>
    </div>
    <main id="main-content" class="main">
        <div class="title-wrapper">
            <a href="javascript:history.back()" class="back-arrow" aria-label="Volver a la página anterior">
                <i data-lucide="arrow-left"></i>
            </a>
            <h1>Configuración de Usuario</h1>
        </div>
        
        <?php if ($message): ?>
            <div class="alert alert-<?php echo $messageType; ?>" role="alert">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <section class="card" aria-labelledby="personal-info-heading">
            <h2 id="personal-info-heading">Actualizar información personal</h2>
            <form action="configuracion.php" method="POST">
                <div class="form-group">
                    <label for="email">Correo electrónico:</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required aria-required="true">
                </div>
                <div class="form-group">
                    <label for="telefono">Teléfono:</label>
                    <input type="tel" id="telefono" name="telefono" value="<?php echo htmlspecialchars($user['telefono']); ?>" required aria-required="true">
                </div>
                <div class="form-group">
                    <label for="direccion">Dirección:</label>
                    <input type="text" id="direccion" name="direccion" value="<?php echo htmlspecialchars($user['direccion']); ?>" required aria-required="true">
                </div>
                <div class="form-actions">
                    <button type="submit" name="update_info" class="btn btn-primary" onclick="return confirm('¿Estás seguro de que quieres actualizar tu información?')">Actualizar Información</button>
                </div>
            </form>
        </section>

        <section class="card" aria-labelledby="password-change-heading">
            <h2 id="password-change-heading">Cambiar Contraseña</h2>
            <form action="configuracion.php" method="POST">
                <div class="form-group">
                    <label for="password_actual">Contraseña Actual:</label>
                    <input type="password" id="password_actual" name="password_actual" required aria-required="true">
                </div>
                <div class="form-group">
                    <label for="nueva_password">Nueva Contraseña:</label>
                    <div class="password-input-wrapper">
                        <input type="password" id="nueva_password" name="nueva_password" required aria-required="true" aria-describedby="password-requirements">
                        <button type="button" class="toggle-password" aria-label="Mostrar contraseña">
                            <span class="show-password">Mostrar</span>
                            <span class="hide-password hidden">Ocultar</span>
                        </button>
                    </div>
                    <p id="password-requirements" class="form-hint">La contraseña debe tener al menos 8 caracteres, incluir una mayúscula, una minúscula y un número.</p>
                </div>
                <div class="form-group">
                    <label for="confirmar_password">Confirmar Nueva Contraseña:</label>
                    <input type="password" id="confirmar_password" name="confirmar_password" required aria-required="true">
                </div>
                <div class="form-actions">
                    <button type="submit" name="change_password" class="btn btn-primary">Cambiar Contraseña</button>
                </div>
            </form>
        </section>
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

        // Funcionalidad para mostrar/ocultar contraseña
        document.querySelectorAll('.toggle-password').forEach(button => {
            button.addEventListener('click', function() {
                const input = this.previousElementSibling;
                const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
                input.setAttribute('type', type);
                this.querySelector('.show-password').classList.toggle('hidden');
                this.querySelector('.hide-password').classList.toggle('hidden');
            });
        });

        // Inicializar los iconos de Lucide
        lucide.createIcons();
    </script>
</body>
</html>