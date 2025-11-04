<?php
// actions/informes_action.php (Versión Funcional para Múltiples Reportes)

ini_set('display_errors', 1);
error_reporting(E_ALL);
ob_start();
session_start();

require_once dirname(__DIR__) . '/includes/db.php';
require_once dirname(__DIR__) . '/includes/functions.php';
require_once dirname(__DIR__) . '/libs/GeneradorInforme.php';

if (!is_logged_in() || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Acceso no autorizado.");
}

$tipo_informe = $_POST['informe-tipo'] ?? '';
$fecha_inicio = $_POST['informe-fecha-inicio'] ?? '';
$fecha_fin = $_POST['informe-fecha-fin'] ?? '';
$cedula = $_POST['informe-cedula'] ?? '';
$id_cliente = $_POST['informe-puesto'] ?? '';

$datos = [];
$titulo_pdf = "Reporte Desconocido";
$cabecera = [];
$anchos = [];
$params = [];

try {
    switch ($tipo_informe) {
        // --- CASO 1: INFORME DE VISITAS DE SUPERVISIÓN ---
        case 'visitas-supervision':
            $titulo_pdf = "Informe de Visitas de Supervisión";
            $cabecera = ['ID', 'Fecha', 'Supervisor', 'Auditado', 'Cliente'];
            $anchos = [15, 40, 60, 60, 80];

            $sql = "SELECT v.ID_Visita, v.FechaVisita, CONCAT(sup.Nombre, ' ', sup.Apellido) AS Supervisor, COALESCE(pa.nombre_completo, v.Documento_Auditado, 'N/A') AS Auditado, cli.NombreEmpresa AS Cliente
                    FROM Visitas v
                    LEFT JOIN Usuarios sup ON v.ID_Usuario_Supervisor = sup.ID_Usuario
                    LEFT JOIN Clientes cli ON v.ID_Cliente = cli.ID_Cliente
                    LEFT JOIN personal_autocompletar pa ON v.Documento_Auditado = pa.documento
                    WHERE 1=1";

            if (!empty($fecha_inicio)) { $sql .= " AND DATE(v.FechaVisita) >= ?"; $params[] = $fecha_inicio; }
            if (!empty($fecha_fin)) { $sql .= " AND DATE(v.FechaVisita) <= ?"; $params[] = $fecha_fin; }
            if (!empty($cedula)) { $sql .= " AND v.Documento_Auditado = ?"; $params[] = $cedula; }
            if (!empty($id_cliente)) { $sql .= " AND v.ID_Cliente = ?"; $params[] = $id_cliente; }
            $sql .= " ORDER BY v.FechaVisita DESC";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $resultados_reales = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (empty($resultados_reales)) {
                $datos = [ ['001', '2025-08-07 11:00', 'Supervisor de Prueba', 'Vigilante Simulado', 'Cliente Ejemplo (Simulado)'], ['002', '2025-08-06 22:30', 'Otro Supervisor', 'Vigilante Nocturno', 'Edificio ABC (Simulado)'] ];
                $titulo_pdf .= " (DATOS SIMULADOS)";
            } else {
                foreach($resultados_reales as $fila) { $datos[] = array_values($fila); }
            }
            break;

        // --- CASO 2: INFORME DE NOVEDADES HISTÓRICAS ---
        case 'novedades-historicas':
            $titulo_pdf = "Informe de Novedades Históricas";
            $cabecera = ['ID', 'Tipo Novedad', 'Fecha', 'Personal Afectado', 'Reportado Por'];
            $anchos = [15, 60, 40, 70, 70];

            $sql = "SELECT n.ID_Novedad, n.TipoNovedad, n.FechaHoraRegistro, COALESCE(pa.nombre_completo, n.Documento_Afectado, 'N/A') AS PersonalAfectado, CONCAT(u.Nombre, ' ', u.Apellido) AS UsuarioReporta
                    FROM Novedades n
                    JOIN Usuarios u ON n.ID_Usuario_Reporta = u.ID_Usuario
                    LEFT JOIN personal_autocompletar pa ON n.Documento_Afectado = pa.documento
                    WHERE 1=1";

            if (!empty($fecha_inicio)) { $sql .= " AND DATE(n.FechaHoraRegistro) >= ?"; $params[] = $fecha_inicio; }
            if (!empty($fecha_fin)) { $sql .= " AND DATE(n.FechaHoraRegistro) <= ?"; $params[] = $fecha_fin; }
            if (!empty($cedula)) { $sql .= " AND n.Documento_Afectado = ?"; $params[] = $cedula; }

            $sql .= " ORDER BY n.FechaHoraRegistro DESC";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $resultados_reales = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (empty($resultados_reales)) {
                $datos = [ ['001', 'Incapacidad (Simulada)', '2025-08-07 10:00', 'Empleado de Prueba (12345)', 'Admin Sistema'], ['002', 'Ausencia (Simulada)', '2025-08-06 08:00', 'Otro Empleado (67890)', 'Supervisor de Turno'] ];
                $titulo_pdf .= " (DATOS SIMULADOS)";
            } else {
                foreach($resultados_reales as $fila) { $datos[] = array_values($fila); }
            }
            break;

        default:
            $titulo_pdf = "Error en el Informe";
            $cabecera = ['Error'];
            $anchos = [255];
            $datos = [['El tipo de informe seleccionado no es válido o no está implementado todavía.']];
            break;
    }

} catch (PDOException $e) {
    die("Error al generar el informe: " . $e->getMessage());
}

// --- Generación del PDF ---
$pdf = new GeneradorInforme();
$pdf->setTitulo($titulo_pdf);
$pdf->crearTabla($cabecera, $datos, $anchos);
ob_end_clean();
$pdf->Output('I', 'Informe_' . preg_replace('/[^A-Za-z0-9\-]/', '', $tipo_informe) . '.pdf');
exit();
?>