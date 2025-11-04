<?php
// actions/consulta_reportes_action.php

ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
require_once dirname(__DIR__) . '/includes/db.php';
require_once dirname(__DIR__) . '/includes/functions.php';

header('Content-Type: application/json');

if (!is_logged_in()) {
    echo json_encode(['error' => 'No autorizado']);
    exit();
}

try {
    // Consulta para obtener las novedades de tipo 'Novedad Disciplinaria'
    $stmt = $pdo->prepare("
        SELECT 
            n.ID_Novedad,
            n.FechaHoraRegistro,
            CONCAT(u_reporta.Nombre, ' ', u_reporta.Apellido) AS UsuarioReporta,
            COALESCE(pa.nombre_completo, n.Documento_Afectado) AS PersonalAfectado,
            n.EstadoNovedad
        FROM Novedades n
        JOIN Usuarios u_reporta ON n.ID_Usuario_Reporta = u_reporta.ID_Usuario
        LEFT JOIN personal_autocompletar pa ON n.Documento_Afectado = pa.documento
        WHERE n.TipoNovedad = 'Novedad Disciplinaria'
        ORDER BY n.FechaHoraRegistro DESC
    ");
    
    $stmt->execute();
    $reportes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($reportes);

} catch (PDOException $e) {
    echo json_encode(['error' => 'Error al consultar la base de datos: ' . $e->getMessage()]);
}
?>