<?php
// actions/consulta_novedades_action.php

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
    $stmt = $pdo->query("
        SELECT 
            n.ID_Novedad,
            n.TipoNovedad,
            n.FechaHoraRegistro,
            n.EstadoNovedad,
            CONCAT(u.Nombre, ' ', u.Apellido) AS UsuarioReporta,
            COALESCE(pa.nombre_completo, n.Documento_Afectado, 'N/A') AS PersonalAfectado
        FROM Novedades n
        JOIN Usuarios u ON n.ID_Usuario_Reporta = u.ID_Usuario
        LEFT JOIN personal_autocompletar pa ON n.Documento_Afectado = pa.documento
        ORDER BY n.FechaHoraRegistro DESC
        LIMIT 50
    ");
    
    $novedades = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($novedades);

} catch (PDOException $e) {
    error_log("Error en consulta_novedades_action.php: " . $e->getMessage());
    echo json_encode(['error' => 'Error al consultar la base de datos.']);
}
?>