<?php
// C:\xampp\htdocs\securigestion\actions\visita_action.php (Versión para API)

ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
require_once dirname(__DIR__) . '/includes/db.php';
require_once dirname(__DIR__) . '/includes/functions.php';

// Establecer la cabecera de la respuesta como JSON
header('Content-Type: application/json');

if (!is_logged_in() || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Acceso no autorizado o método no válido.']);
    exit();
}

// Directorio para guardar las evidencias del checklist
$upload_dir = dirname(__DIR__) . '/uploads/checklist_evidence/';
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0775, true);
}

$pdo->beginTransaction();
try {
    // 1. Validar y recoger datos principales
    $id_supervisor = $_SESSION['user_id'];
    $id_cliente = $_POST['id_cliente'];
    $cedula_auditado = $_POST['cedula_auditado'];
    $id_checklist = $_POST['id_checklist'];
    $hallazgos_generales = $_POST['hallazgos_generales'] ?? '';
    $recomendaciones = $_POST['recomendaciones'] ?? '';

    $stmt_auditado = $pdo->prepare("SELECT ID_Usuario, DocumentoIdentidad FROM Usuarios WHERE DocumentoIdentidad = ?");
    $stmt_auditado->execute([$cedula_auditado]);
    $usuario_auditado = $stmt_auditado->fetch(PDO::FETCH_ASSOC);
    $id_usuario_auditado = $usuario_auditado['ID_Usuario'] ?? null;
    $documento_auditado = $usuario_auditado['DocumentoIdentidad'] ?? $cedula_auditado;
    
    // 2. Insertar el registro principal de la visita
    $stmt_visita = $pdo->prepare(
        "INSERT INTO Visitas (ID_Usuario_Supervisor, ID_Usuario_Auditado, Documento_Auditado, ID_Cliente, ID_Checklist, Hallazgos, Recomendaciones, FechaVisita) 
         VALUES (?, ?, ?, ?, ?, ?, ?, NOW())"
    );
    $stmt_visita->execute([$id_supervisor, $id_usuario_auditado, $documento_auditado, $id_cliente, $id_checklist, $hallazgos_generales, $recomendaciones]);
    $id_visita = $pdo->lastInsertId();

    // 3. Procesar respuestas y evidencias fotográficas
    $respuestas = $_POST['respuestas'] ?? [];
    $stmt_respuesta = $pdo->prepare(
        "INSERT INTO RespuestasChecklist (ID_Visita, ID_Item, Respuesta, RutaEvidencia) VALUES (?, ?, ?, ?)"
    );

    foreach ($respuestas as $id_item => $respuesta) {
        $ruta_evidencia = null;
        if (isset($_FILES['evidencias']['name'][$id_item]) && $_FILES['evidencias']['error'][$id_item] === UPLOAD_ERR_OK) {
            $file = [
                'name' => $_FILES['evidencias']['name'][$id_item],
                'tmp_name' => $_FILES['evidencias']['tmp_name'][$id_item],
                'size' => $_FILES['evidencias']['size'][$id_item],
            ];
            if ($file['size'] > 5 * 1024 * 1024) {
                throw new Exception("El archivo para el ítem $id_item es demasiado grande.");
            }
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $new_filename = 'visita_' . $id_visita . '_item_' . $id_item . '_' . time() . '.' . $extension;
            $destination = $upload_dir . $new_filename;
            if (move_uploaded_file($file['tmp_name'], $destination)) {
                $ruta_evidencia = 'uploads/checklist_evidence/' . $new_filename;
            } else {
                throw new Exception("Error al mover el archivo para el ítem $id_item.");
            }
        }
        $stmt_respuesta->execute([$id_visita, $id_item, $respuesta, $ruta_evidencia]);
    }

    $pdo->commit();
    echo json_encode(['success' => true, 'message' => 'Visita y evidencias guardadas exitosamente.']);
    exit();

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error al guardar la visita: ' . $e->getMessage()]);
    exit();
}
?>