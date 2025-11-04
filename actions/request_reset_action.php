<?php
// C:\xampp\htdocs\securigestion\actions\request_reset_action.php

// Habilitar la visualización de errores para depuración
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Incluir archivos necesarios
require_once dirname(__DIR__) . '/includes/db.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

// Incluir la librería PHPMailer
require dirname(__DIR__) . '/libs/PHPMailer/Exception.php';
require dirname(__DIR__) . '/libs/PHPMailer/PHPMailer.php';
require dirname(__DIR__) . '/libs/PHPMailer/SMTP.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';

    // 1. Verificar si el correo existe en la base de datos
    $stmt = $pdo->prepare("SELECT ID_Usuario FROM Usuarios WHERE CorreoElectronico = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user) {
        // 2. Generar un token seguro y su fecha de expiración
        $token = bin2hex(random_bytes(32));
        $expires_at = new DateTime('+1 hour'); // El token expira en 1 hora
        $expires_at_str = $expires_at->format('Y-m-d H:i:s');

        // 3. Guardar el token en la base de datos
        $stmt = $pdo->prepare("UPDATE Usuarios SET reset_token = ?, reset_token_expires_at = ? WHERE ID_Usuario = ?");
        $stmt->execute([$token, $expires_at_str, $user['ID_Usuario']]);

        // 4. Preparar el correo electrónico
        $reset_link = "http://localhost/securigestion/index.php?page=reset-password&token=" . $token;
        $mail = new PHPMailer(true);

        try {
            // --- CONFIGURACIÓN DEL SERVIDOR SMTP (USANDO GMAIL COMO EJEMPLO) ---
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = segurigestionintegral@gmail.com; // SUSTITUYE con tu correo de Gmail
            $mail->Password = kkmg gumj puzy grek; // SUSTITUYE con tu contraseña de aplicación de 16 letras
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port = 465;
            $mail->CharSet = 'UTF-8';

            // Contenido del correo
            $mail->setFrom('no-reply@securigestion.com', 'SecuriGestión Integral');
            $mail->addAddress($email);
            $mail->isHTML(true);
            $mail->Subject = 'Instrucciones para Restablecer tu Contraseña';
            $mail->Body = "
                <h1>Solicitud de Restablecimiento de Contraseña</h1>
                <p>Hemos recibido una solicitud para restablecer tu contraseña. Haz clic en el siguiente enlace para continuar:</p>
                <p><a href='{$reset_link}'>Restablecer mi Contraseña</a></p>
                <p>Si no solicitaste esto, puedes ignorar este correo. El enlace expirará en 1 hora.</p>
            ";

            $mail->send();

        } catch (Exception $e) {
            // Si el envío de correo falla, no se lo decimos al usuario por seguridad.
            // En un entorno de producción, aquí se registraría el error en un archivo de logs.
            // error_log("Error al enviar correo: " . $mail->ErrorInfo);
        }
    }

    // Por seguridad, siempre redirigimos a la misma página con un mensaje genérico,
    // sin importar si el correo fue encontrado o no.
    header('Location: ../index.php?page=login&success=' . urlencode('Si tu correo está registrado, hemos enviado las instrucciones de recuperación.'));
    exit();
}
?>