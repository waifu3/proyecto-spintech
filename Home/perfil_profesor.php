<?php
session_start();

// Verifica si el usuario es un profesor (role_id = 3)
if (!isset($_SESSION['username']) || $_SESSION['role_id'] != 3) {
    header("Location: ../login.php");
    exit();
}

// Conectar a la base de datos
require_once '../config/Connection.php';  // Asegúrate de que la ruta sea correcta
$connection = new Connection();
$pdo = $connection->connect();

// Obtener la información del profesor
$username = $_SESSION['username'];
$sql_profesor = "SELECT * FROM usuarios WHERE username = :username";
$stmt_profesor = $pdo->prepare($sql_profesor);
$stmt_profesor->bindParam(':username', $username, PDO::PARAM_STR);
$stmt_profesor->execute();
$profesor = $stmt_profesor->fetch(PDO::FETCH_ASSOC);

// Verificar si se encontró al profesor
if (!$profesor) {
    echo "Error: Profesor no encontrado.";
    exit();
}

// Obtener los cursos que imparte el profesor
$sql_cursos = "SELECT * FROM cursos WHERE profesor_id = :profesor_id";
$stmt_cursos = $pdo->prepare($sql_cursos);
$stmt_cursos->bindParam(':profesor_id', $profesor['id'], PDO::PARAM_INT);
$stmt_cursos->execute();
$cursos = $stmt_cursos->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil de Profesor - Spintech</title>
    <link rel="shortcut icon" href="../images/Spintech.png" type="image/x-icon">
    <link rel="stylesheet" href="../css/perfil_profesor.css">
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
    <a href="perfil_profesor.php" aria-current="page">
        <i data-lucide="user" class="nav-icon"></i>
        Perfil
    </a>
    <a href="../gestionar_cursos_profesor.php">
        <i data-lucide="book-open" class="nav-icon"></i>
        Gestionar Cursos
    </a>
    <a href="../subir_recurso.php">
        <i data-lucide="upload" class="nav-icon"></i>
        Subir Recursos
    </a>
    <a href="../configuracion_profesor.php">
        <i data-lucide="settings" class="nav-icon"></i>
        Configuración
    </a>
    <a href="../InicioSesion/CerrarSesion.php">
        <i data-lucide="log-out" class="nav-icon"></i>
        Cerrar sesión
    </a>
</nav>
    </div>

    <!-- Contenido Principal -->
    <main id="main-content" class="main">
        <h1>Perfil de Profesor</h1>

        <!-- Información personal del profesor -->
        <section class="card" aria-labelledby="info-personal">
            <h2 id="info-personal">Información Personal</h2>
            <p><strong>Nombre de Usuario:</strong> <?php echo htmlspecialchars($profesor['username']); ?></p>
            <p><strong>Correo Electrónico:</strong> <?php echo htmlspecialchars($profesor['email']); ?></p>
            <p><strong>Teléfono:</strong> <?php echo htmlspecialchars($profesor['telefono']); ?></p>
            <p><strong>Dirección:</strong> <?php echo htmlspecialchars($profesor['direccion']); ?></p>
        </section>

        <!-- Cursos que imparte el profesor -->
        <section class="card cursos-impartidos" aria-labelledby="cursos-impartidos">
            <h2 id="cursos-impartidos">Cursos que Imparte</h2>
            <?php if (count($cursos) > 0): ?>
                <table class="tabla-cursos">
                    <thead>
                        <tr>
                            <th scope="col">ID del Curso</th>
                            <th scope="col">Nombre del Curso</th>
                            <th scope="col">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($cursos as $curso): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($curso['id']); ?></td>
                                <td><?php echo htmlspecialchars($curso['nombre']); ?></td>
                                <td>
                                    <a href="../ver_estudiantes.php?curso_id=<?php echo $curso['id']; ?>" class="btn-accion">Ver Estudiantes</a>
                                    <a href="../subir_recurso.php?curso_id=<?php echo $curso['id']; ?>" class="btn-accion">Subir Recurso</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No tienes cursos asignados.</p>
            <?php endif; ?>
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