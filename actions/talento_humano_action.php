<?php
// C:\xampp\htdocs\securigestion\actions\talento_humano_action.php (Versión para API de React)

ini_set('display_errors', 1);
error_reporting(E_ALL);
ob_start();
session_start();

require_once dirname(__DIR__) . '/includes/db.php';
require_once dirname(__DIR__) . '/includes/functions.php';
require_once dirname(__DIR__) . '/libs/GeneradorCarta.php';

// Establecer la cabecera de la respuesta como JSON
header('Content-Type: application/json');

if (!is_logged_in() || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Acceso no autorizado o método no válido.']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);

$tipo_documento = $data['tipo_documento'] ?? '';
$cedula = $data['cedula'] ?? '';

if (empty($cedula)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Error: La cédula del empleado no puede estar vacía.']);
    exit();
}

try {
    $stmt_empleado = $pdo->prepare("
        SELECT u.Nombre, u.Apellido, u.DocumentoIdentidad, u.FechaContratacion, r.NombreRol as Cargo, c.SalarioBase 
        FROM Usuarios u 
        JOIN Roles r ON u.ID_Rol = r.ID_Rol 
        JOIN Contratos c ON u.ID_Usuario = c.ID_Usuario 
        WHERE u.DocumentoIdentidad = ?
    ");
    $stmt_empleado->execute([$cedula]);
    $empleado = $stmt_empleado->fetch(PDO::FETCH_ASSOC);

    if (!$empleado) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'No se encontró al empleado con la cédula ' . htmlspecialchars($cedula)]);
        exit();
    }

    if ($tipo_documento === 'certificacion_laboral') {
        $nombre_completo = $empleado['Nombre'] . ' ' . $empleado['Apellido'];
        $documento = $empleado['DocumentoIdentidad'];
        $cargo = $empleado['Cargo'];
        $salario = $empleado['SalarioBase'];
        $fecha_contratacion = $empleado['FechaContratacion'];

        $pdf = new GeneradorCarta();
        $pdf->generarCertificadoLaboral($nombre_completo, $documento, $cargo, $salario, $fecha_contratacion);
        
        $pdf_output = $pdf->Output('S');
        $base64_pdf = base64_encode($pdf_output);

        echo json_encode(['success' => true, 'message' => 'Certificado laboral generado.', 'pdf' => $base64_pdf, 'filename' => 'Certificado_Laboral_' . $cedula . '.pdf']);
        exit();
    }
    
    // ... (otras lógicas de documentos si existen)

    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Tipo de documento no válido.']);
    exit();

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error al generar el documento: ' . $e->getMessage()]);
    exit();
}
?>