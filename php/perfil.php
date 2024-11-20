<?php
session_start();
require_once '../config/Connection.php';

// Verifica si el usuario ha iniciado sesión
if (!isset($_SESSION['username'])) {
    header('Location: ../InicioSesion/InicioSesion.php');
    exit;
}

// Crea una instancia de la conexión
$conn = new Connection();
$db = $conn->connect();

// Consulta para obtener los datos del usuario y su rol
$username = $_SESSION['username'];
$query = "
    SELECT u.*, r.nombre AS role_name 
    FROM usuarios u 
    JOIN roles r ON u.role_id = r.id 
    WHERE u.username = :username";
    
$stmt = $db->prepare($query);
$stmt->bindParam(':username', $username, PDO::PARAM_STR);
$stmt->execute();

$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Asignar valores a las variables
if ($user) {
    $username = htmlspecialchars($user['username']);
    $email = htmlspecialchars($user['email']);
    $full_name = htmlspecialchars($user['full_name']);
    $role = htmlspecialchars($user['role_name']);
    $telefono = htmlspecialchars($user['telefono']);
    $direccion = htmlspecialchars($user['direccion']);
} else {
    $username = "No disponible";
    $email = "No disponible";
    $full_name = "No disponible";
    $role = "No disponible";
    $telefono = "No disponible";
    $direccion = "No disponible";
}

// Obtener el id del usuario desde la sesión
$user_id = $_SESSION['user_id'] ?? 0;

// Consulta para contar los cursos completados
$query = "
    SELECT COUNT(*) AS cursos_completados 
    FROM cursos_completados 
    WHERE usuario_id = :user_id AND completado = 1";

$stmt = $db->prepare($query);
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$result = $stmt->fetch();

$cursos_completados = $result['cursos_completados'] ?? 0;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil de Usuario - Spintech</title>
    <link rel="shortcut icon" href="../images/Spintech.png" type="image/x-icon">
    <link rel="stylesheet" href="../css/perfil.css">
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
            <a href="../Home/dashboard.php">
                <i data-lucide="home" class="nav-icon"></i>
                Inicio
            </a>
            <a href="perfil.php" aria-current="page">
                <i data-lucide="user" class="nav-icon"></i>
                Perfil
            </a>
            <a href="../cursos.php">
                <i data-lucide="book-open" class="nav-icon"></i>
                Cursos
            </a>
            <a href="../configuracion.php">
                <i data-lucide="settings" class="nav-icon"></i>
                Configuración
            </a>
            <a href="../InicioSesion/CerrarSesion.php">
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
            <h1>Perfil de Usuario</h1>
        </div>
        <section class="card" aria-labelledby="info-personal">
            <h2 id="info-personal">Información Personal</h2>
            <div class="info-grid">
                <p><strong>Nombre de Usuario:</strong> <?php echo $username; ?></p>
                <p><strong>Nombre Completo:</strong> <?php echo $full_name; ?></p>
                <p><strong>Email:</strong> <?php echo $email; ?></p>
                <p><strong>Teléfono:</strong> <?php echo $telefono; ?></p>
                <p><strong>Rol:</strong> <?php echo $role; ?></p>
                <p><strong>Dirección:</strong> <?php echo $direccion; ?></p>
            </div>
        </section>
        
        <section class="card" aria-labelledby="actividades-recientes">
            <h2 id="actividades-recientes">Actividades Recientes</h2>
            <ul>
                <li>Curso completado: PHP Básico</li>
                <li>Acceso reciente: Curso de SQL</li>
            </ul>
        </section>

        <section class="card" aria-labelledby="estadisticas">
            <h2 id="estadisticas">Estadísticas</h2>
            <p><strong>Cursos Completados:</strong> <?php echo $cursos_completados; ?></p>
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