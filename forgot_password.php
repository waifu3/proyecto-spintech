<?php
session_start();
require_once './config/Connection.php';

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    
    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Por favor, introduce un correo electrónico válido.";
        $messageType = 'error';
    } else {
        $connection = new Connection();
        $pdo = $connection->connect();
        
        // Check if the email exists in the database
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            // Generate a unique token
            $token = bin2hex(random_bytes(32));
            $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));
            
            // Store the token in the database
            $stmt = $pdo->prepare("UPDATE usuarios SET reset_token = :token, reset_token_expiry = :expiry WHERE email = :email");
            $stmt->bindParam(':token', $token);
            $stmt->bindParam(':expiry', $expiry);
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            
            // Send email with reset link
            $resetLink = "http://yourdomain.com/reset_password.php?token=" . $token;
            $to = $email;
            $subject = "Restablecer tu contraseña - Spintech";
            $message = "Haz clic en el siguiente enlace para restablecer tu contraseña: " . $resetLink;
            $headers = "From: noreply@spintech.com";
            
            if (mail($to, $subject, $message, $headers)) {
                $message = "Se ha enviado un enlace para restablecer la contraseña a tu correo electrónico.";
                $messageType = 'success';
            } else {
                $message = "Hubo un problema al enviar el correo electrónico. Por favor, inténtalo de nuevo más tarde.";
                $messageType = 'error';
            }
        } else {
            $message = "No se encontró ninguna cuenta con ese correo electrónico.";
            $messageType = 'error';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Olvidé mi contraseña - Spintech</title>
    <link rel="shortcut icon" href="./images/Spintech.png" type="image/x-icon">
    <link rel="stylesheet" href="./css/login.css">
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body>
    <a href="#main-content" class="skip-link">Saltar al contenido principal</a>
    <div class="accessibility-controls">
        <button id="contrast-toggle" aria-label="Alternar contraste">Alternar contraste</button>
        <button id="font-size-toggle" aria-label="Cambiar tamaño de fuente">Cambiar tamaño de fuente</button>
    </div>

    <a href="login.php" class="back-button" aria-label="Volver a la página de inicio de sesión">
        <i data-lucide="arrow-left"></i>
        <span>Volver</span>
    </a>

    <main id="main-content" class="main">
        <div class="login-container">
            <div class="logo">
                <i data-lucide="book-open" class="logo-icon"></i>
                <h1>Spintech</h1>
            </div>

            <h2>Olvidé mi contraseña</h2>

            <?php if ($message): ?>
                <div class="alert alert-<?php echo $messageType; ?>" role="alert">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <form action="forgot_password.php" method="POST">
                <div class="form-group">
                    <label for="email">Correo electrónico</label>
                    <div class="input-icon">
                        <i data-lucide="mail" aria-hidden="true"></i>
                        <input type="email" id="email" name="email" required>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Enviar enlace de restablecimiento</button>
            </form>
            <p class="signup-link">¿Recordaste tu contraseña? <a href="login.php">Iniciar sesión</a></p>
        </div>
    </main>

    <script>
        // (Same accessibility script as in login.php)
        // ...
    </script>
</body>
</html>