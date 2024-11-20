<?php
session_start();
require_once '../config/Connection.php';

// Verificar si el usuario ha iniciado sesión y si es administrador (role_id = 1)
if (!isset($_SESSION['username']) || $_SESSION['role_id'] != 1) {
    header("Location: ../login.php");
    exit();
}

// Conectar a la base de datos
$connection = new Connection();
$pdo = $connection->connect();

// Obtener la información del administrador
$username = $_SESSION['username'];
$sql = "SELECT * FROM usuarios WHERE username = :username";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':username', $username, PDO::PARAM_STR);
$stmt->execute();
$admin = $stmt->fetch(PDO::FETCH_ASSOC);

// Procesar la actualización del perfil
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $email = $_POST['email'];
    $telefono = $_POST['telefono'];
    $direccion = $_POST['direccion'];
    
    $update_sql = "UPDATE usuarios SET email = :email, telefono = :telefono, direccion = :direccion WHERE username = :username";
    $update_stmt = $pdo->prepare($update_sql);
    $update_stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $update_stmt->bindParam(':telefono', $telefono, PDO::PARAM_STR);
    $update_stmt->bindParam(':direccion', $direccion, PDO::PARAM_STR);
    $update_stmt->bindParam(':username', $username, PDO::PARAM_STR);
    
    if ($update_stmt->execute()) {
        // Actualizar la información en la sesión
        $_SESSION['success_message'] = "Perfil actualizado correctamente";
        header("Location: perfil_administrador.php");
        exit();
    } else {
        $_SESSION['error_message'] = "Error al actualizar el perfil";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil del Administrador - Spintech</title>
    <link rel="shortcut icon" href="../images/Spintech.png" type="image/x-icon">
    <link rel="stylesheet" href="../css/perfil_administrador.css">
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
            <a href="perfil_administrador.php" aria-current="page">
                <i data-lucide="user" class="nav-icon"></i>
                Perfil
            </a>
            <a href="../gestionar_usuarios.php">
                <i data-lucide="users" class="nav-icon"></i>
                Gestionar Usuarios
            </a>
            <a href="../gestionar_cursos_admin.php">
                <i data-lucide="book-open" class="nav-icon"></i>
                Gestionar Cursos
            </a>
            <a href="../estadisticas.php">
                <i data-lucide="bar-chart-2" class="nav-icon"></i>
                Ver Estadísticas
            </a>
            <a href="../InicioSesion/CerrarSesion.php">
                <i data-lucide="log-out" class="nav-icon"></i>
                Cerrar sesión
            </a>
        </nav>
    </div>

    <!-- Contenido Principal -->
    <main id="main-content" class="main">
        <div class="profile-header">
            <h1>Perfil del Administrador</h1>
        </div>

        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success">
                <?php 
                echo $_SESSION['success_message'];
                unset($_SESSION['success_message']);
                ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="alert alert-error">
                <?php 
                echo $_SESSION['error_message'];
                unset($_SESSION['error_message']);
                ?>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="section-header">
                <h2>Información Personal</h2>
                <button class="btn-icon" id="edit-profile" aria-label="Editar información personal">
                    <i data-lucide="edit-2" class="edit-icon"></i>
                </button>
            </div>
            <div class="profile-info" id="profile-info-display">
                <div class="profile-info-item">
                    <p class="profile-info-label">Nombre de Usuario</p>
                    <p><?php echo htmlspecialchars($admin['username']); ?></p>
                </div>
                <div class="profile-info-item">
                    <p class="profile-info-label">Correo Electrónico</p>
                    <p><?php echo htmlspecialchars($admin['email']); ?></p>
                </div>
                <div class="profile-info-item">
                    <p class="profile-info-label">Teléfono</p>
                    <p><?php echo htmlspecialchars($admin['telefono']); ?></p>
                </div>
                <div class="profile-info-item">
                    <p class="profile-info-label">Dirección</p>
                    <p><?php echo htmlspecialchars($admin['direccion']); ?></p>
                </div>
            </div>

            <!-- Formulario de edición (inicialmente oculto) -->
            <form id="edit-form" class="profile-edit-form hidden" method="POST" action="">
                <div class="form-group">
                    <label for="username">Nombre de Usuario</label>
                    <input type="text" id="username" value="<?php echo htmlspecialchars($admin['username']); ?>" disabled>
                </div>
                <div class="form-group">
                    <label for="email">Correo Electrónico</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($admin['email']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="telefono">Teléfono</label>
                    <input type="tel" id="telefono" name="telefono" value="<?php echo htmlspecialchars($admin['telefono']); ?>">
                </div>
                <div class="form-group">
                    <label for="direccion">Dirección</label>
                    <textarea id="direccion" name="direccion"><?php echo htmlspecialchars($admin['direccion']); ?></textarea>
                </div>
                <div class="form-actions">
                    <button type="submit" name="update_profile" class="btn btn-primary">Guardar cambios</button>
                    <button type="button" class="btn btn-secondary" id="cancel-edit">Cancelar</button>
                </div>
            </form>
        </div>
    </main>

    <script>
        // Funcionalidad para alternar el contraste alto
        const contrastToggle = document.getElementById('contrast-toggle');
        const body = document.body;

        contrastToggle.addEventListener('click', () => {
            body.classList.toggle('high-contrast');
            const isHighContrast = body.classList.contains('high-contrast');
            localStorage.setItem('highContrast', isHighContrast);
        });

        // Funcionalidad para cambiar el tamaño de fuente
        const fontSizeToggle = document.getElementById('font-size-toggle');

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

        // Funcionalidad para editar perfil
        const editProfileBtn = document.getElementById('edit-profile');
        const profileInfoDisplay = document.getElementById('profile-info-display');
        const editForm = document.getElementById('edit-form');
        const cancelEditBtn = document.getElementById('cancel-edit');

        editProfileBtn.addEventListener('click', () => {
            profileInfoDisplay.classList.add('hidden');
            editForm.classList.remove('hidden');
        });

        cancelEditBtn.addEventListener('click', () => {
            profileInfoDisplay.classList.remove('hidden');
            editForm.classList.add('hidden');
        });

        // Inicializar los iconos de Lucide
        lucide.createIcons();
    </script>
</body>
</html>