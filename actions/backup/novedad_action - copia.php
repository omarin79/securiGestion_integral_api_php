<?php
// C:\xampp\htdocs\securigestion\actions\novedad_action.php (Versión para API)

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

// Directorio para guardar evidencias de novedades
$upload_dir = dirname(__DIR__) . '/uploads/novedades/';
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0775, true);
}

$pdo->beginTransaction();
try {
    // 1. Recoger datos comunes del formulario
    $id_usuario_reporta = $_SESSION['user_id'];
    $tipo_novedad = $_POST['tipo_novedad'];
    $documento_afectado = $_POST['cedula'] ?? $_POST['cedula_reportante'] ?? null;

    // 2. Insertar el registro principal en la tabla `Novedades`
    $stmt_novedad = $pdo->prepare(
        "INSERT INTO Novedades (TipoNovedad, ID_Usuario_Reporta, Documento_Afectado, FechaHoraRegistro, EstadoNovedad) 
         VALUES (?, ?, ?, NOW(), 'Abierta')"
    );
    $stmt_novedad->execute([$tipo_novedad, $id_usuario_reporta, $documento_afectado]);
    $id_novedad = $pdo->lastInsertId();

    // 3. Preparar la consulta para guardar todos los demás campos en `DetallesNovedad`
    $stmt_detalle = $pdo->prepare("INSERT INTO DetallesNovedad (ID_Novedad, Campo, Valor) VALUES (?, ?, ?)");

    // 4. Recorrer todos los campos de texto del formulario
    foreach ($_POST as $campo => $valor) {
        if ($campo !== 'tipo_novedad' && !empty($valor)) {
            $stmt_detalle->execute([$id_novedad, $campo, $valor]);
        }
    }

    // 5. Procesar cualquier archivo que se haya subido
    foreach ($_FILES as $campo_archivo => $file) {
        if (isset($file['error']) && $file['error'] === UPLOAD_ERR_OK) {
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $new_filename = "novedad_{$id_novedad}_{$campo_archivo}_" . time() . "." . $extension;
            $destination = $upload_dir . $new_filename;

            if (move_uploaded_file($file['tmp_name'], $destination)) {
                $ruta_relativa = 'uploads/novedades/' . $new_filename;
                $stmt_detalle->execute([$id_novedad, $campo_archivo, $ruta_relativa]);
            }
        }
    }

    $pdo->commit();
    echo json_encode(['success' => true, 'message' => 'Novedad registrada exitosamente.']);
    exit();

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error al guardar la novedad: ' . $e->getMessage()]);
    exit();
}
?>