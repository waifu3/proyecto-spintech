<?php
session_start();
require_once 'config/Connection.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    die('Acceso denegado. Por favor, inicie sesión.');
}

// Check if a course name is provided
if (!isset($_GET['curso'])) {
    die('Curso no especificado.');
}

$courseName = urldecode($_GET['curso']);
$userId = $_SESSION['user_id'];

try {
    // Create a new connection
    $connection = new Connection();
    $conn = $connection->connect();

    // First, check if the course exists and get its ID
    $stmt = $conn->prepare("
        SELECT id, nombre 
        FROM cursos 
        WHERE nombre = :course_name
    ");
    $stmt->bindParam(':course_name', $courseName, PDO::PARAM_STR);
    $stmt->execute();
    $course = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$course) {
        die('Curso no encontrado.');
    }

    // Now check if the user has completed the course
    $stmt = $conn->prepare("
        SELECT cc.id, cc.fecha_completado, u.username AS user_name, c.nombre AS course_name
        FROM cursos_completados cc
        JOIN usuarios u ON cc.usuario_id = u.id
        JOIN cursos c ON cc.curso_id = c.id
        WHERE c.id = :course_id AND cc.usuario_id = :user_id
    ");
    
    $stmt->bindParam(':course_id', $course['id'], PDO::PARAM_INT);
    $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
    $stmt->execute();

    $completedCourse = $stmt->fetch(PDO::FETCH_ASSOC);

    // If the course is not completed, check if it's at least enrolled
    if (!$completedCourse) {
        $stmt = $conn->prepare("
            SELECT i.id
            FROM inscripciones i
            WHERE i.curso_id = :course_id AND i.usuario_id = :user_id
        ");
        $stmt->bindParam(':course_id', $course['id'], PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        $enrollment = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$enrollment) {
            die('No estás inscrito en este curso.');
        } else {
            die('Aún no has completado este curso.');
        }
    }

    // Generate the certificate content
    require_once(__DIR__ . '/fpdf/fpdf.php');

    $pdf = new FPDF();
    $pdf->AddPage();
    
    // Configure font
    $pdf->SetFont('Arial', 'B', 24);
    
    // Add logo if exists
    if (file_exists(__DIR__ . '/images/Spintech.png')) {
        $pdf->Image(__DIR__ . '/images/Spintech.png', 10, 10, 30);
    }
    
    // Title
    $pdf->Cell(0, 30, 'Certificado de Finalizacion', 0, 1, 'C');
    
    // Certificate content
    $pdf->SetFont('Arial', '', 16);
    $pdf->Cell(0, 20, 'Este certifica que:', 0, 1, 'C');
    
    $pdf->SetFont('Arial', 'B', 20);
    $pdf->Cell(0, 20, utf8_decode($completedCourse['user_name']), 0, 1, 'C');
    
    $pdf->SetFont('Arial', '', 16);
    $pdf->Cell(0, 20, 'ha completado satisfactoriamente el curso:', 0, 1, 'C');
    
    $pdf->SetFont('Arial', 'B', 20);
    $pdf->Cell(0, 20, utf8_decode($completedCourse['course_name']), 0, 1, 'C');
    
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(0, 20, 'Fecha de finalizacion: ' . date('d/m/Y', strtotime($completedCourse['fecha_completado'])), 0, 1, 'C');
    $pdf->Cell(0, 10, 'Certificado ID: ' . $completedCourse['id'], 0, 1, 'C');

    // Output the PDF
    $filename = 'Certificado_' . sanitizeFilename($completedCourse['course_name']) . '.pdf';
    $pdf->Output('D', $filename);
    exit;

} catch (PDOException $e) {
    die('Error al procesar la solicitud: ' . $e->getMessage());
} catch (Exception $e) {
    die('Error al generar el certificado: ' . $e->getMessage());
}

/**
 * Sanitize filename to prevent security issues
 */
function sanitizeFilename($filename) {
    // Remove any character that isn't a word character, dash, space, or dot
    $filename = preg_replace('/[^\w\-. ]/', '', $filename);
    // Replace spaces with underscores
    $filename = str_replace(' ', '_', $filename);
    // Transliterate Spanish characters
    $unwanted_array = array(
        'á'=>'a', 'é'=>'e', 'í'=>'i', 'ó'=>'o', 'ú'=>'u',
        'Á'=>'A', 'É'=>'E', 'Í'=>'I', 'Ó'=>'O', 'Ú'=>'U',
        'ñ'=>'n', 'Ñ'=>'N'
    );
    $filename = strtr($filename, $unwanted_array);
    return $filename;
}