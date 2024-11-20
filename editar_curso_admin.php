<?php
session_start();
require_once './config/Connection.php';

// Verificar si el usuario es administrador (role_id = 1)
if (!isset($_SESSION['username']) || $_SESSION['role_id'] != 1) {
    header('Location: ../login.php');
    exit();
}

$curso_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($curso_id == 0) {
    echo "Curso no encontrado.";
    exit();
}

// Conectar a la base de datos
$connection = new Connection();
$pdo = $connection->connect();

// Obtener información del curso
$sql_curso = "SELECT * FROM cursos WHERE id = :curso_id";
$stmt_curso = $pdo->prepare($sql_curso);
$stmt_curso->bindParam(':curso_id', $curso_id, PDO::PARAM_INT);
$stmt_curso->execute();
$curso = $stmt_curso->fetch(PDO::FETCH_ASSOC);

if (!$curso) {
    echo "Curso no encontrado.";
    exit();
}

// Actualizar los datos del curso
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $descripcion_breve = $_POST['descripcion_breve'];
    $profesor_id = $_POST['profesor_id'];

    $sql_update = "UPDATE cursos SET nombre = :nombre, descripcion = :descripcion, descripcion_breve = :descripcion_breve, profesor_id = :profesor_id WHERE id = :curso_id";
    $stmt_update = $pdo->prepare($sql_update);
    $stmt_update->bindParam(':nombre', $nombre);
    $stmt_update->bindParam(':descripcion', $descripcion);
    $stmt_update->bindParam(':descripcion_breve', $descripcion_breve);
    $stmt_update->bindParam(':profesor_id', $profesor_id, PDO::PARAM_INT);
    $stmt_update->bindParam(':curso_id', $curso_id, PDO::PARAM_INT);
    $stmt_update->execute();

    // Redirigir a la página de gestión de cursos
    header("Location: gestionar_cursos_admin.php");
    exit();
}

// Obtener todos los profesores
$sql_profesores = "SELECT id, username FROM usuarios WHERE role_id = 3";
$stmt_profesores = $pdo->prepare($sql_profesores);
$stmt_profesores->execute();
$profesores = $stmt_profesores->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Curso - Spintech</title>
    <link rel="shortcut icon" href="./images/Spintech.png" type="image/x-icon">
    <link rel="stylesheet" href="./css/editar_curso_admin.css">
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
                <a href="gestionar_usuarios.php">
                    <i data-lucide="users" class="nav-icon"></i>
                    Gestionar Usuarios
                </a>
                <a href="gestionar_cursos_admin.php" aria-current="page">
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
                    <a href="gestionar_cursos_admin.php" class="back-link" aria-label="Volver a gestionar cursos">
                        <i data-lucide="arrow-left" class="back-icon"></i>
                    </a>
                    <h1>Editar Curso: <?php echo htmlspecialchars($curso['nombre']); ?></h1>
                </div>
                <div class="card">
                    <form action="editar_curso_admin.php?id=<?php echo $curso_id; ?>" method="POST">
                        <div class="form-group">
                            <label for="nombre">Nombre del Curso:</label>
                            <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($curso['nombre']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="descripcion_breve">Descripción Breve:</label>
                            <input type="text" id="descripcion_breve" name="descripcion_breve" value="<?php echo htmlspecialchars($curso['descripcion_breve'] ?? ''); ?>" required maxlength="255">
                            <small>Máximo 255 caracteres</small>
                        </div>
                        <div class="form-group">
                            <label for="descripcion">Descripción Completa:</label>
                            <textarea id="descripcion" name="descripcion" required rows="4"><?php echo htmlspecialchars($curso['descripcion']); ?></textarea>
                        </div>
                        <div class="form-group">
                            <label for="profesor_id">Asignar Profesor:</label>
                            <select id="profesor_id" name="profesor_id" required>
                                <?php foreach ($profesores as $profesor): ?>
                                    <option value="<?php echo $profesor['id']; ?>" <?php if ($curso['profesor_id'] == $profesor['id']) echo 'selected'; ?>>
                                        <?php echo htmlspecialchars($profesor['username']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Actualizar Curso</button>
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