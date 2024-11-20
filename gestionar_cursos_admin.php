<?php
session_start();
require_once './config/Connection.php';

// Verificar si el usuario es un administrador (role_id = 1)
if (!isset($_SESSION['username']) || $_SESSION['role_id'] != 1) {
    header('Location: ../login.php');
    exit();
}

// Conectar a la base de datos
$connection = new Connection();
$pdo = $connection->connect();

// Obtener todos los cursos y los nombres de los profesores asociados
$sql = "SELECT c.id, c.nombre, c.descripcion_breve, u.full_name AS profesor_nombre 
        FROM cursos c 
        LEFT JOIN usuarios u ON c.profesor_id = u.id";

$stmt = $pdo->prepare($sql);
$stmt->execute();
$cursos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestionar Cursos - Administrador</title>
    <link rel="stylesheet" href="./css/gestionar_cursos_admin.css">
    <link rel="shortcut icon" href="./images/Spintech.png" type="image/x-icon">
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
                <h1>Gestionar Cursos - Administrador</h1>

                <!-- Botón para agregar cursos -->
                <a href="agregar_curso_admin.php" class="btn btn-primary">
                    <i data-lucide="plus" class="btn-icon"></i>
                    Agregar Nuevo Curso
                </a>

                <!-- Tabla de cursos -->
                <div class="card">
                    <div class="table-responsive">
                        <table>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre</th>
                                    <th>Descripción Breve</th>
                                    <th>Profesor</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($cursos as $curso): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($curso['id']); ?></td>
                                        <td><?php echo htmlspecialchars($curso['nombre']); ?></td>
                                        <td><?php echo htmlspecialchars($curso['descripcion_breve']); ?></td>
                                        <td><?php echo htmlspecialchars($curso['profesor_nombre']); ?></td>
                                        <td>
                                            <a href="editar_curso_admin.php?id=<?php echo $curso['id']; ?>" class="btn btn-secondary btn-sm">
                                                <i data-lucide="edit" class="btn-icon"></i>
                                                Editar
                                            </a>
                                            <a href="eliminar_curso_admin.php?id=<?php echo $curso['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de eliminar este curso?');">
                                                <i data-lucide="trash-2" class="btn-icon"></i>
                                                Eliminar
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
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