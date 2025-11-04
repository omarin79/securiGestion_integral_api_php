<?php
// actions/responder_turno_action.php

ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
require_once dirname(__DIR__) . '/includes/db.php';
require_once dirname(__DIR__) . '/includes/functions.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require dirname(__DIR__) . '/libs/PHPMailer/Exception.php';
require dirname(__DIR__) . '/libs/PHPMailer/PHPMailer.php';
require dirname(__DIR__) . '/libs/PHPMailer/SMTP.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['error' => 'Método no permitido.']);
    exit();
}

$cedula_empleado = $_POST['cedula'] ?? '';
$accion = $_POST['accion'] ?? '';

if (empty($cedula_empleado) || empty($accion)) {
    echo json_encode(['error' => 'Faltan datos en la solicitud.']);
    exit();
}

$destinatario_email = 'supervisor@securigestion.com'; // Correo del supervisor
$destinatario_nombre = 'Departamento de Programación';

$mail = new PHPMailer(true);

try {
    $stmt = $pdo->prepare("SELECT CONCAT(Nombre, ' ', Apellido) AS nombre_completo FROM Usuarios WHERE DocumentoIdentidad = ?");
    $stmt->execute([$cedula_empleado]);
    $empleado = $stmt->fetch(PDO::FETCH_ASSOC);
    $nombre_empleado = $empleado ? $empleado['nombre_completo'] : "C.C. " . $cedula_empleado;
    
    $accion_texto = ($accion === 'aceptado') ? 'ACEPTADO' : 'RECHAZADO';
    
    // --- Configuración del servidor SMTP ---
    // ¡¡¡IMPORTANTE!!! DEBES USAR TUS PROPIAS CREDENCIALES
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'tu-correo@gmail.com'; // SUSTITUYE con tu correo
    $mail->Password   = 'tu-contraseña-de-aplicacion'; // SUSTITUYE con tu contraseña de aplicación
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    $mail->Port       = 465;
    $mail->CharSet    = 'UTF-8';

    // Destinatarios
    $mail->setFrom('no-reply@securigestion.com', 'Sistema de Programación');
    $mail->addAddress($destinatario_email, $destinatario_nombre);

    // Contenido
    $mail->isHTML(true);
    $mail->Subject = "Respuesta de Turno: $nombre_empleado";
    $mail->Body    = "
        <h1>Notificación de Respuesta de Turno</h1>
        <p>El empleado <strong>{$nombre_empleado}</strong> (C.C. {$cedula_empleado}) ha <strong>{$accion_texto}</strong> la oferta para cubrir el turno.</p>
        <p>Por favor, ingrese a la plataforma para gestionar la programación.</p>
        <hr>
        <p><small>Este es un mensaje automático.</small></p>
    ";
    
    $mail->send();
    echo json_encode(['status' => 'success', 'message' => 'Notificación enviada exitosamente.']);

} catch (Exception $e) {
    error_log("Error de PHPMailer: " . $mail->ErrorInfo);
    echo json_encode(['error' => 'No se pudo enviar la notificación. Revise la configuración del correo.']);
}
?>