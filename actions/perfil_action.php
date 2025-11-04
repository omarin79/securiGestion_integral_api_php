<?php
// C:\xampp\htdocs\securigestion\actions\perfil_action.php (Versión para API)

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

$user_id = $_SESSION['user_id'];

if (isset($_FILES['foto_perfil']) && $_FILES['foto_perfil']['error'] === UPLOAD_ERR_OK) {
    $file = $_FILES['foto_perfil'];

    // Validación de tamaño (hasta 5MB)
    if ($file['size'] > 5 * 1024 * 1024) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Error: El archivo no debe superar los 5MB.']);
        exit();
    }

    // Validación de tipo de archivo
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime_type = finfo_file($finfo, $file['tmp_name']);
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
    finfo_close($finfo);

    if (!in_array($mime_type, $allowed_types)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Error: Tipo de archivo no permitido (solo JPG, PNG, GIF).']);
        exit();
    }

    // Crear nombre de archivo único
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $new_filename = 'user_' . $user_id . '_' . time() . '.' . $extension;
    $upload_path = dirname(__DIR__) . '/uploads/' . $new_filename;

    // Mover el archivo
    if (move_uploaded_file($file['tmp_name'], $upload_path)) {
        $db_path = 'uploads/' . $new_filename;
        $stmt = $pdo->prepare("UPDATE Usuarios SET FotoPerfilRuta = ? WHERE ID_Usuario = ?");
        $stmt->execute([$db_path, $user_id]);
        $_SESSION['user_foto'] = $db_path;

        echo json_encode(['success' => true, 'message' => '¡Foto de perfil actualizada con éxito!', 'foto_url' => $db_path]);
        exit();
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Error del servidor: No se pudo guardar la imagen.']);
        exit();
    }
} else {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Error: No se seleccionó un archivo o hubo un problema con la subida.']);
    exit();
}
?>