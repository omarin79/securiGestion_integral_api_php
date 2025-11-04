<?php
// C:\xampp\htdocs\securigestion\actions\update_perfil_action.php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
require_once dirname(__DIR__) . '/includes/db.php';
require_once dirname(__DIR__) . '/includes/functions.php';

// Establecer la cabecera de la respuesta como JSON
header('Content-Type: application/json');

if (!is_logged_in() || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(401); // No autorizado
    echo json_encode(['success' => false, 'message' => 'Acceso no autorizado o método no válido.']);
    exit();
}

$user_id = $_SESSION['user_id'];

// Leer y decodificar los datos JSON enviados por React
$json_data = file_get_contents('php://input');
$data = json_decode($json_data, true);

// 1. Recoger datos del formulario
$nombre = trim($data['nombre'] ?? '');
$apellido = trim($data['apellido'] ?? '');
$telefono = trim($data['telefono'] ?? null);
$direccion = trim($data['direccion'] ?? null);
$email = trim($data['email'] ?? '');
$password = $data['password'] ?? '';
$password_confirm = $data['password_confirm'] ?? '';

// 2. Construir la consulta SQL dinámicamente
$fields = [
    'Nombre' => $nombre,
    'Apellido' => $apellido,
    'Telefono' => $telefono,
    'Direccion' => $direccion,
    'CorreoElectronico' => $email
];
$query_parts = [];
$params = [];

foreach ($fields as $key => $value) {
    if ($value !== null) { // Solo incluir campos que no son nulos
        $query_parts[] = "$key = ?";
        $params[] = $value;
    }
}

// 3. Manejar el cambio de contraseña (solo si se proporcionó una nueva)
if (!empty($password)) {
    if ($password !== $password_confirm) {
        http_response_code(400); // Bad Request
        echo json_encode(['success' => false, 'message' => 'Las nuevas contraseñas no coinciden.']);
        exit();
    }
    $query_parts[] = "ContrasenaHash = ?";
    $params[] = password_hash($password, PASSWORD_BCRYPT);
}

// 4. Ejecutar la actualización en la base de datos
if (count($query_parts) > 0) {
    $sql = "UPDATE Usuarios SET " . implode(', ', $query_parts) . " WHERE ID_Usuario = ?";
    $params[] = $user_id;

    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        // Actualizar el nombre en la sesión de PHP (opcional, si otras partes del backend lo usan)
        $_SESSION['user_nombre'] = $nombre . ' ' . $apellido;

        echo json_encode(['success' => true, 'message' => '¡Perfil actualizado exitosamente!']);
        exit();

    } catch (PDOException $e) {
        http_response_code(500); // Internal Server Error
        echo json_encode(['success' => false, 'message' => 'Error al actualizar el perfil: ' . $e->getMessage()]);
        exit();
    }
} else {
    // No se envió ningún dato para actualizar
    http_response_code(400); // Bad Request
    echo json_encode(['success' => false, 'message' => 'No se proporcionaron datos para actualizar.']);
    exit();
}
?>