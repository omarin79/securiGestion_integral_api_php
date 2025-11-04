<?php
// C:\xampp\htdocs\Seguri_gestion_integral_React\actions\login_action.php (Versión FINAL Corregida)

header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

session_start();
// Importante: Este archivo define la variable de conexión $pdo
require_once '../includes/db.php'; 
require_once '../includes/functions.php';

$response = ['success' => false, 'message' => 'Método no válido.'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data['username']) && isset($data['password'])) {
        $documento = trim($data['username']);
        $password = trim($data['password']);

        if (empty($documento) || empty($password)) {
            $response = ['success' => false, 'message' => 'Los campos no pueden estar vacíos.'];
        } else {
            try {
                // CORRECCIÓN: Usar la variable global $pdo directamente
                $stmt = $pdo->prepare("SELECT * FROM Usuarios WHERE DocumentoIdentidad = ?");
                $stmt->execute([$documento]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($user && password_verify($password, $user['ContrasenaHash'])) {
                    $_SESSION['user_id'] = $user['ID_Usuario'];
                    $_SESSION['user_nombre'] = $user['Nombre'] . ' ' . $user['Apellido'];
                    $_SESSION['user_doc'] = $user['DocumentoIdentidad'];
                    $_SESSION['user_foto'] = $user['FotoPerfilRuta'];
                    $_SESSION['user_rol_id'] = $user['ID_Rol'];
                    
                    $response = [
                        'success' => true,
                        'message' => 'Inicio de sesión exitoso.',
                        'user' => [
                            'id' => $user['ID_Usuario'],
                            'nombre' => $_SESSION['user_nombre'],
                            'rol_id' => $user['ID_Rol'],
                            'fotoUrl' => $_SESSION['user_foto']
                        ]
                    ];
                } else {
                    $response = ['success' => false, 'message' => 'Credenciales inválidas.'];
                }
            } catch (PDOException $e) {
                // Enviar un mensaje de error genérico por seguridad
                $response = ['success' => false, 'message' => 'Error al procesar la solicitud.'];
                // Opcional: Registrar el error real en un log del servidor
                // error_log('Error de base de datos: ' . $e->getMessage());
            }
        }
    } else {
        $response = ['success' => false, 'message' => 'Datos de usuario o contraseña no recibidos.'];
    }
}

echo json_encode($response);
exit();
?>