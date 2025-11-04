<?php
// actions/consulta_nomina_action.php

ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
require_once dirname(__DIR__) . '/includes/db.php';
require_once dirname(__DIR__) . '/includes/functions.php';

header('Content-Type: application/json');

if (!is_logged_in() || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['error' => 'Acceso no autorizado.']);
    exit();
}

$cedula = $_POST['cedula_consulta'] ?? '';

if (empty($cedula)) {
    echo json_encode(['error' => 'Debe proporcionar una cédula para la consulta.']);
    exit();
}

try {
    // Consulta 1: Documentos solicitados
    $stmt_docs = $pdo->prepare("
        SELECT
            td.NombreDocumento,
            sd.FechaHoraSolicitud,
            sd.EstadoSolicitud
        FROM SolicitudesDocumento sd
        JOIN TiposDocumento td ON sd.ID_TipoDocumento = td.ID_TipoDocumento
        JOIN Usuarios u ON sd.ID_Usuario_Solicita = u.ID_Usuario
        WHERE u.DocumentoIdentidad = ?
        ORDER BY sd.FechaHoraSolicitud DESC
    ");
    $stmt_docs->execute([$cedula]);
    $documentos = $stmt_docs->fetchAll(PDO::FETCH_ASSOC);

    // Consulta 2: Novedades de nómina (ausencias, licencias, permisos)
    $stmt_novedades = $pdo->prepare("
        SELECT
            TipoNovedad,
            FechaHoraRegistro,
            EstadoNovedad
        FROM Novedades
        WHERE Documento_Afectado = ? 
        AND TipoNovedad IN ('Incapacidad', 'Licencia Remunerada', 'Permiso Remunerado', 'Licencia No Remunerada', 'Permiso No Remunerado', 'Ausencia')
        ORDER BY FechaHoraRegistro DESC
    ");
    $stmt_novedades->execute([$cedula]);
    $novedades = $stmt_novedades->fetchAll(PDO::FETCH_ASSOC);

    // Devolvemos ambos resultados en un solo JSON
    echo json_encode([
        'documentos' => $documentos,
        'novedades' => $novedades
    ]);

} catch (PDOException $e) {
    echo json_encode(['error' => 'Error en la base de datos: ' . $e->getMessage()]);
}
?>