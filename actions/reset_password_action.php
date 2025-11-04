<?php
// C:\xampp\htdocs\securigestion\actions\reset_password_action.php (Código Refactorizado para API)

require_once dirname(__DIR__) . '/includes/db.php';

// Establecer la cabecera de la respuesta como JSON
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido.']);
    exit();
}

$json_data = file_get_contents('php://input');
$data = json_decode($json_data, true);

$token = $data['token'] ?? '';
$new_password = $data['new_password'] ?? '';
$confirm_password = $data['password_confirm'] ?? '';

if (empty($token) || empty($new_password) || empty($confirm_password)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Todos los campos son obligatorios.']);
    exit();
}

if ($new_password !== $confirm_password) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Las contraseñas no coinciden.']);
    exit();
}

try {
    // 1. Buscar el usuario por el token y verificar que no haya expirado
    $stmt = $pdo->prepare("SELECT * FROM Usuarios WHERE reset_token = ? AND reset_token_expires_at > NOW()");
    $stmt->execute([$token]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // 2. Si el token es válido, actualizar la contraseña
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        
        // 3. Limpiar el token para que no se pueda volver a usar
        $stmt_update = $pdo->prepare("UPDATE Usuarios SET ContrasenaHash = ?, reset_token = NULL, reset_token_expires_at = NULL WHERE ID_Usuario = ?");
        $stmt_update->execute([$hashed_password, $user['ID_Usuario']]);

        echo json_encode(['success' => true, 'message' => 'Su contraseña ha sido actualizada exitosamente.']);
        exit();
    } else {
        // Si el token es inválido o ha expirado
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'El token es inválido o ha expirado.']);
        exit();
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error de la base de datos: ' . $e->getMessage()]);
    exit();
}
?>