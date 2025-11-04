<?php
// actions/generar_documento_action.php (Versión que registra la solicitud)

ini_set('display_errors', 1);
error_reporting(E_ALL);
ob_start(); 
session_start();

require_once dirname(__DIR__) . '/includes/db.php';
require_once dirname(__DIR__) . '/includes/functions.php';
require_once dirname(__DIR__) . '/libs/fpdf.php'; 

if (!is_logged_in() || $_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['cedula_empleado'])) { die("Solicitud no válida."); }

$cedula = $_POST['cedula_empleado'];
$tipo_salario_seleccionado = $_POST['tipo_salario'] ?? 'con_salario_basico';
$dirigido_a = $_POST['dirigido_a'] ?? 'A quien interese';

try {
    // Iniciar transacción para asegurar que todo se haga correctamente
    $pdo->beginTransaction();

    $stmt = $pdo->prepare("
        SELECT 
            u.ID_Usuario, u.Nombre, u.Apellido, u.DocumentoIdentidad, u.FechaContratacion,
            r.NombreRol AS Cargo, c.SalarioBase AS Salario
        FROM Usuarios u
        JOIN Roles r ON u.ID_Rol = r.ID_Rol
        LEFT JOIN Contratos c ON u.ID_Usuario = c.ID_Usuario
        WHERE u.DocumentoIdentidad = ?
    ");
    $stmt->execute([$cedula]);
    $empleado = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$empleado) {
        throw new Exception("No se encontró un empleado con la cédula proporcionada.");
    }

    // --- LÓGICA PARA REGISTRAR LA SOLICITUD ---
    $id_tipo_documento = 1; // 1 = Carta Laboral (según la tabla tiposdocumento)
    $id_usuario_solicita = $empleado['ID_Usuario'];

    $stmt_log = $pdo->prepare(
        "INSERT INTO SolicitudesDocumento (ID_Usuario_Solicita, ID_TipoDocumento, EstadoSolicitud) VALUES (?, ?, 'Generado')"
    );
    $stmt_log->execute([$id_usuario_solicita, $id_tipo_documento]);
    // --- FIN DE LA LÓGICA DE REGISTRO ---

    // Confirmar la transacción si todo fue bien hasta ahora
    $pdo->commit();
    
    // --- Creación del PDF (sin cambios) ---
    $pdf = new FPDF('P', 'mm', 'Letter');
    $pdf->AddPage();

    $logoPath = dirname(__DIR__) . '/images/logo_segurigestion.png';
    if (!file_exists($logoPath)) { $logoPath = dirname(__DIR__) . '/images/segurigestionintegrallogo.jpg'; }
    if (file_exists($logoPath)) { $pdf->Image($logoPath, 10, 10, 30, 0); }

    $pdf->SetFont('Arial', 'B', 14);
    $pdf->SetY(40);
    $pdf->Cell(0, 10, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', 'CARTA LABORAL'), 0, 1, 'C');
    $pdf->Ln(10);
    $pdf->SetFont('Arial', '', 12);
    
    setlocale(LC_TIME, 'es_ES.UTF-8', 'es_ES', 'spanish', 'es_CO.UTF-8', 'es');
    $fecha_actual = strftime("%d de %B de %Y");
    $pdf->Cell(0, 10, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', "Fecha de expedición: Bogotá D.C., $fecha_actual"), 0, 1, 'R');
    $pdf->Ln(10);
    
    $pdf->MultiCell(0, 8, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $dirigido_a . ","));
    $pdf->Ln(8);
    
    $nombre_completo = $empleado['Nombre'] . ' ' . $empleado['Apellido'];
    $documento = number_format($empleado['DocumentoIdentidad'], 0, ',', '.');
    $cargo = $empleado['Cargo'];
    
    $salario_texto = '';
    $salario_numeros = number_format($empleado['Salario'] ?? 0, 0, ',', '.');

    switch ($tipo_salario_seleccionado) {
        case 'con_salario_basico':
            $salario_texto = "\n\nActualmente, devenga un salario mensual de " . $salario_numeros . " pesos.";
            break;
        case 'con_salario_extras':
            $salario_texto = "\n\nActualmente, devenga un salario mensual de " . $salario_numeros . " pesos, mas compensacion variable por concepto de horas extras.";
            break;
        case 'sin_salario':
            $salario_texto = "";
            break;
    }

    $fecha_contrato_texto = !empty($empleado['FechaContratacion'])
        ? "labora en nuestra compañía desde el día " . date("d/m/Y", strtotime($empleado['FechaContratacion']))
        : "labora en nuestra compañía";

    $cuerpo_carta = "Certificamos que el/la señor(a) $nombre_completo, identificado(a) con cédula de ciudadanía número $documento, $fecha_contrato_texto, desempeñando el cargo de $cargo.$salario_texto";
    
    $pdf->MultiCell(0, 8, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $cuerpo_carta));
    $pdf->Ln(15);
    
    $pdf->MultiCell(0, 8, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', "La presente certificación se expide a solicitud del interesado."));
    $pdf->Ln(20);

    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 10, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', "Atentamente,"), 0, 1, 'L');
    $pdf->Ln(10);
    $pdf->Cell(0, 10, "_________________________", 0, 1, 'L');
    $pdf->Cell(0, 8, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', "Gestión Humana"), 0, 1, 'L');
    $pdf->Cell(0, 8, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', "SEGURI GESTIÓN INTEGRAL"), 0, 1, 'L');

    ob_end_clean();
    $pdf->Output('I', iconv('UTF-8', 'ISO-8859-1//TRANSLIT', "Carta_Laboral_$cedula.pdf"));
    exit();

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    die("Error al procesar la solicitud: " . $e->getMessage());
}
?>