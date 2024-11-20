<?php
session_start();
require_once './config/Connection.php';

// Verificar si el usuario es profesor (role_id = 3)
if (!isset($_SESSION['username']) || $_SESSION['role_id'] != 3) {
    header('Location: ../login.php');
    exit();
}

$connection = new Connection();
$pdo = $connection->connect();

$message = '';
$messageType = '';

// Aumentar límites de PHP si es necesario
ini_set('upload_max_filesize', '64M');
ini_set('post_max_size', '64M');
ini_set('memory_limit', '256M');

// Crear el directorio de uploads si no existe
$upload_dir = "./uploads";
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

// Procesar el formulario al enviar
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $curso_id = $_POST['curso_id'];
    $nombre_archivo = $_POST['nombre_archivo']; // Cambiado de nombre_recurso a nombre_archivo
    $tipo_recurso = $_POST['tipo_archivo']; // Cambiado de tipo_archivo a tipo_recurso
    $archivo = $_FILES['archivo'];

    if ($archivo['error'] === 0) {
        // Sanitizar el nombre del archivo
        $archivo_nombre = preg_replace("/[^a-zA-Z0-9.]/", "_", $archivo['name']);
        $archivo_tmp = $archivo['tmp_name'];
        $archivo_destino = $upload_dir . "/" . $archivo_nombre;

        // Verificar el tipo de archivo y tamaño si es necesario
        $allowed_types = array('jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'mp4', 'mp3');
        $file_extension = strtolower(pathinfo($archivo_nombre, PATHINFO_EXTENSION));
        
        if (!in_array($file_extension, $allowed_types)) {
            $message = "Tipo de archivo no permitido. Los tipos permitidos son: " . implode(', ', $allowed_types);
            $messageType = "error";
        } else {
            // Intentar mover el archivo
            if (move_uploaded_file($archivo_tmp, $archivo_destino)) {
                try {
                    // Actualizado para coincidir con los nombres de columnas de la base de datos
                    $sql = "INSERT INTO recursos (curso_id, nombre_archivo, ruta_archivo, tipo_recurso, fecha_subida) 
                           VALUES (:curso_id, :nombre_archivo, :ruta_archivo, :tipo_recurso, NOW())";
                    $stmt = $pdo->prepare($sql);
                    $stmt->bindParam(':curso_id', $curso_id);
                    $stmt->bindParam(':nombre_archivo', $nombre_archivo);
                    $stmt->bindParam(':ruta_archivo', $archivo_destino);
                    $stmt->bindParam(':tipo_recurso', $tipo_recurso);
                    
                    if ($stmt->execute()) {
                        $message = "Recurso subido exitosamente.";
                        $messageType = "success";
                    } else {
                        $message = "Error al guardar la información en la base de datos.";
                        $messageType = "error";
                        // Si falla la BD, eliminar el archivo subido
                        if (file_exists($archivo_destino)) {
                            unlink($archivo_destino);
                        }
                    }
                } catch (PDOException $e) {
                    $message = "Error en la base de datos: " . $e->getMessage();
                    $messageType = "error";
                    // Si falla la BD, eliminar el archivo subido
                    if (file_exists($archivo_destino)) {
                        unlink($archivo_destino);
                    }
                }
            } else {
                $message = "Error al mover el archivo. Verifica los permisos del directorio.";
                $messageType = "error";
            }
        }
    } else {
        $message = "Error al procesar el archivo. Código de error: " . $archivo['error'];
        $messageType = "error";
    }
}

// Obtener cursos disponibles para el profesor
$sql_cursos = "SELECT id, nombre FROM cursos WHERE profesor_id = :profesor_id";
$stmt_cursos = $pdo->prepare($sql_cursos);
$stmt_cursos->bindParam(':profesor_id', $_SESSION['user_id']);
$stmt_cursos->execute();
$cursos = $stmt_cursos->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subir Recurso - Spintech</title>
    <link rel="shortcut icon" href="./images/Spintech.png" type="image/x-icon">
    <link rel="stylesheet" href="./css/subir_recurso.css">
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
            <a href="gestionar_cursos_profesor.php">
                <i data-lucide="book-open" class="nav-icon"></i>
                Gestionar Cursos
            </a>
            <a href="subir_recurso.php" aria-current="page">
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
            <h1>Subir Recurso</h1>
        </div>

        <?php if ($message): ?>
            <div class="alert alert-<?php echo $messageType; ?>" role="alert">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <form action="subir_recurso.php" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="curso_id">Selecciona un curso:</label>
                <select id="curso_id" name="curso_id" required>
                    <option value="">-- Selecciona un curso --</option>
                    <?php foreach ($cursos as $curso): ?>
                        <option value="<?php echo $curso['id']; ?>"><?php echo htmlspecialchars($curso['nombre']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="nombre_archivo">Nombre del Recurso:</label>
                <input type="text" id="nombre_archivo" name="nombre_archivo" placeholder="Nombre del recurso" required>
            </div>

            <div class="form-group">
                <label for="tipo_archivo">Tipo de archivo:</label>
                <select id="tipo_archivo" name="tipo_archivo" required>
                    <option value="documento">Documento</option>
                    <option value="video">Video</option>
                    <option value="imagen">Imagen</option>
                    <option value="archivo">Otro</option>
                </select>
            </div>

            <div class="form-group">
                <label for="archivo">Selecciona el archivo:</label>
                <input type="file" id="archivo" name="archivo" required>
            </div>

            <div class="button-container">
                <button type="submit" class="btn btn-primary">Subir Recurso</button>
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