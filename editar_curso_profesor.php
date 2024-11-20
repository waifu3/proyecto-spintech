<?php
session_start();
require_once './config/Connection.php';

// Verificar si el usuario es profesor (role_id = 3)
if (!isset($_SESSION['username']) || $_SESSION['role_id'] != 3) {
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

// Obtener módulos existentes del curso
$sql_modulos = "SELECT * FROM modulos WHERE curso_id = :curso_id";
$stmt_modulos = $pdo->prepare($sql_modulos);
$stmt_modulos->bindParam(':curso_id', $curso_id, PDO::PARAM_INT);
$stmt_modulos->execute();
$modulos = $stmt_modulos->fetchAll(PDO::FETCH_ASSOC);

$message = '';
$messageType = '';

// Procesar el formulario para editar el curso o agregar/editar/eliminar módulos
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['editar_curso'])) {
        $nombre = $_POST['nombre'];
        $descripcion_breve = $_POST['descripcion_breve'];
        $descripcion = $_POST['descripcion'];
        $tipo_discapacidad = $_POST['tipo_discapacidad'];

        $sql_update_curso = "UPDATE cursos SET nombre = :nombre, descripcion_breve = :descripcion_breve, descripcion = :descripcion, tipo_discapacidad = :tipo_discapacidad WHERE id = :curso_id";
        $stmt_update_curso = $pdo->prepare($sql_update_curso);
        $stmt_update_curso->bindParam(':nombre', $nombre);
        $stmt_update_curso->bindParam(':descripcion_breve', $descripcion_breve);
        $stmt_update_curso->bindParam(':descripcion', $descripcion);
        $stmt_update_curso->bindParam(':tipo_discapacidad', $tipo_discapacidad);
        $stmt_update_curso->bindParam(':curso_id', $curso_id);

        if ($stmt_update_curso->execute()) {
            $message = "Curso actualizado exitosamente.";
            $messageType = "success";
            // Actualizar la información del curso en la variable $curso
            $curso['nombre'] = $nombre;
            $curso['descripcion_breve'] = $descripcion_breve;
            $curso['descripcion'] = $descripcion;
            $curso['tipo_discapacidad'] = $tipo_discapacidad;
        } else {
            $message = "Error al actualizar el curso.";
            $messageType = "error";
        }
    } elseif (isset($_POST['agregar_modulo'])) {
        $nombre_modulo = $_POST['nombre_modulo'];
        $descripcion_modulo = $_POST['descripcion_modulo'];

        $sql_insert_modulo = "INSERT INTO modulos (curso_id, nombre_modulo, descripcion) VALUES (:curso_id, :nombre_modulo, :descripcion)";
        $stmt_insert_modulo = $pdo->prepare($sql_insert_modulo);
        $stmt_insert_modulo->bindParam(':curso_id', $curso_id);
        $stmt_insert_modulo->bindParam(':nombre_modulo', $nombre_modulo);
        $stmt_insert_modulo->bindParam(':descripcion', $descripcion_modulo);
        
        if ($stmt_insert_modulo->execute()) {
            $message = "Módulo agregado exitosamente.";
            $messageType = "success";
        } else {
            $message = "Error al agregar el módulo.";
            $messageType = "error";
        }
    } elseif (isset($_POST['editar_modulo'])) {
        $modulo_id = $_POST['modulo_id'];
        $nombre_modulo = $_POST['nombre_modulo'];
        $descripcion_modulo = $_POST['descripcion_modulo'];

        $sql_update = "UPDATE modulos SET nombre_modulo = :nombre_modulo, descripcion = :descripcion WHERE id = :modulo_id AND curso_id = :curso_id";
        $stmt_update = $pdo->prepare($sql_update);
        $stmt_update->bindParam(':nombre_modulo', $nombre_modulo);
        $stmt_update->bindParam(':descripcion', $descripcion_modulo);
        $stmt_update->bindParam(':modulo_id', $modulo_id);
        $stmt_update->bindParam(':curso_id', $curso_id);

        if ($stmt_update->execute()) {
            $message = "Módulo actualizado exitosamente.";
            $messageType = "success";
        } else {
            $message = "Error al actualizar el módulo.";
            $messageType = "error";
        }
    } elseif (isset($_POST['eliminar_modulo'])) {
        $modulo_id = $_POST['modulo_id'];

        $sql_delete = "DELETE FROM modulos WHERE id = :modulo_id AND curso_id = :curso_id";
        $stmt_delete = $pdo->prepare($sql_delete);
        $stmt_delete->bindParam(':modulo_id', $modulo_id);
        $stmt_delete->bindParam(':curso_id', $curso_id);

        if ($stmt_delete->execute()) {
            $message = "Módulo eliminado exitosamente.";
            $messageType = "success";
        } else {
            $message = "Error al eliminar el módulo.";
            $messageType = "error";
        }
    }

    // Recargar los módulos después de cualquier operación
    $stmt_modulos->execute();
    $modulos = $stmt_modulos->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Curso - Profesor - Spintech</title>
    <link rel="shortcut icon" href="./images/Spintech.png" type="image/x-icon">
    <link rel="stylesheet" href="./css/editar_curso_profesor.css">
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body>
    <a href="#main-content" class="skip-link">Saltar al contenido principal</a>
    <div class="accessibility-controls">
        <button id="contrast-toggle" aria-label="Alternar contraste">Alternar contraste</button>
        <button id="font-size-toggle" aria-label="Cambiar tamaño de fuente">Cambiar tamaño de fuente</button>
    </div>

    <!-- Sidebar -->
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
        <a href="configuracion_profesor.php">
            <i data-lucide="settings" class="nav-icon"></i>
            Configuración
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
            <h1>Editar Curso: <?php echo htmlspecialchars($curso['nombre']); ?></h1>
        </div>

        <?php if ($message): ?>
            <div class="alert alert-<?php echo $messageType; ?>" role="alert">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <!-- Formulario para editar el curso -->
        <section class="card">
            <h2>Editar Detalles del Curso</h2>
            <form action="editar_curso_profesor.php?id=<?php echo $curso_id; ?>" method="POST">
                <div class="form-group">
                    <label for="nombre">Nombre del Curso:</label>
                    <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($curso['nombre']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="descripcion_breve">Descripción Breve:</label>
                    <input type="text" id="descripcion_breve" name="descripcion_breve" value="<?php echo htmlspecialchars($curso['descripcion_breve']); ?>" maxlength="150" required>
                    <p class="form-help">Máximo 150 caracteres. Esta descripción aparecerá en la lista de cursos.</p>
                </div>

                <div class="form-group">
                    <label for="descripcion">Descripción Detallada:</label>
                    <textarea id="descripcion" name="descripcion" required><?php echo htmlspecialchars($curso['descripcion']); ?></textarea>
                </div>

                <div class="form-group">
                    <label for="tipo_discapacidad">Tipo de Discapacidad:</label>
                    <select id="tipo_discapacidad" name="tipo_discapacidad" required>
                        <option value="">Seleccione un tipo de discapacidad</option>
                        <option value="cognitiva" <?php echo ($curso['tipo_discapacidad'] == 'cognitiva') ? 'selected' : ''; ?>>Cognitiva</option>
                        <option value="visual" <?php echo ($curso['tipo_discapacidad'] == 'visual') ? 'selected' : ''; ?>>Visual</option>
                        <option value="auditiva" <?php echo ($curso['tipo_discapacidad'] == 'auditiva') ? 'selected' : ''; ?>>Auditiva</option>
                    </select>
                    <p class="form-help">Seleccione el tipo de discapacidad para el cual está diseñado este curso.</p>
                </div>

                <button type="submit" name="editar_curso" class="btn btn-primary">Actualizar Curso</button>
            </form>
        </section>

        <!-- Formulario para agregar un módulo -->
        <section class="card">
            <h2>Agregar Módulo</h2>
            <form action="editar_curso_profesor.php?id=<?php echo $curso_id; ?>" method="POST">
                <div class="form-group">
                    <label for="nombre_modulo">Nombre del Módulo:</label>
                    <input type="text" id="nombre_modulo" name="nombre_modulo" required>
                </div>

                <div class="form-group">
                    <label for="descripcion_modulo">Descripción:</label>
                    <textarea id="descripcion_modulo" name="descripcion_modulo" required></textarea>
                </div>

                <button type="submit" name="agregar_modulo" class="btn btn-primary">Agregar Módulo</button>
            </form>
        </section>

        <!-- Listar módulos existentes -->
        <section class="card modules-container">
            <h2>Módulos del Curso</h2>
            <?php if (count($modulos) > 0): ?>
                <?php foreach ($modulos as $modulo): ?>
                    <div class="module-item" id="modulo-<?php echo $modulo['id']; ?>">
                        <div class="module-content">
                            <h3><?php echo htmlspecialchars($modulo['nombre_modulo']); ?></h3>
                            <p><?php echo htmlspecialchars($modulo['descripcion']); ?></p>
                        </div>
                        <div class="module-actions">
                            <button 
                                class="btn btn-secondary btn-icon" 
                                onclick="editarModulo(<?php echo htmlspecialchars(json_encode($modulo)); ?>)"
                                aria-label="Editar módulo"
                            >
                                <i data-lucide="edit-2"></i>
                            </button>
                            <button 
                                class="btn btn-danger btn-icon" 
                                onclick="confirmarEliminar(<?php echo $modulo['id']; ?>)"
                                aria-label="Eliminar módulo"
                            >
                                <i data-lucide="trash-2"></i>
                            </button>
                        </div>
                        <!-- Formulario de edición (inicialmente oculto) -->
                        <form 
                            action="editar_curso_profesor.php?id=<?php echo $curso_id; ?>" 
                            method="POST" 
                            class="edit-form" 
                            id="edit-form-<?php echo $modulo['id']; ?>"
                            style="display: none;"
                        >
                            <input type="hidden" name="modulo_id" value="<?php echo $modulo['id']; ?>">
                            <div class="form-group">
                                <label for="nombre_modulo_<?php echo $modulo['id']; ?>">Nombre del Módulo:</label>
                                <input 
                                    type="text" 
                                    id="nombre_modulo_<?php echo $modulo['id']; ?>" 
                                    name="nombre_modulo" 
                                    value="<?php echo htmlspecialchars($modulo['nombre_modulo']); ?>" 
                                    required
                                >
                            </div>
                            <div class="form-group">
                                <label for="descripcion_modulo_<?php echo $modulo['id']; ?>">Descripción:</label>
                                <textarea 
                                    id="descripcion_modulo_<?php echo $modulo['id']; ?>" 
                                    name="descripcion_modulo" 
                                    required
                                ><?php echo htmlspecialchars($modulo['descripcion']); ?></textarea>
                            </div>
                            <div class="button-group">
                                <button type="submit" name="editar_modulo" class="btn btn-primary">Guardar Cambios</button>
                                <button type="button" class="btn btn-secondary" onclick="cancelarEdicion(<?php echo $modulo['id']; ?>)">Cancelar</button>
                            </div>
                        </form>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No hay módulos para este curso.</p>
            <?php endif; ?>
        </section>

        <!-- Formulario oculto para eliminar módulos -->
        <form id="eliminar-form" method="POST" style="display: none;">
            <input type="hidden" name="modulo_id" id="eliminar_modulo_id">
            <input type="hidden" name="eliminar_modulo" value="1">
        </form>
    </main>

    <script>
        // Funciones para manejar la edición de módulos
        function editarModulo(modulo) {
            const moduleContent = document.querySelector(`#modulo-${modulo.id} .module-content`);
            const moduleActions = document.querySelector(`#modulo-${modulo.id} .module-actions`);
            const editForm = document.querySelector(`#edit-form-${modulo.id}`);
            
            moduleContent.style.display = 'none';
            moduleActions.style.display = 'none';
            editForm.style.display = 'block';
        }

        function cancelarEdicion(moduloId) {
            const moduleContent = document.querySelector(`#modulo-${moduloId} .module-content`);
            const moduleActions = document.querySelector(`#modulo-${moduloId} .module-actions`);
            const editForm = document.querySelector(`#edit-form-${moduloId}`);
            
            moduleContent.style.display = 'block';
            moduleActions.style.display = 'flex';
            editForm.style.display = 'none';
        }

        // Función para confirmar eliminación
        function confirmarEliminar(moduloId) {
            if (confirm('¿Estás seguro de que deseas eliminar este módulo? Esta acción no se puede deshacer.')) {
                const form = document.getElementById('eliminar-form');
                document.getElementById('eliminar_modulo_id').value = moduloId;
                form.submit();
            }
        }

        // Inicialización de controles de accesibilidad
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