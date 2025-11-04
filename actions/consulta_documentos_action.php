<?php
// Habilitar la visualización de errores para encontrar cualquier problema
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

require_once dirname(__DIR__) . '/includes/db.php';
require_once dirname(__DIR__) . '/includes/functions.php';

// Verificamos que el usuario esté logueado
if (!is_logged_in()) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'No autorizado. Por favor, inicie sesión de nuevo.']);
    exit();
}

// Solo procesamos si es una solicitud POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cedula = $_POST['cedula_empleado_consulta_docs'] ?? '';

    if (empty($cedula)) {
        header('Content-Type: application/json');
        echo json_encode(['error' => 'El campo de cédula no puede estar vacío.']);
        exit();
    }

    try {
        // Consulta a la base de datos para obtener los documentos
        $stmt = $pdo->prepare("
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
        $stmt->execute([$cedula]);
        $documentos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Devolvemos los resultados como JSON
        header('Content-Type: application/json');
        echo json_encode($documentos);

    } catch (PDOException $e) {
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Error en la base de datos: ' . $e->getMessage()]);
    }
}
?>