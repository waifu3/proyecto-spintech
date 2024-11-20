<?php
session_start();
require_once './config/Connection.php';

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['username'])) {
    header('Location: ../login.php');
    exit;
}

// Conectar a la base de datos
$connection = new Connection();
$pdo = $connection->connect();

// Obtener el id del curso desde la URL
$curso_id = isset($_GET['curso_id']) ? intval($_GET['curso_id']) : 0;

// Validar que el id del curso es válido
if ($curso_id > 0) {
    // Consulta para obtener los detalles del curso
    $query = "SELECT * FROM cursos WHERE id = :curso_id";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':curso_id', $curso_id, PDO::PARAM_INT);
    $stmt->execute();
    $curso = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($curso) {
        $nombre_curso = htmlspecialchars($curso['nombre']);
        $descripcion_curso = htmlspecialchars($curso['descripcion']);
        $descripcion_breve_curso = htmlspecialchars($curso['descripcion_breve']);
        $categoria_curso = htmlspecialchars($curso['categoria']);
    } else {
        echo "Curso no encontrado.";
        exit;
    }
} else {
    header('Location: cursos.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $nombre_curso; ?> - Spintech</title>
    <link rel="shortcut icon" href="./images/Spintech.png" type="image/x-icon">
    <link rel="stylesheet" href="./css/detalle_curso.css">
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body>
    <a href="#main-content" class="skip-link">Saltar al contenido principal</a>
    
    <header>
        <div class="header-content container">
            <a href="javascript:history.back()" class="back-button" aria-label="Volver a la página anterior">
                <i data-lucide="arrow-left"></i>
            </a>
            <a href="index.php" class="logo-link">
                <h1 class="logo">Spintech</h1>
            </a>
            <nav aria-label="Navegación principal">
                <ul class="nav-links">
                    <li><a href="index.php">Inicio</a></li>
                    <li><a href="./Home/dashboard.php">Perfil</a></li>
                    <li><a href="cursos.php">Cursos</a></li>
                    <li><a href="#">Contacto</a></li>
                </ul>
            </nav>
        </div>
        <div class="accessibility-bar">
            <div class="container">
                <div class="accessibility-controls">
                    <button id="contrast-toggle" aria-label="Alternar contraste">Alternar contraste</button>
                    <button id="font-size-toggle" aria-label="Cambiar tamaño de fuente">Cambiar tamaño de fuente</button>
                </div>
            </div>
        </div>
    </header>

    <main id="main-content">
        <div class="course-header">
            <div class="container">
                <h1><?php echo $nombre_curso; ?></h1>
                <p class="course-brief"><?php echo $descripcion_breve_curso; ?></p>
                <p class="course-category"><strong>Categoría:</strong> <?php echo $categoria_curso; ?></p>
            </div>
        </div>

        <div class="container course-content">
            <article class="course-details">
                <h2>Detalles del curso</h2>
                <p><?php echo $descripcion_curso; ?></p>
            </article>

            <aside class="course-sidebar">
                <img src="./images/Spintech2.png" alt="Imagen representativa del curso <?php echo $nombre_curso; ?>" class="course-image">
                <h2><?php echo $nombre_curso; ?></h2>
                <a href="curso.php?id=<?php echo $curso_id; ?>" class="btn-start-course">
                    Iniciar Curso
                </a>
            </aside>
        </div>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2024 Spintech. Todos los derechos reservados.</p>
        </div>
    </footer>

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