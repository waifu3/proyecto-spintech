<?php
session_start();
require_once 'config/Connection.php';

// Verifica si el usuario es un profesor (role_id = 3)
if (!isset($_SESSION['username']) || $_SESSION['role_id'] != 3) {
    header("Location: login.php");
    exit();
}

$connection = new Connection();
$pdo = $connection->connect();

$username = $_SESSION['username'];
$message = '';
$error = '';

// Obtener la información actual del profesor
$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE username = :username");
$stmt->execute(['username' => $username]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Procesar el formulario de actualización de información personal
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_info'])) {
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $telefono = $_POST['telefono'];
    $direccion = $_POST['direccion'];

    $stmt = $pdo->prepare("UPDATE usuarios SET full_name = :full_name, email = :email, telefono = :telefono, direccion = :direccion WHERE username = :username");
    if ($stmt->execute([
        'full_name' => $full_name,
        'email' => $email,
        'telefono' => $telefono,
        'direccion' => $direccion,
        'username' => $username
    ])) {
        $message = "Información personal actualizada con éxito.";
    } else {
        $error = "Error al actualizar la información personal.";
    }
}

// Procesar el formulario de cambio de contraseña
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if ($new_password === $confirm_password) {
        // Verificar la contraseña actual
        if (password_verify($current_password, $user['password'])) {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE usuarios SET password = :password WHERE username = :username");
            if ($stmt->execute(['password' => $hashed_password, 'username' => $username])) {
                $message = "Contraseña actualizada con éxito.";
            } else {
                $error = "Error al actualizar la contraseña.";
            }
        } else {
            $error = "La contraseña actual es incorrecta.";
        }
    } else {
        $error = "Las nuevas contraseñas no coinciden.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuración de Profesor - Spintech</title>
    <link rel="shortcut icon" href="images/Spintech.png" type="image/x-icon">
    <link rel="stylesheet" href="css/configuracion_profesor.css">
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body>
    <a href="#main-content" class="skip-link">Saltar al contenido principal</a>
    <div class="accessibility-controls">
        <button id="contrast-toggle" aria-label="Alternar contraste">Alternar contraste</button>
        <button id="font-size-toggle" aria-label="Cambiar tamaño de fuente">Cambiar tamaño de fuente</button>
    </div>

    <!-- Sidebar -->
    <div class="sidebar">
        <h2>
            <i data-lucide="book-open" class="nav-icon"></i>
            Spintech
        </h2>
        <nav aria-label="Navegación principal">
            <a href="./Home/perfil_profesor.php">
                <i data-lucide="user" class="nav-icon"></i>
                Perfil
            </a>
            <a href="gestionar_cursos_profesor.php">
                <i data-lucide="book-open" class="nav-icon"></i>
                Gestionar Cursos
            </a>
            <a href="subir_recurso.php">
                <i data-lucide="upload" class="nav-icon"></i>
                Subir Recursos
            </a>
            <a href="configuracion_profesor.php" aria-current="page">
                <i data-lucide="settings" class="nav-icon"></i>
                Configuración
            </a>
            <a href="InicioSesion/CerrarSesion.php">
                <i data-lucide="log-out" class="nav-icon"></i>
                Cerrar sesión
            </a>
        </nav>
    </div>

    <!-- Contenido Principal -->
    <main id="main-content" class="main">
        <h1>Configuración de Profesor</h1>

        <?php if ($message): ?>
            <div class="alert alert-success" role="alert">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert-danger" role="alert">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <!-- Formulario de actualización de información personal -->
        <section class="card" aria-labelledby="info-personal">
            <h2 id="info-personal">Actualizar Información Personal</h2>
            <form action="configuracion_profesor.php" method="post">
                <div class="form-group">
                    <label for="full_name">Nombre Completo:</label>
                    <input type="text" id="full_name" name="full_name" value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="email">Correo Electrónico:</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="telefono">Teléfono:</label>
                    <input type="tel" id="telefono" name="telefono" value="<?php echo htmlspecialchars($user['telefono']); ?>">
                </div>
                <div class="form-group">
                    <label for="direccion">Dirección:</label>
                    <input type="text" id="direccion" name="direccion" value="<?php echo htmlspecialchars($user['direccion']); ?>">
                </div>
                <button type="submit" name="update_info" class="btn-primary">Actualizar Información</button>
            </form>
        </section>

        <!-- Formulario de cambio de contraseña -->
        <section class="card" aria-labelledby="cambiar-contrasena">
            <h2 id="cambiar-contrasena">Cambiar Contraseña</h2>
            <form action="configuracion_profesor.php" method="post">
                <div class="form-group">
                    <label for="current_password">Contraseña Actual:</label>
                    <input type="password" id="current_password" name="current_password" required>
                </div>
                <div class="form-group">
                    <label for="new_password">Nueva Contraseña:</label>
                    <input type="password" id="new_password" name="new_password" required>
                </div>
                <div class="form-group">
                    <label for="confirm_password">Confirmar Nueva Contraseña:</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                </div>
                <button type="submit" name="change_password" class="btn-primary">Cambiar Contraseña</button>
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

        // Inicializar los iconos de Lucide
        lucide.createIcons();
    </script>
</body>
</html>