<?php
session_start();
require_once './config/Connection.php';

// Verificar si el usuario es profesor (role_id = 3)
if (!isset($_SESSION['username']) || $_SESSION['role_id'] != 3) {
    header('Location: ../login.php');
    exit();
}

// Conectar a la base de datos
$connection = new Connection();
$pdo = $connection->connect();

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = $_POST['nombre'];
    $descripcion_breve = $_POST['descripcion_breve'];
    $descripcion = $_POST['descripcion'];
    $tipo_discapacidad = $_POST['tipo_discapacidad'];
    $username = $_SESSION['username'];

    // Obtener el profesor_id del usuario que está agregando el curso
    $sql_profesor = "SELECT id FROM usuarios WHERE username = :username";
    $stmt_profesor = $pdo->prepare($sql_profesor);
    $stmt_profesor->bindParam(':username', $username, PDO::PARAM_STR);
    $stmt_profesor->execute();
    $profesor = $stmt_profesor->fetch(PDO::FETCH_ASSOC);
    $profesor_id = $profesor['id']; 

    // Insertar el nuevo curso en la base de datos
    $sql_insert = "INSERT INTO cursos (nombre, descripcion_breve, descripcion, profesor_id, tipo_discapacidad, fecha_creacion) VALUES (:nombre, :descripcion_breve, :descripcion, :profesor_id, :tipo_discapacidad, NOW())";
    $stmt_insert = $pdo->prepare($sql_insert);
    $stmt_insert->bindParam(':nombre', $nombre);
    $stmt_insert->bindParam(':descripcion_breve', $descripcion_breve);
    $stmt_insert->bindParam(':descripcion', $descripcion);
    $stmt_insert->bindParam(':profesor_id', $profesor_id, PDO::PARAM_INT);
    $stmt_insert->bindParam(':tipo_discapacidad', $tipo_discapacidad);
    
    if ($stmt_insert->execute()) {
        $_SESSION['message'] = "Curso agregado exitosamente.";
        $_SESSION['messageType'] = "success";
        header('Location: gestionar_cursos_profesor.php');
        exit();
    } else {
        $message = "Error al agregar el curso.";
        $messageType = "error";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Curso - Profesor - Spintech</title>
    <link rel="shortcut icon" href="./images/Spintech.png" type="image/x-icon">
    <link rel="stylesheet" href="./css/agregar_curso_profesor.css">
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
            <a href="gestionar_cursos_profesor.php" aria-current="page">
                <i data-lucide="book-open" class="nav-icon"></i>
                Gestionar Cursos
            </a>
            <a href="subir_recurso.php">
                <i data-lucide="upload" class="nav-icon"></i>
                Subir Recursos
            </a>
            <a href="./InicioSesion/CerrarSesion.php">
                <i data-lucide="log-out" class="nav-icon"></i>
                Cerrar sesión
            </a>
        </nav>
    </div>

    <!-- Contenido Principal -->
    <main id="main-content" class="main">
        <div class="title-wrapper">
            <a href="javascript:history.back()" class="back-arrow" aria-label="Volver a la página anterior">
                <i data-lucide="arrow-left"></i>
            </a>
            <h1>Agregar Nuevo Curso</h1>
        </div>

        <?php if ($message): ?>
            <div class="alert alert-<?php echo $messageType; ?>" role="alert">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <form action="agregar_curso_profesor.php" method="POST">
            <div class="form-group">
                <label for="nombre">Nombre del Curso:</label>
                <input type="text" id="nombre" name="nombre" placeholder="Ejemplo: Curso de Desarrollo Web" required>
            </div>

            <div class="form-group">
                <label for="descripcion_breve">Descripción Breve del Curso:</label>
                <input type="text" id="descripcion_breve" name="descripcion_breve" placeholder="Breve resumen del curso (máximo 150 caracteres)" maxlength="150" required>
                <p class="form-help">Esta descripción breve aparecerá en la lista de cursos. Debe ser concisa y atractiva.</p>
            </div>

            <div class="form-group">
                <label for="descripcion">Descripción Detallada del Curso:</label>
                <textarea id="descripcion" name="descripcion" placeholder="Descripción completa del curso..." required></textarea>
                <p class="form-help">Proporcione una descripción detallada del curso, incluyendo los temas que se cubrirán, los objetivos de aprendizaje y cualquier requisito previo.</p>
            </div>

            <div class="form-group">
                <label for="tipo_discapacidad">Tipo de Discapacidad:</label>
                <select id="tipo_discapacidad" name="tipo_discapacidad" required>
                    <option value="">Seleccione un tipo de discapacidad</option>
                    <option value="cognitiva">Cognitiva</option>
                    <option value="visual">Visual</option>
                    <option value="auditiva">Auditiva</option>
                </select>
                <p class="form-help">Seleccione el tipo de discapacidad para el cual está diseñado este curso.</p>
            </div>

            <div class="button-container">
                <button type="submit" class="btn btn-primary">Agregar Curso</button>
                <a href="gestionar_cursos_profesor.php" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
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