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

// Obtener estadísticas
$total_usuarios = $pdo->query("SELECT COUNT(*) FROM usuarios")->fetchColumn();
$total_cursos = $pdo->query("SELECT COUNT(*) FROM cursos")->fetchColumn();
$total_estudiantes = $pdo->query("SELECT COUNT(*) FROM usuarios WHERE role_id = 2")->fetchColumn();
$total_profesores = $pdo->query("SELECT COUNT(*) FROM usuarios WHERE role_id = 3")->fetchColumn();

// Obtener los 5 cursos más populares
$cursos_populares = $pdo->query("
    SELECT c.nombre, COUNT(i.id) as estudiantes
    FROM cursos c
    LEFT JOIN inscripciones i ON c.id = i.curso_id
    GROUP BY c.id, c.nombre
    ORDER BY estudiantes DESC
    LIMIT 5
")->fetchAll(PDO::FETCH_ASSOC);

// Obtener estadísticas adicionales
$total_recursos = $pdo->query("SELECT COUNT(*) FROM recursos")->fetchColumn();
$total_modulos = $pdo->query("SELECT COUNT(*) FROM modulos")->fetchColumn();
$total_videos = $pdo->query("SELECT COUNT(*) FROM recursos WHERE tipo_recurso = 'video'")->fetchColumn();

// Obtener distribución de cursos por categoría
$cursos_por_categoria = $pdo->query("
    SELECT categoria, COUNT(*) as total
    FROM cursos
    GROUP BY categoria
    ORDER BY total DESC
")->fetchAll(PDO::FETCH_KEY_PAIR);

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estadísticas - Spintech</title>
    <link rel="shortcut icon" href="./images/Spintech.png" type="image/x-icon">
    <link rel="stylesheet" href="./css/estadisticas.css">
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
                <a href="gestionar_cursos_admin.php">
                    <i data-lucide="book-open" class="nav-icon"></i>
                    Gestionar Cursos
                </a>
                <a href="estadisticas.php" aria-current="page">
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
                <h1>Estadísticas de la Plataforma</h1>
                
                <div class="stats-grid">
                    <div class="stat-card">
                        <h2>Total de Usuarios</h2>
                        <p class="stat-number"><?php echo $total_usuarios; ?></p>
                    </div>
                    <div class="stat-card">
                        <h2>Total de Cursos</h2>
                        <p class="stat-number"><?php echo $total_cursos; ?></p>
                    </div>
                    <div class="stat-card">
                        <h2>Total de Estudiantes</h2>
                        <p class="stat-number"><?php echo $total_estudiantes; ?></p>
                    </div>
                    <div class="stat-card">
                        <h2>Total de Profesores</h2>
                        <p class="stat-number"><?php echo $total_profesores; ?></p>
                    </div>
                </div>

                <div class="stats-grid">
                    <div class="stat-card">
                        <h2>Total de Recursos</h2>
                        <p class="stat-number"><?php echo $total_recursos; ?></p>
                    </div>
                    <div class="stat-card">
                        <h2>Total de Módulos</h2>
                        <p class="stat-number"><?php echo $total_modulos; ?></p>
                    </div>
                    <div class="stat-card">
                        <h2>Total de Videos</h2>
                        <p class="stat-number"><?php echo $total_videos; ?></p>
                    </div>
                </div>

                <div class="charts-grid">
                    <div class="chart-container">
                        <h2>Distribución de Usuarios</h2>
                        <canvas id="userDistributionChart"></canvas>
                    </div>
                    <div class="chart-container">
                        <h2>Cursos por Categoría</h2>
                        <canvas id="courseDistributionChart"></canvas>
                    </div>
                </div>

                <div class="popular-courses">
                    <h2>Cursos Más Populares</h2>
                    <ul>
                        <?php foreach ($cursos_populares as $curso): ?>
                            <li>
                                <span class="course-name"><?php echo htmlspecialchars($curso['nombre']); ?></span>
                                <span class="student-count"><?php echo $curso['estudiantes']; ?> estudiantes</span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
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

        // Crear gráfico de distribución de usuarios
        const userCtx = document.getElementById('userDistributionChart').getContext('2d');
        new Chart(userCtx, {
            type: 'pie',
            data: {
                labels: ['Estudiantes', 'Profesores', 'Administradores'],
                datasets: [{
                    data: [<?php echo $total_estudiantes; ?>, <?php echo $total_profesores; ?>, <?php echo $total_usuarios - $total_estudiantes - $total_profesores; ?>],
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.8)',
                        'rgba(54, 162, 235, 0.8)',
                        'rgba(255, 206, 86, 0.8)'
                    ],
                    borderColor: [
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                layout: {
                    padding: {
                        left: 10,
                        right: 10,
                        top: 0,
                        bottom: 0
                    }
                },
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            boxWidth: 12,
                            padding: 15,
                            font: {
                                size: 12
                            }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                const value = context.parsed;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = ((value / total) * 100).toFixed(1);
                                label += percentage + '%';
                                return label;
                            }
                        }
                    }
                }
            }
        });

        // Crear gráfico de distribución de cursos por categoría
        const courseCtx = document.getElementById('courseDistributionChart').getContext('2d');
        new Chart(courseCtx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode(array_keys($cursos_por_categoria)); ?>,
                datasets: [{
                    label: 'Número de Cursos',
                    data: <?php echo json_encode(array_values($cursos_por_categoria)); ?>,
                    backgroundColor: 'rgba(54, 162, 235, 0.8)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                layout: {
                    padding: {
                        left: 10,
                        right: 10,
                        top: 10,
                        bottom: 10
                    }
                },
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            boxWidth: 12,
                            padding: 15,
                            font: {
                                size: 12
                            }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                const value = context.parsed.y;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = ((value / total) * 100).toFixed(1);
                                label += value + ' (' + percentage + '%)';
                                return label;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        ticks: {
                            font: {
                                size: 11
                            },
                            maxRotation: 45,
                            minRotation: 45
                        }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1,
                            font: {
                                size: 11
                            }
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>