<?php
// C:\xampp\htdocs\securigestion\actions\recuperar_action.php (Código Refactorizado para API)

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require_once dirname(__DIR__) . '/includes/db.php';
require dirname(__DIR__) . '/libs/PHPMailer/Exception.php';
require dirname(__DIR__) . '/libs/PHPMailer/PHPMailer.php';
require dirname(__DIR__) . '/libs/PHPMailer/SMTP.php';

// Establecer la cabecera de la respuesta como JSON
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $json_data = file_get_contents('php://input');
    $data = json_decode($json_data, true);

    $email = $data['email_recuperar'] ?? '';

    $stmt = $pdo->prepare("SELECT ID_Usuario FROM Usuarios WHERE CorreoElectronico = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user) {
        $token = bin2hex(random_bytes(32));
        $expires_at = new DateTime('+1 hour');
        $expires_at_str = $expires_at->format('Y-m-d H:i:s');

        $stmt_update = $pdo->prepare("UPDATE Usuarios SET reset_token = ?, reset_token_expires_at = ? WHERE ID_Usuario = ?");
        $stmt_update->execute([$token, $expires_at_str, $user['ID_Usuario']]);

        $reset_link = "http://localhost/securigestion-react/reset-password?token=" . $token; // Cita la URL de tu app de React
        
        $mail = new PHPMailer(true);
        try {
            // Configuración SMTP (asegúrate de que estos valores sean correctos)
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'segurigestionintegral@gmail.com';
            $mail->Password = 'kkmg gumj puzy grek';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;
            $mail->CharSet = 'UTF-8';

            // Contenido del correo
            $mail->setFrom('no-reply@securigestion.com', 'SecuriGestión Integral');
            $mail->addAddress($email);
            $mail->isHTML(true);
            $mail->Subject = 'Instrucciones para Restablecer tu Contraseña';
            $mail->Body = "Hola,<br><br>Para restablecer tu contraseña, por favor haz clic en el siguiente enlace:<br><a href='{$reset_link}'>Restablecer Contraseña</a><br><br>Si no solicitaste esto, ignora este mensaje.";
            
            $mail->send();
        } catch (Exception $e) {
            // Se puede registrar el error en un log, pero no se lo mostramos al usuario por seguridad
        }
    }
    
    // Devolvemos una respuesta genérica por seguridad, sin importar si el correo existía o no
    echo json_encode(['success' => true, 'message' => 'Si tu correo está registrado, hemos enviado las instrucciones de recuperación.']);
    exit();
}
?>