<?php
// C:\xampp\htdocs\securigestion\actions\alertas_action.php (Versión para API de React)

ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();
require_once dirname(__DIR__) . '/includes/db.php';
require_once dirname(__DIR__) . '/includes/functions.php';

// Establecer la cabecera de la respuesta como JSON
header('Content-Type: application/json');

if (!is_logged_in()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Acceso no autorizado o método no válido.']);
    exit();
}

$response = [
    'success' => true,
    'data' => [
        [
            'ID_Alerta' => 1,
            'TipoAlerta' => 'Novedad Crítica',
            'Descripcion' => 'Ausencia de Unidad (CC: 123456789)',
            'Detalles' => 'Puesto: Portería Vehicular La Floresta, Turno: Noche, Fecha: 2025-06-01 23:00'
        ],
        [
            'ID_Alerta' => 2,
            'TipoAlerta' => 'Pendiente de Aprobación',
            'Descripcion' => 'Alerta: Pendiente de Aprobación (Reporte Cond. Insegura)',
            'Detalles' => 'Ubicación: Pasillo Central, Fecha: 2025-05-30 10:15'
        ]
    ]
];

echo json_encode($response);
exit();
?>