<?php
session_start();
require_once './config/Connection.php';

// Verificar si el usuario es administrador
if (!isset($_SESSION['username']) || $_SESSION['role_id'] != 1) {
    header('Location: ../login.php');
    exit();
}

$usuario_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($usuario_id == 0) {
    echo "Usuario no encontrado.";
    exit();
}

// Conectar a la base de datos
$connection = new Connection();
$pdo = $connection->connect();

// Obtener la información del usuario
$sql = "SELECT * FROM usuarios WHERE id = :usuario_id";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
$stmt->execute();
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$usuario) {
    echo "Usuario no encontrado.";
    exit();
}

// Actualizar la información del usuario si se envía el formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_BCRYPT) : $usuario['password'];
    $role_id = $_POST['role_id'];

    // Actualizar el usuario en la base de datos
    $sql_update = "UPDATE usuarios SET username = :username, email = :email, password = :password, role_id = :role_id WHERE id = :usuario_id";
    $stmt_update = $pdo->prepare($sql_update);
    $stmt_update->bindParam(':username', $username);
    $stmt_update->bindParam(':email', $email);
    $stmt_update->bindParam(':password', $password);
    $stmt_update->bindParam(':role_id', $role_id, PDO::PARAM_INT);
    $stmt_update->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
    $stmt_update->execute();

    // Redirigir a la página de gestión de usuarios
    header("Location: gestionar_usuarios.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Usuario - Spintech</title>
    <link rel="shortcut icon" href="./images/Spintech.png" type="image/x-icon">
    <link rel="stylesheet" href="./css/editar_usuario.css">
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body>
    <a href="#main-content" class="skip-link">Saltar al contenido principal</a>
    <div class="accessibility-controls">
        <button id="contrast-toggle" aria-label="Alternar contraste">Alternar contraste</button>
        <button id="font-size-toggle" aria-label="Cambiar tamaño de fuente">Cambiar tamaño de fuente</button>
    </div>

    <div class="layout">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <h2>
                    <i data-lucide="book-open" class="nav-icon"></i>
                    Spintech
                </h2>
            </div>
            <nav aria-label="Navegación principal">
                <a href="./Home/perfil_administrador.php">
                    <i data-lucide="user" class="nav-icon"></i>
                    Perfil
                </a>
                <a href="gestionar_usuarios.php" aria-current="page">
                    <i data-lucide="users" class="nav-icon"></i>
                    Gestionar Usuarios
                </a>
                <a href="gestionar_cursos_admin.php">
                    <i data-lucide="book-open" class="nav-icon"></i>
                    Gestionar Cursos
                </a>
                <a href="estadisticas.php">
                    <i data-lucide="bar-chart-2" class="nav-icon"></i>
                    Ver Estadísticas
                </a>
                <a href="./InicioSesion/CerrarSesion.php">
                    <i data-lucide="log-out" class="nav-icon"></i>
                    Cerrar sesión
                </a>
            </nav>
        </aside>

        <!-- Contenido Principal -->
        <main id="main-content" class="main-content">
            <div class="content-wrapper">
                <div class="header-container">
                    <a href="gestionar_usuarios.php" class="back-link" aria-label="Volver a gestionar usuarios">
                        <i data-lucide="arrow-left" class="back-icon"></i>
                    </a>
                    <h1>Editar Usuario: <?php echo htmlspecialchars($usuario['username']); ?></h1>
                </div>
                <div class="card">
                    <form action="editar_usuario.php?id=<?php echo $usuario_id; ?>" method="POST">
                        <div class="form-group">
                            <label for="username">Nombre de Usuario:</label>
                            <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($usuario['username']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Correo Electrónico:</label>
                            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($usuario['email']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="password">Contraseña (dejar en blanco para no cambiar):</label>
                            <input type="password" id="password" name="password">
                        </div>
                        <div class="form-group">
                            <label for="role_id">Rol:</label>
                            <select id="role_id" name="role_id" required>
                                <option value="1" <?php if ($usuario['role_id'] == 1) echo 'selected'; ?>>Administrador</option>
                                <option value="2" <?php if ($usuario['role_id'] == 2) echo 'selected'; ?>>Usuario</option>
                                <option value="3" <?php if ($usuario['role_id'] == 3) echo 'selected'; ?>>Profesor</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Actualizar Usuario</button>
                    </form>
                </div>
            </div>
        </main>
    </div>

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

        // Inicializar los iconos de Lucide
        lucide.createIcons();
    </script>
</body>
</html>