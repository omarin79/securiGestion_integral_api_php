<?php
// actions/get_checklist_items_action.php (Versión Final y Corregida)

ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
require_once dirname(__DIR__) . '/includes/db.php';
require_once dirname(__DIR__) . '/includes/functions.php';

// Establecemos que la respuesta siempre será en formato JSON
header('Content-Type: application/json');

// 1. Verificación de Seguridad: ¿El usuario ha iniciado sesión?
if (!is_logged_in()) {
    echo json_encode(['error' => 'No autorizado. Por favor, inicie sesión.']);
    exit();
}

// 2. Verificación de Datos: ¿Se envió un ID de checklist válido?
if (!isset($_GET['id_checklist']) || !is_numeric($_GET['id_checklist'])) {
    echo json_encode(['error' => 'No se especificó un ID de checklist válido.']);
    exit();
}

$id_checklist = (int)$_GET['id_checklist'];

try {
    // 3. Consulta a la Base de Datos
    $stmt = $pdo->prepare(
        "SELECT ID_Item, Seccion, Pregunta, Orden
         FROM ItemsChecklist
         WHERE ID_Checklist = ?
         ORDER BY Seccion, Orden, ID_Item"
    );
    $stmt->execute([$id_checklist]);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 4. Verificación de Resultados: ¿El checklist tiene preguntas?
    if (empty($items)) {
         echo json_encode(['error' => 'Este checklist no tiene preguntas asignadas.']);
         exit();
    }

    // 5. Éxito: Se envían los ítems a la página
    echo json_encode($items);

} catch (PDOException $e) {
    // 6. Manejo de Errores: Si la base de datos falla
    error_log("Error en get_checklist_items_action.php: " . $e->getMessage());
    echo json_encode(['error' => 'Error al consultar la base de datos.']);
}
?>