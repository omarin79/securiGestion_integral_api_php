<?php
// C:\xampp\htdocs\securigestion\actions\novedad_action.php (Versión Definitiva)

session_start();
require_once '../includes/db.php';
require_once '../includes/functions.php';

// 1. Verificar que el usuario haya iniciado sesión
if (!is_logged_in()) {
    header('Location: ../index.php');
    exit();
}

// 2. Asegurarse de que los datos se envíen por POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // 3. Recoger datos comunes y específicos de los formularios
    $id_usuario_reporta = $_SESSION['user_id'];
    $tipo_novedad_slug = $_POST['tipo_novedad'] ?? '';
    $cedula_afectado = $_POST['cedula'] ?? $_POST['cedula_reportante'] ?? null;
    $descripcion_novedad = $_POST['observaciones'] ?? $_POST['diagnostico'] ?? $_POST['motivo_permiso'] ?? $_POST['circunstancias'] ?? $_POST['descripcion'] ?? 'Sin descripción.';

    // 4. Buscar el ID del tipo de novedad en la base de datos
    $id_tipo_novedad = null;
    try {
        // Busca el tipo de novedad por su nombre (ej: "Unidad Evadida")
        $nombre_busqueda = ucwords(str_replace('_', ' ', $tipo_novedad_slug));
        $stmt_tipo = $pdo->prepare("SELECT ID_TipoNovedad FROM TiposNovedad WHERE NombreNovedad LIKE ?");
        $stmt_tipo->execute([$nombre_busqueda]);
        $tipo_novedad_row = $stmt_tipo->fetch(PDO::FETCH_ASSOC);
        if ($tipo_novedad_row) {
            $id_tipo_novedad = $tipo_novedad_row['ID_TipoNovedad'];
        }
    } catch (PDOException $e) {
        // Manejar error si la tabla no existe o la consulta falla
    }

    if (!$id_tipo_novedad) {
        header('Location: ../index.php?page=registro-novedades-general&error=' . urlencode('Error: Tipo de novedad no encontrado en la base de datos.'));
        exit();
    }

    // 5. Lógica para manejar la subida de archivos (si se envió alguno)
    $ruta_evidencia = null;
    if (!empty($_FILES)) {
        $nombre_campo_archivo = key($_FILES); // Obtiene el nombre del primer input de archivo que se envió
        
        if (isset($_FILES[$nombre_campo_archivo]) && $_FILES[$nombre_campo_archivo]['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES[$nombre_campo_archivo];

            // Validaciones de seguridad
            if ($file['size'] > 5 * 1024 * 1024) { // Límite de 5MB
                header('Location: ../index.php?page=registro-novedades-general&error=' . urlencode('El archivo de evidencia es demasiado grande (máx 5MB).'));
                exit();
            }

            // Mover el archivo a la carpeta /uploads
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $new_filename = 'novedad_' . $tipo_novedad_slug . '_' . time() . '.' . $extension;
            $upload_path = dirname(__DIR__) . '/uploads/' . $new_filename;

            if (move_uploaded_file($file['tmp_name'], $upload_path)) {
                $ruta_evidencia = 'uploads/' . $new_filename; // Ruta para guardar en la BD
            } else {
                header('Location: ../index.php?page=registro-novedades-general&error=' . urlencode('Error del servidor al guardar el archivo. Verifique los permisos de la carpeta /uploads.'));
                exit();
            }
        }
    }

    // 6. Guardar la novedad en la base de datos
    try {
        $sql = "INSERT INTO Novedades (ID_TipoNovedad, ID_Usuario_Reporta, Descripcion, EvidenciaAdjuntaRuta, EstadoNovedad, FechaHoraOcurrencia) 
                VALUES (?, ?, ?, ?, 'Abierta', NOW())";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $id_tipo_novedad,
            $id_usuario_reporta,
            $descripcion_novedad,
            $ruta_evidencia // Será NULL si no se subió archivo, lo cual es correcto
        ]);
        
        $success_message = "Novedad de '" . htmlspecialchars($nombre_busqueda) . "' registrada exitosamente.";
        header('Location: ../index.php?page=registro-novedades-general&success=' . urlencode($success_message));
        exit();

    } catch (PDOException $e) {
        header('Location: ../index.php?page=registro-novedades-general&error=' . urlencode('Error al guardar en la base de datos: ' . $e->getMessage()));
        exit();
    }
} else {
    // Si no es POST, redirigir
    header('Location: ../index.php');
    exit();
}
?>