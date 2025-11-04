<?php
// actions/consulta_programacion_action.php

session_start();
require_once dirname(__DIR__) . '/includes/db.php';
require_once dirname(__DIR__) . '/includes/functions.php';

header('Content-Type: application/json');

if (!is_logged_in()) {
    echo json_encode(['error' => 'No autorizado']);
    exit();
}

$id_usuario = $_SESSION['user_id'];
$eventos = [];

try {
    $stmt = $pdo->prepare("
        SELECT 
            p.Fecha,
            p.Turno,
            c.NombreEmpresa AS Cliente
        FROM Programacion p
        JOIN Clientes c ON p.ID_Cliente = c.ID_Cliente
        WHERE p.ID_Usuario = ?
    ");
    $stmt->execute([$id_usuario]);
    $programacion = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($programacion as $turno) {
        $color = '#007bff'; // Azul por defecto
        if (strtoupper($turno['Turno']) === 'DESCANSO') {
            $color = '#28a745'; // Verde para descansos
        } else if (strtoupper($turno['Turno']) === 'NOCHE') {
            $color = '#6c757d'; // Gris para noches
        }

        $eventos[] = [
            'title' => $turno['Turno'],
            'start' => $turno['Fecha'],
            'backgroundColor' => $color,
            'borderColor' => $color,
            'extendedProps' => [
                'cliente' => $turno['Cliente']
            ]
        ];
    }

    echo json_encode($eventos);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error en la base de datos: ' . $e->getMessage()]);
}
?>