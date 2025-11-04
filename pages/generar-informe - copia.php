<?php
// pages/generar-informe.php

// --- Verificación de Seguridad ---
define('ROLES_ADMIN_INFORMES', [1, 2, 3]);
if (!is_logged_in() || !in_array($_SESSION['user_rol_id'], ROLES_ADMIN_INFORMES)) {
    die("Acceso denegado.");
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Método no permitido.");
}

$tipo_informe = $_POST['tipo_informe'] ?? '';
$resultados = [];
$titulo_informe = "Informe Desconocido";

// --- Lógica para el Informe de Visitas ---
if ($tipo_informe === 'visitas') {
    $fecha_inicio = $_POST['fecha_inicio'] ?? '';
    $fecha_fin = $_POST['fecha_fin'] ?? '';
    
    if (empty($fecha_inicio) || empty($fecha_fin)) {
        die("Debe seleccionar una fecha de inicio y una fecha de fin.");
    }

    $titulo_informe = "Informe de Visitas del " . htmlspecialchars($fecha_inicio) . " al " . htmlspecialchars($fecha_fin);

    try {
        $stmt = $pdo->prepare("
            SELECT 
                v.ID_Visita, v.FechaVisita, v.Hallazgos,
                CONCAT(sup.Nombre, ' ', sup.Apellido) AS Supervisor,
                COALESCE(CONCAT(aud.Nombre, ' ', aud.Apellido), pa.nombre_completo, 'N/A') AS Auditado,
                cli.NombreEmpresa AS Cliente,
                chk.NombreChecklist AS Checklist
            FROM Visitas v
            LEFT JOIN Usuarios sup ON v.ID_Usuario_Supervisor = sup.ID_Usuario
            LEFT JOIN Clientes cli ON v.ID_Cliente = cli.ID_Cliente
            LEFT JOIN Checklists chk ON v.ID_Checklist = chk.ID_Checklist
            LEFT JOIN Usuarios aud ON v.ID_Usuario_Auditado = aud.ID_Usuario
            LEFT JOIN personal_autocompletar pa ON v.Documento_Auditado = pa.documento
            WHERE DATE(v.FechaVisita) BETWEEN ? AND ?
            ORDER BY v.FechaVisita ASC
        ");
        $stmt->execute([$fecha_inicio, $fecha_fin]);
        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

    } catch (PDOException $e) {
        die("Error al generar el informe: " . $e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?php echo $titulo_informe; ?></title>
    <style>
        body { font-family: sans-serif; margin: 20px; }
        .report-container { max-width: 900px; margin: auto; }
        h1, h2 { text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        @media print {
            body { margin: 0; }
            button { display: none; }
        }
        .print-button { display: block; width: 100px; margin: 20px auto; padding: 10px; text-align: center; background: #007bff; color: white; border: none; border-radius: 5px; cursor: pointer; }
    </style>
</head>
<body>
    <div class="report-container">
        <h1><?php echo $titulo_informe; ?></h1>
        <p>Generado el: <?php echo date('d/m/Y H:i:s'); ?></p>
        <button class="print-button" onclick="window.print()">Imprimir</button>
        
        <?php if (empty($resultados)): ?>
            <p style="text-align: center;">No se encontraron registros para los filtros seleccionados.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>ID Visita</th>
                        <th>Fecha</th>
                        <th>Supervisor</th>
                        <th>Auditado</th>
                        <th>Cliente</th>
                        <th>Hallazgos</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($resultados as $fila): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($fila['ID_Visita']); ?></td>
                            <td><?php echo htmlspecialchars($fila['FechaVisita']); ?></td>
                            <td><?php echo htmlspecialchars($fila['Supervisor']); ?></td>
                            <td><?php echo htmlspecialchars($fila['Auditado']); ?></td>
                            <td><?php echo htmlspecialchars($fila['Cliente']); ?></td>
                            <td><?php echo nl2br(htmlspecialchars($fila['Hallazgos'])); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>