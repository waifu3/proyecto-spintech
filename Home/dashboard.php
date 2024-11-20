<?php
session_start();

// Verifica si el usuario ha iniciado sesión
if (!isset($_SESSION['username'])) {
    header('Location: ../login.php');
    exit;
}

// Verifica el rol del usuario (solo usuarios con role_id = 2 pueden acceder)
if ($_SESSION['role_id'] !== 2) {
    echo "Acceso denegado. Solo los usuarios pueden acceder a esta página.";
    exit;
}

// Conexión a la base de datos
require_once '../config/Connection.php';
$connection = new Connection();
$conn = $connection->connect();

// Función para obtener las actividades recientes
function getRecentActivities($conn, $userId) {
    $query = "SELECT c.nombre as curso, 'Inscripción' as actividad, i.id as inscripcion_id, 
                     i.id as fecha_actividad
              FROM inscripciones i
              JOIN cursos c ON i.curso_id = c.id
              WHERE i.usuario_id = :userId1
              UNION ALL
              SELECT c.nombre as curso, 'Curso completado' as actividad, cc.id as inscripcion_id,
                     cc.fecha_completado as fecha_actividad
              FROM cursos_completados cc
              JOIN cursos c ON cc.curso_id = c.id
              WHERE cc.usuario_id = :userId2
              ORDER BY fecha_actividad DESC
              LIMIT 5";
    
    $stmt = $conn->prepare($query);
    $stmt->bindValue(':userId1', $userId, PDO::PARAM_INT);
    $stmt->bindValue(':userId2', $userId, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Función para obtener las estadísticas del usuario
function getUserStatistics($conn, $userId) {
    $query = "SELECT 
                (SELECT COUNT(*) FROM cursos_completados WHERE usuario_id = :userId1) as cursos_completados,
                (SELECT COUNT(*) FROM inscripciones WHERE usuario_id = :userId2) as cursos_inscritos,
                (SELECT COUNT(*) FROM recursos r
                 JOIN inscripciones i ON r.curso_id = i.curso_id
                 WHERE i.usuario_id = :userId3 AND r.tipo_recurso = 'video') as videos_disponibles";
    
    $stmt = $conn->prepare($query);
    $stmt->bindValue(':userId1', $userId, PDO::PARAM_INT);
    $stmt->bindValue(':userId2', $userId, PDO::PARAM_INT);
    $stmt->bindValue(':userId3', $userId, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Función para obtener las certificaciones del usuario (cursos completados)
function getUserCertifications($conn, $userId) {
    $query = "SELECT c.nombre as titulo, cc.fecha_completado, u.full_name as instructor
              FROM cursos_completados cc
              JOIN cursos c ON cc.curso_id = c.id
              JOIN usuarios u ON c.profesor_id = u.id
              WHERE cc.usuario_id = :userId
              ORDER BY cc.fecha_completado DESC";
    
    $stmt = $conn->prepare($query);
    $stmt->bindValue(':userId', $userId, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$userId = $_SESSION['user_id'];
$recentActivities = getRecentActivities($conn, $userId);
$statistics = getUserStatistics($conn, $userId);
$certifications = getUserCertifications($conn, $userId);

// Función para formatear la fecha
function formatDate($date) {
    $timestamp = strtotime($date);
    $now = time();
    $diff = $now - $timestamp;

    if ($diff < 3600) {
        return "Hace " . floor($diff / 60) . " minutos";
    } elseif ($diff < 86400) {
        return "Hace " . floor($diff / 3600) . " horas";
    } else {
        return "Hace " . floor($diff / 86400) . " días";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Spintech</title>
    <link rel="shortcut icon" href="../images/Spintech.png" type="image/x-icon">
    <link rel="stylesheet" href="../css/dashboard.css">
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
            <a href="../Home/dashboard.php" aria-current="page">
                <i data-lucide="home" class="nav-icon"></i>
                Inicio
            </a>
            <a href="../php/perfil.php">
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
            <h1>Bienvenido al Dashboard</h1>
        </div>
        <div class="dashboard-content">
            <!-- Resumen de Actividades -->
            <div class="card col-span-2">
                <div class="card-header">
                    <h2 class="card-title">Resumen de Actividades</h2>
                    <p class="card-description">Actividades más recientes en tus cursos</p>
                </div>
                <div class="card-content">
                    <div class="scroll-area" style="height: 300px; padding-right: 1rem;">
                        <?php foreach ($recentActivities as $activity): ?>
                            <div class="activity-item">
                                <div class="activity-header">
                                    <h3 class="activity-title"><?php echo htmlspecialchars($activity['curso']); ?></h3>
                                </div>
                                <p class="activity-description"><?php echo htmlspecialchars($activity['actividad']); ?></p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Estadísticas Recientes -->
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title">Estadísticas Recientes</h2>
                    <p class="card-description">Tu progreso general en la plataforma</p>
                </div>
                <div class="card-content">
                    <div class="statistics-grid">
                        <div class="statistic-item">
                            <i data-lucide="check-circle" class="statistic-icon text-green-500"></i>
                            <div class="statistic-value"><?php echo $statistics['cursos_completados']; ?></div>
                            <p class="statistic-label">Cursos Completados</p>
                        </div>
                        <div class="statistic-item">
                            <i data-lucide="book-open" class="statistic-icon text-blue-500"></i>
                            <div class="statistic-value"><?php echo $statistics['cursos_inscritos']; ?></div>
                            <p class="statistic-label">Cursos Inscritos</p>
                        </div>
                        <div class="statistic-item">
                            <i data-lucide="video" class="statistic-icon text-yellow-500"></i>
                            <div class="statistic-value"><?php echo $statistics['videos_disponibles']; ?></div>
                            <p class="statistic-label">Videos Disponibles</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Certificaciones Obtenidas -->
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title">Certificaciones Obtenidas</h2>
                    <p class="card-description">Tus logros académicos en Spintech</p>
                </div>
                <div class="card-content">
                    <table class="certifications-table">
                        <thead>
                            <tr>
                                <th>Certificación</th>
                                <th>Fecha</th>
                                <th>Instructor</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($certifications as $cert): ?>
                                <tr>
                                    <td class="cert-title"><?php echo htmlspecialchars($cert['titulo']); ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($cert['fecha_completado'])); ?></td>
                                    <td><?php echo htmlspecialchars($cert['instructor']); ?></td>
                                    <td>
                                        <a href="../download_certificate.php?curso=<?php echo urlencode($cert['titulo']); ?>" class="btn btn-download">
                                            <i data-lucide="download" class="btn-icon"></i>
                                            Descargar
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