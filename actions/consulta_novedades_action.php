<?php
// C:\xampp\htdocs\Seguri_gestion_integral_React\actions\consulta_novedades_action.php

// Iniciar la sesión para verificar el login.
session_start();

// Incluir los archivos de conexión a la BD ($pdo) y funciones (is_logged_in).
require_once dirname(__DIR__) . '/includes/db.php';
require_once dirname(__DIR__) . '/includes/functions.php';

// --- Cabeceras de la API ---
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *"); // Permitir cualquier origen para Postman

// 1. Verificar Seguridad: (Temporalmente desactivado para pruebas de Postman)
/*
if (!is_logged_in()) {
    http_response_code(401); // 401 No Autorizado
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit();
}
*/

try {
    // 2. Preparar la consulta SQL para obtener las novedades.
    // Se unen las tablas Novedades, Usuarios y personal_autocompletar para obtener los nombres.
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
    
    // 3. Ejecutar y obtener los resultados.
    $novedades = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 4. Enviar la respuesta JSON con los datos.
    http_response_code(200); // 200 OK
    echo json_encode(['success' => true, 'data' => $novedades]);

} catch (PDOException $e) {
    // 5. Manejo de errores de base de datos.
    http_response_code(500); // 500 Error Interno del Servidor
    error_log("Error en consulta_novedades_action.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error al consultar la base de datos.']);
}
?>