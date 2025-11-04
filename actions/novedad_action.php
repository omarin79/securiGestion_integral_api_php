<?php
<<<<<<< HEAD
// C:\xampp\htdocs\Seguri_gestion_integral_React\actions\novedad_action.php
=======
// actions/novedad_action.php (Versión con Mensajes de Éxito/Error)

ini_set('display_errors', 1);
error_reporting(E_ALL);
>>>>>>> 3777558850002e07be6778636564d2dff0d0c724

session_start();
// Incluir los archivos de conexión a la BD ($pdo) y funciones (is_logged_in).
require_once dirname(__DIR__) . '/includes/db.php';
require_once dirname(__DIR__) . '/includes/functions.php';

// --- Cabeceras de la API ---
// Definir que la respuesta será en formato JSON.
header('Content-Type: application/json');
// Permitir solicitudes desde cualquier origen (para que Postman funcione).
header("Access-Control-Allow-Origin: *");
// Permitir los métodos POST y OPTIONS (para la solicitud 'preflight' de CORS).
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Manejar la solicitud preliminar 'OPTIONS' (necesaria para CORS).
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

<<<<<<< HEAD
// 1. Verificar Seguridad: (Temporalmente desactivado para pruebas de Postman)
// Comentamos este bloque para que Postman (que no ha iniciado sesión) pueda crear un registro.
/*
if (!is_logged_in() || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(401); // 401 No Autorizado
    echo json_encode(['success' => false, 'message' => 'Acceso no autorizado o método no válido.']);
    exit();
}
*/

// 2. Definir el directorio de subida para las evidencias.
=======
>>>>>>> 3777558850002e07be6778636564d2dff0d0c724
$upload_dir = dirname(__DIR__) . '/uploads/novedades/';
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0775, true);
}

// Iniciar una transacción: si algo falla, se revierte todo.
$pdo->beginTransaction();
<<<<<<< HEAD
=======
try {
    // 1. Recoger datos comunes del formulario
    $id_usuario_reporta = $_SESSION['user_id'];
    $tipo_novedad = $_POST['tipo_novedad'];
    $documento_afectado = $_POST['cedula'] ?? $_POST['cedula_reportante'] ?? null;
>>>>>>> 3777558850002e07be6778636564d2dff0d0c724

try {
    // 3. Recoger datos (se esperan como 'form-data' desde Postman).
    
    // --- ¡CORRECCIÓN APLICADA AQUÍ! ---
    // Usamos el ID de usuario '2', que SÍ existe en tu tabla 'usuarios'.
    $id_usuario_reporta = 2; 
    
    $tipo_novedad = $_POST['tipo_novedad'] ?? 'N/A';
    $documento_afectado = $_POST['cedula'] ?? null;

    // 4. Insertar el registro principal en la tabla `Novedades`.
    $stmt_novedad = $pdo->prepare(
        "INSERT INTO Novedades (TipoNovedad, ID_Usuario_Reporta, Documento_Afectado, FechaHoraRegistro, EstadoNovedad)
         VALUES (?, ?, ?, NOW(), 'Abierta')"
    );
    $stmt_novedad->execute([$tipo_novedad, $id_usuario_reporta, $documento_afectado]);
    $id_novedad = $pdo->lastInsertId(); // Obtener el ID de la novedad recién creada.

<<<<<<< HEAD
    // 5. Preparar la consulta para guardar todos los demás campos en `DetallesNovedad`.
    $stmt_detalle = $pdo->prepare("INSERT INTO DetallesNovedad (ID_Novedad, Campo, Valor) VALUES (?, ?, ?)");

    // 6. Recorrer todos los campos de texto del formulario POST.
    foreach ($_POST as $campo => $valor) {
        if ($campo !== 'tipo_novedad') { // Evitar duplicar el campo
=======
    // 3. Preparar la consulta para guardar los detalles
    $stmt_detalle = $pdo->prepare("INSERT INTO DetallesNovedad (ID_Novedad, Campo, Valor) VALUES (?, ?, ?)");

    // 4. Recorrer y guardar todos los demás campos del formulario
    foreach ($_POST as $campo => $valor) {
        if ($campo !== 'tipo_novedad' && !empty($valor)) {
>>>>>>> 3777558850002e07be6778636564d2dff0d0c724
            $stmt_detalle->execute([$id_novedad, $campo, $valor]);
        }
    }

    // 7. Procesar cualquier archivo que se haya subido (leídos desde $_FILES).
    foreach ($_FILES as $campo_archivo => $file) {
        if (isset($file['error']) && $file['error'] === UPLOAD_ERR_OK) {
            // Se crea un nombre único para el archivo.
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $new_filename = "novedad_{$id_novedad}_{$campo_archivo}_" . time() . "." . $extension;
            $destination = $upload_dir . $new_filename;

            if (move_uploaded_file($file['tmp_name'], $destination)) {
<<<<<<< HEAD
                // Si el archivo se guarda en el servidor, se guarda la ruta en la BD.
=======
>>>>>>> 3777558850002e07be6778636564d2dff0d0c724
                $ruta_relativa = 'uploads/novedades/' . $new_filename;
                $stmt_detalle->execute([$id_novedad, $campo_archivo, $ruta_relativa]);
            }
        }
    }

<<<<<<< HEAD
    // 8. Si todo salió bien, confirmar los cambios en la BD.
    $pdo->commit();
    http_response_code(201); // 201 "Created" (El estándar para un POST exitoso).
    echo json_encode(['success' => true, 'message' => 'Novedad registrada exitosamente desde Postman.']);
    exit();

} catch (Exception $e) {
    // 9. Si algo falló, revertir la transacción.
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    http_response_code(500); // 500 Error Interno del Servidor
    echo json_encode(['success' => false, 'message' => 'Error al guardar la novedad: ' . $e->getMessage()]);
=======
    $pdo->commit();
    // Redirigir con mensaje de éxito a la página correcta
    header('Location: ../index.php?page=registro-novedades-general&success=' . urlencode('Novedad #' . $id_novedad . ' registrada exitosamente.'));
    exit();

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    // Redirigir con mensaje de error a la página correcta
    header('Location: ../index.php?page=registro-novedades-general&error=' . urlencode('Error al guardar la novedad: ' . $e->getMessage()));
>>>>>>> 3777558850002e07be6778636564d2dff0d0c724
    exit();
}
?>