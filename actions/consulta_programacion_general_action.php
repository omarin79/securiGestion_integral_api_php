<?php
// actions/consulta_programacion_general_action.php

session_start();
require_once dirname(__DIR__) . '/includes/db.php';
require_once dirname(__DIR__) . '/includes/functions.php';

header('Content-Type: application/json');

if (!is_logged_in()) {
    echo json_encode(['error' => 'No autorizado']);
    exit();
}

$cedula_empleado = $_GET['cedula'] ?? null;

try {
    $sql = "
        SELECT 
            p.Fecha,
            p.Turno,
            c.NombreEmpresa AS Cliente,
            CONCAT(u.Nombre, ' ', u.Apellido) AS Empleado
        FROM Programacion p
        JOIN Clientes c ON p.ID_Cliente = c.ID_Cliente
        JOIN Usuarios u ON p.ID_Usuario = u.ID_Usuario
    ";
    
    $params = [];
    if (!empty($cedula_empleado)) {
        $sql .= " WHERE u.DocumentoIdentidad = ?";
        $params[] = $cedula_empleado;
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $programacion = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $eventos = [];
    foreach ($programacion as $turno) {
        $color = '#007bff'; // Azul por defecto para 'DIA'
        if (strtoupper($turno['Turno']) === 'DESCANSO') {
            $color = '#28a745'; // Verde
        } else if (strtoupper($turno['Turno']) === 'NOCHE') {
            $color = '#343a40'; // Gris oscuro
        }

        $eventos[] = [
            'title' => $turno['Empleado'] . ' - ' . $turno['Turno'],
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