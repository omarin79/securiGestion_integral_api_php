<?php
// C:\xampp\htdocs\securigestion\actions\nomina_action.php (Versión para API de React)

ini_set('display_errors', 1);
error_reporting(E_ALL);
ob_start();
session_start();

require_once dirname(__DIR__) . '/includes/db.php';
require_once dirname(__DIR__) . '/includes/functions.php';
require_once dirname(__DIR__) . '/libs/GeneradorDesprendible.php';
require_once dirname(__DIR__) . '/libs/GeneradorCertificado.php';

// Establecer la cabecera de la respuesta como JSON
header('Content-Type: application/json');

if (!is_logged_in()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Acceso no autorizado.']);
    exit();
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido.']);
    exit();
}

define('ROLES_ADMIN_NOMINA', [1, 2, 3]);
$es_admin = in_array($_SESSION['user_rol_id'], ROLES_ADMIN_NOMINA);

$data = json_decode(file_get_contents('php://input'), true);

$tipo_documento = $data['tipo_documento'] ?? '';
$cedula = $data['cedula'] ?? '';

if (empty($cedula)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Error: La cédula del empleado no puede estar vacía.']);
    exit();
}

// Lógica de seguridad para evitar que usuarios no-admin soliciten documentos de otros
$cedula_solicitada = $cedula;
$cedula_sesion = $_SESSION['user_doc'] ?? null;
if (!$es_admin && $cedula_solicitada !== $cedula_sesion) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Acceso denegado. No tienes permiso para solicitar documentos de otros usuarios.']);
    exit();
}

try {
    if ($tipo_documento === 'desprendible') {
        $periodo = $data['periodo'] ?? '';
        if (empty($periodo)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Error: Debes seleccionar un periodo.']);
            exit();
        }

        $stmt_empleado = $pdo->prepare("SELECT u.ID_Usuario, u.Nombre, u.Apellido, u.DocumentoIdentidad, r.NombreRol AS Cargo FROM Usuarios u JOIN Roles r ON u.ID_Rol = r.ID_Rol WHERE u.DocumentoIdentidad = ?");
        $stmt_empleado->execute([$cedula]);
        $empleado = $stmt_empleado->fetch(PDO::FETCH_ASSOC);

        if (!$empleado) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'No se encontró al empleado con la cédula ' . htmlspecialchars($cedula)]);
            exit();
        }

        $fecha_periodo = $periodo . '-01';
        $stmt_pago = $pdo->prepare("SELECT * FROM PagosNomina WHERE ID_Usuario = ? AND Periodo = ?");
        $stmt_pago->execute([$empleado['ID_Usuario'], $fecha_periodo]);
        $pago = $stmt_pago->fetch(PDO::FETCH_ASSOC);

        if (!$pago) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'No se encontraron registros de pago para el empleado en el periodo ' . htmlspecialchars($periodo)]);
            exit();
        }

        $datosEmpleado = [
            'nombre' => $empleado['Nombre'] . ' ' . $empleado['Apellido'],
            'cedula' => $empleado['DocumentoIdentidad'],
            'cargo' => $empleado['Cargo'],
            'periodo_pago' => 'Periodo de ' . htmlspecialchars($periodo)
        ];
        $datosPago = [
            'devengados' => [['concepto' => 'Salario Básico', 'valor' => $pago['SalarioBase']], ['concepto' => 'Horas Extras', 'valor' => $pago['HorasExtras']], ['concepto' => 'Bonificaciones', 'valor' => $pago['Bonificaciones']]],
            'deducidos' => [['concepto' => 'Aporte Salud', 'valor' => $pago['AporteSalud']], ['concepto' => 'Aporte Pensión', 'valor' => $pago['AportePension']], ['concepto' => 'Otras Deducciones', 'valor' => $pago['OtrasDeducciones']]],
            'total_devengado' => $pago['TotalDevengado'], 'total_deducido' => $pago['TotalDeducido'], 'neto_a_pagar' => $pago['NetoAPagar'],
        ];

        $pdf = new GeneradorDesprendible();
        $pdf->crearDesprendible($datosEmpleado, $datosPago);
        
        $pdf_output = $pdf->Output('S');
        $base64_pdf = base64_encode($pdf_output);

        echo json_encode(['success' => true, 'message' => 'Desprendible generado.', 'pdf' => $base64_pdf, 'filename' => 'Desprendible_' . $cedula . '_' . $periodo . '.pdf']);
        exit();

    } elseif ($tipo_documento === 'certificado_ingresos') {
        $anio = $data['anio'] ?? '';
        
        $stmt_empleado = $pdo->prepare("SELECT Nombre, Apellido, DocumentoIdentidad FROM Usuarios WHERE DocumentoIdentidad = ?");
        $stmt_empleado->execute([$cedula]);
        $empleado = $stmt_empleado->fetch(PDO::FETCH_ASSOC);

        if (!$empleado) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'No se encontró al empleado con la cédula ' . htmlspecialchars($cedula)]);
            exit();
        }

        $datosRetenedor = [
            'nit' => '900.123.456-7',
            'razon_social' => 'SEGURI GESTION INTEGRAL S.A.S'
        ];
        
        $datosEmpleado = [
            'nombre_completo' => $empleado['Apellido'] . ' ' . $empleado['Nombre'],
            'documento' => $empleado['DocumentoIdentidad']
        ];
        
        $datosPagos = [
            'salarios' => 15600000, 'honorarios' => 0, 'servicios' => 0,
            'comisiones' => 500000, 'prestaciones' => 1300000, 'cesantias' => 1300000,
            'aportes_salud' => 748800, 'aportes_pension' => 748800, 'retencion' => 120000
        ];
        
        $pdf = new GeneradorCertificado();
        $pdf->crearCertificado($datosRetenedor, $datosEmpleado, $datosPagos);

        $pdf_output = $pdf->Output('S');
        $base64_pdf = base64_encode($pdf_output);

        echo json_encode(['success' => true, 'message' => 'Certificado generado.', 'pdf' => $base64_pdf, 'filename' => 'Certificado_Ingresos_' . $cedula . '.pdf']);
        exit();
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error al generar el documento: ' . $e->getMessage()]);
    exit();
}
?>