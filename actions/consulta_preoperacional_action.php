<?php
// actions/consulta_preoperacional_action.php

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
    $stmt = $pdo->prepare("
        SELECT 
            p.ID_Registro,
            p.FechaHora,
            p.TipoVehiculo,
            p.Placa,
            p.Marca,
            p.Modelo,
            CONCAT(u.Nombre, ' ', u.Apellido) AS Usuario
        FROM preoperacional p
        JOIN Usuarios u ON p.ID_Usuario = u.ID_Usuario
        ORDER BY p.FechaHora DESC
        LIMIT 50
    ");
    $stmt->execute();
    $registros = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($registros);

} catch (PDOException $e) {
    echo json_encode(['error' => 'Error al consultar la base de datos: ' . $e->getMessage()]);
}
?>