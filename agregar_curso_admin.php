<?php
session_start();
require_once './config/Connection.php';

// Verificar si el usuario es administrador
if (!isset($_SESSION['username']) || $_SESSION['role_id'] != 1) {
    header('Location: ../login.php');
    exit();
}

// Conectar a la base de datos
$connection = new Connection();
$pdo = $connection->connect();

// Agregar un nuevo curso cuando se envíe el formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $profesor_id = $_POST['profesor_id'];

    $sql = "INSERT INTO cursos (nombre, descripcion, profesor_id) VALUES (:nombre, :descripcion, :profesor_id)";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':nombre', $nombre);
    $stmt->bindParam(':descripcion', $descripcion);
    $stmt->bindParam(':profesor_id', $profesor_id, PDO::PARAM_INT);
    $stmt->execute();

    header("Location: gestionar_cursos_admin.php");
    exit();
}

// Obtener lista de profesores
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
    <title>Agregar Curso - Spintech</title>
    <link rel="shortcut icon" href="./images/Spintech.png" type="image/x-icon">
    <link rel="stylesheet" href="./css/agregar_curso_admin.css">
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
                    <h1>Agregar Nuevo Curso</h1>
                </div>
                <div class="card">
                    <p class="description">Ingrese los detalles del nuevo curso para registrarlo en el sistema.</p>
                    <form action="agregar_curso_admin.php" method="POST">
                        <div class="form-group">
                            <label for="nombre">Nombre del Curso:</label>
                            <input type="text" id="nombre" name="nombre" required placeholder="Ingrese el nombre del curso">
                        </div>
                        <div class="form-group">
                            <label for="descripcion">Descripción del Curso:</label>
                            <textarea id="descripcion" name="descripcion" required placeholder="Ingrese la descripción del curso"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="profesor_id">Profesor Asignado:</label>
                            <select id="profesor_id" name="profesor_id" required>
                                <option value="">Seleccione un profesor</option>
                                <?php foreach ($profesores as $profesor): ?>
                                    <option value="<?php echo $profesor['id']; ?>"><?php echo htmlspecialchars($profesor['username']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Agregar Curso</button>
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