<?php
// C:\xampp\htdocs\Seguri_gestion_integral_React\actions\novedad_action.php

// Iniciar la sesión para poder verificar que el usuario esté logueado.
session_start();

// Incluir los archivos de conexión a la BD ($pdo) y funciones (is_logged_in).
require_once dirname(__DIR__) . '/includes/db.php';
require_once dirname(__DIR__) . '/includes/functions.php';

// --- Cabeceras de la API ---
// Definir que la respuesta será en formato JSON.
header('Content-Type: application/json');
// Permitir solicitudes desde el frontend de React (CORS).
header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Manejar la solicitud preliminar 'OPTIONS' (necesaria para CORS).
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

// 1. Verificar Seguridad: Solo usuarios logueados y por método POST.
if (!is_logged_in() || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(401); // 401 No Autorizado
    echo json_encode(['success' => false, 'message' => 'Acceso no autorizado o método no válido.']);
    exit();
}

// 2. Definir el directorio de subida para las evidencias.
$upload_dir = dirname(__DIR__) . '/uploads/novedades/';
if (!is_dir($upload_dir)) {
    // Si la carpeta no existe, intentar crearla.
    mkdir($upload_dir, 0775, true);
}

// Iniciar una transacción: si algo falla, se revierte todo.
$pdo->beginTransaction();

try {
    // 3. Recoger datos comunes del formulario (enviados por React como FormData).
    $id_usuario_reporta = $_SESSION['user_id'];
    $tipo_novedad = $_POST['tipo_novedad'];
    // Buscar la cédula en diferentes posibles campos del formulario.
    $documento_afectado = $_POST['cedula'] ?? $_POST['cedula_reportante'] ?? null;

    // 4. Insertar el registro principal en la tabla `Novedades`.
    $stmt_novedad = $pdo->prepare(
        "INSERT INTO Novedades (TipoNovedad, ID_Usuario_Reporta, Documento_Afectado, FechaHoraRegistro, EstadoNovedad)
         VALUES (?, ?, ?, NOW(), 'Abierta')"
    );
    $stmt_novedad->execute([$tipo_novedad, $id_usuario_reporta, $documento_afectado]);
    $id_novedad = $pdo->lastInsertId(); // Obtener el ID de la novedad recién creada.

    // 5. Preparar la consulta para guardar todos los demás campos en `DetallesNovedad`.
    $stmt_detalle = $pdo->prepare("INSERT INTO DetallesNovedad (ID_Novedad, Campo, Valor) VALUES (?, ?, ?)");

    // 6. Recorrer todos los campos de texto del formulario POST.
    foreach ($_POST as $campo => $valor) {
        // Guardar todos los campos excepto el 'tipo_novedad' que ya se usó.
        if ($campo !== 'tipo_novedad' && !empty($valor)) {
            $stmt_detalle->execute([$id_novedad, $campo, $valor]);
        }
    }

    // 7. Procesar cualquier archivo que se haya subido.
    foreach ($_FILES as $campo_archivo => $file) {
        if (isset($file['error']) && $file['error'] === UPLOAD_ERR_OK) {
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $new_filename = "novedad_{$id_novedad}_{$campo_archivo}_" . time() . "." . $extension;
            $destination = $upload_dir . $new_filename;

            if (move_uploaded_file($file['tmp_name'], $destination)) {
                // Si el archivo se mueve con éxito, guardar la ruta en la BD.
                $ruta_relativa = 'uploads/novedades/' . $new_filename;
                $stmt_detalle->execute([$id_novedad, $campo_archivo, $ruta_relativa]);
            }
        }
    }

    // 8. Si todo salió bien, confirmar los cambios en la BD.
    $pdo->commit();
    http_response_code(201); // 201 Creado
    echo json_encode(['success' => true, 'message' => 'Novedad registrada exitosamente.']);
    exit();

} catch (Exception $e) {
    // 9. Si algo falló, revertir la transacción.
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    http_response_code(500); // 500 Error Interno del Servidor
    echo json_encode(['success' => false, 'message' => 'Error al guardar la novedad: ' . $e->getMessage()]);
    exit();
}
?>