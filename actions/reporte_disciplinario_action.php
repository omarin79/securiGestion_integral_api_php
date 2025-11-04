<?php
// actions/reporte_disciplinario_action.php (Versión con Firmas Múltiples)

ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
require_once dirname(__DIR__) . '/includes/db.php';
require_once dirname(__DIR__) . '/includes/functions.php';

if (!is_logged_in() || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../index.php');
    exit();
}

// Directorios para los adjuntos
$upload_dir_disciplinarios = dirname(__DIR__) . '/uploads/disciplinarios/';
$upload_dir_firmas = dirname(__DIR__) . '/uploads/firmas/';
if (!is_dir($upload_dir_disciplinarios)) mkdir($upload_dir_disciplinarios, 0777, true);
if (!is_dir($upload_dir_firmas)) mkdir($upload_dir_firmas, 0777, true);

// Función auxiliar para guardar firmas
function guardar_firma($base64_string, $upload_dir, $prefix) {
    if (empty($base64_string)) return null;
    $data_uri = $base64_string;
    $encoded_image = explode(",", $data_uri)[1];
    $decoded_image = base64_decode($encoded_image);
    $firma_filename = "{$prefix}_" . time() . ".png";
    file_put_contents($upload_dir . $firma_filename, $decoded_image);
    return 'uploads/firmas/' . $firma_filename;
}

$pdo->beginTransaction();
try {
    // 1. Recoger datos del formulario
    $id_usuario_reporta = $_SESSION['user_id'];
    $documento_afectado = $_POST['cedula_unidad'];
    $nombre_afectado = $_POST['nombre_unidad_afectado'];
    $nombre_reporta = $_SESSION['user_nombre'];

    // 2. Insertar el registro principal en Novedades
    $stmt_novedad = $pdo->prepare(
        "INSERT INTO Novedades (TipoNovedad, ID_Usuario_Reporta, Documento_Afectado, FechaHoraRegistro, EstadoNovedad) VALUES (?, ?, ?, NOW(), 'Abierta')"
    );
    $stmt_novedad->execute(['Novedad Disciplinaria', $id_usuario_reporta, $documento_afectado]);
    $id_novedad = $pdo->lastInsertId();

    // 3. Guardar detalles
    $stmt_detalle = $pdo->prepare("INSERT INTO DetallesNovedad (ID_Novedad, Campo, Valor) VALUES (?, ?, ?)");
    $stmt_detalle->execute([$id_novedad, 'puesto_de_trabajo_id', $_POST['id_cliente']]);
    $stmt_detalle->execute([$id_novedad, 'tipo_de_falta', $_POST['tipo_falta']]);
    $stmt_detalle->execute([$id_novedad, 'descripcion_hechos', $_POST['descripcion']]);

    // 4. Guardar nombres de los firmantes
    $stmt_detalle->execute([$id_novedad, 'nombre_empleado_afectado', $nombre_afectado]);
    $stmt_detalle->execute([$id_novedad, 'nombre_quien_reporta', $nombre_reporta]);

    // 5. Procesar y guardar firmas
    $ruta_firma_empleado = guardar_firma($_POST['firma_empleado_base64'], $upload_dir_firmas, "firma_empleado_{$id_novedad}");
    $ruta_firma_reporta = guardar_firma($_POST['firma_reporta_base64'], $upload_dir_firmas, "firma_reporta_{$id_novedad}");
    
    if ($ruta_firma_empleado) $stmt_detalle->execute([$id_novedad, 'ruta_firma_empleado', $ruta_firma_empleado]);
    if ($ruta_firma_reporta) $stmt_detalle->execute([$id_novedad, 'ruta_firma_reporta', $ruta_firma_reporta]);

    // 6. Procesar la foto del informe físico
    if (isset($_FILES['foto_documento']) && $_FILES['foto_documento']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['foto_documento'];
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $new_filename = "reporte_{$id_novedad}_" . time() . "." . $extension;
        $destination = $upload_dir_disciplinarios . $new_filename;
        if (move_uploaded_file($file['tmp_name'], $destination)) {
            $stmt_detalle->execute([$id_novedad, 'informe_adjunto_ruta', 'uploads/disciplinarios/' . $new_filename]);
        }
    }

    $pdo->commit();
    header('Location: ../index.php?page=consulta_reportes&success=' . urlencode('Reporte disciplinario #' . $id_novedad . ' ha sido guardado.'));
    exit();

} catch (Exception $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    header('Location: ../index.php?page=reporte_disciplinario&error=' . urlencode('Error al guardar: ' . $e->getMessage()));
    exit();
}
?>