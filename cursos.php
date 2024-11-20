<?php
session_start();
require_once './config/Connection.php';

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['username']) || !isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

// Conectar a la base de datos
$connection = new Connection();
$db = $connection->connect();

try {
    // Consulta para obtener los cursos de la base de datos
    $query = "SELECT * FROM cursos";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $cursos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Obtener categorías únicas de los cursos existentes
    $categorias = array();
    foreach ($cursos as $curso) {
        if (!empty($curso['categoria']) && !in_array($curso['categoria'], $categorias)) {
            $categorias[] = $curso['categoria'];
        }
    }
    sort($categorias); // Ordenar categorías alfabéticamente

} catch (PDOException $e) {
    error_log("Error de base de datos: " . $e->getMessage());
    echo "Ha ocurrido un error al cargar los cursos. Por favor, inténtelo más tarde.";
    exit;
}

// Inscribir al usuario en un curso cuando hace clic en "Inscribirse"
if (isset($_POST['inscribir_curso_id'])) {
    $curso_id = intval($_POST['inscribir_curso_id']);
    $usuario_id = $_SESSION['user_id'];

    // Verificar si el usuario ya está inscrito en el curso
    $query_verificar = "SELECT * FROM inscripciones WHERE usuario_id = :usuario_id AND curso_id = :curso_id";
    $stmt_verificar = $db->prepare($query_verificar);
    $stmt_verificar->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
    $stmt_verificar->bindParam(':curso_id', $curso_id, PDO::PARAM_INT);
    $stmt_verificar->execute();

    if ($stmt_verificar->rowCount() == 0) {
        // Insertar la inscripción en la base de datos
        $query_inscribir = "INSERT INTO inscripciones (usuario_id, curso_id) VALUES (:usuario_id, :curso_id)";
        $stmt_inscribir = $db->prepare($query_inscribir);
        $stmt_inscribir->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
        $stmt_inscribir->bindParam(':curso_id', $curso_id, PDO::PARAM_INT);
        $stmt_inscribir->execute();
    }
    // Redirigir al detalle del curso en el que se inscribió
    header("Location: detalle_curso.php?curso_id=" . $curso_id);
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cursos - Spintech</title>
    <link rel="shortcut icon" href="./images/Spintech.png" type="image/x-icon">
    <link rel="stylesheet" href="./css/cursos.css">
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body>
    <a href="#main-content" class="skip-link">Saltar al contenido principal</a>
    
    <header>
        <div class="header-content container">
            <div class="logo">
                <h1>Spintech</h1>
            </div>
            <nav aria-label="Navegación principal">
                <ul>
                    <li><a href="index.php">Inicio</a></li>
                    <li><a href="./Home/dashboard.php">Perfil</a></li>
                    <li><a href="cursos.php" aria-current="page">Cursos</a></li>
                    <li><a href="#">Contacto</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <div class="accessibility-bar">
        <div class="container">
            <div class="accessibility-controls">
                <button id="contrast-toggle" aria-label="Alternar contraste">Alternar contraste</button>
                <button id="font-size-toggle" aria-label="Cambiar tamaño de fuente">Cambiar tamaño de fuente</button>
            </div>
        </div>
    </div>

    <main id="main-content">
        <div class="blog-container-cover">
            <div class="container-info-cover">
                <h1>¡Encuentra tu tema de interés!</h1>
                <p>Descubre los cursos que necesitas para mejorar tus habilidades.</p>
            </div>
        </div>

        <div class="container-post">
            <div class="filter-container">
                <div class="container-category">
                    <h2>Categorías:</h2>
                    <input type="radio" id="TODOS" name="categories" value="TODOS" checked>
                    <label for="TODOS">TODOS</label>
                    <?php foreach ($categorias as $categoria): ?>
                        <input type="radio" id="<?php echo htmlspecialchars($categoria); ?>" 
                               name="categories" 
                               value="<?php echo htmlspecialchars($categoria); ?>">
                        <label for="<?php echo htmlspecialchars($categoria); ?>">
                            <?php echo htmlspecialchars($categoria); ?>
                        </label>
                    <?php endforeach; ?>
                </div>
                <div class="container-disability">
                    <h2>Tipo de discapacidad:</h2>
                    <input type="checkbox" id="cognitiva" name="disability" value="cognitiva">
                    <label for="cognitiva">Cognitiva</label>
                    <input type="checkbox" id="visual" name="disability" value="visual">
                    <label for="visual">Visual</label>
                    <input type="checkbox" id="auditiva" name="disability" value="auditiva">
                    <label for="auditiva">Auditiva</label>
                </div>
            </div>

            <div class="posts">
                <?php foreach ($cursos as $curso): ?>
                    <article class="post" 
                             data-category="<?php echo htmlspecialchars($curso['categoria'] ?? ''); ?>"
                             data-disability="<?php echo htmlspecialchars($curso['tipo_discapacidad'] ?? ''); ?>">
                        <div class="ctn-img">
                            <img src="<?php echo htmlspecialchars($curso['imagen_url'] ?? './images/Spintech2.png'); ?>" 
                                 alt="Imagen del curso <?php echo htmlspecialchars($curso['nombre']); ?>">
                        </div>
                        <h3><?php echo htmlspecialchars($curso['nombre']); ?></h3>
                        <p><?php echo htmlspecialchars($curso['descripcion_breve']); ?></p>
                        <ul class="ctn-tag">
                            <li><?php echo htmlspecialchars($curso['categoria'] ?? 'General'); ?></li>
                            <?php if (!empty($curso['tipo_discapacidad'])): ?>
                                <li><?php echo htmlspecialchars($curso['tipo_discapacidad']); ?></li>
                            <?php endif; ?>
                        </ul>
                        <form method="post" action="cursos.php">
                            <input type="hidden" name="inscribir_curso_id" value="<?php echo $curso['id']; ?>">
                            <button type="submit">Inscribirse</button>
                        </form>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2024 Spintech. Todos los derechos reservados.</p>
        </div>
    </footer>

    <script>
        // Funcionalidad de filtrado
        document.addEventListener('DOMContentLoaded', function() {
            const categoryInputs = document.querySelectorAll('.container-category input');
            const disabilityInputs = document.querySelectorAll('.container-disability input');
            const posts = document.querySelectorAll('.post');

            function filterPosts() {
                const selectedCategory = document.querySelector('.container-category input:checked').value;
                const selectedDisabilities = Array.from(document.querySelectorAll('.container-disability input:checked')).map(input => input.value);

                posts.forEach(post => {
                    const postCategory = post.dataset.category;
                    const postDisability = post.dataset.disability;

                    const categoryMatch = selectedCategory === 'TODOS' || postCategory === selectedCategory;
                    const disabilityMatch = selectedDisabilities.length === 0 || 
                                          selectedDisabilities.some(disability => postDisability.includes(disability));

                    if (categoryMatch && disabilityMatch) {
                        post.style.display = 'block';
                    } else {
                        post.style.display = 'none';
                    }
                });
            }

            categoryInputs.forEach(input => input.addEventListener('change', filterPosts));
            disabilityInputs.forEach(input => input.addEventListener('change', filterPosts));
        });

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