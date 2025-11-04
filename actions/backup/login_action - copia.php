<?php
// C:\xampp\htdocs\securigestion\actions\login_action.php (Versión Híbrida para PHP y React)

// Paso 1: Configurar las cabeceras HTTP para CORS
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
session_start();

require_once '../includes/db.php';

// Paso 2: Leer los datos de entrada
// Intentar leer datos JSON (desde React)
$json_data = file_get_contents('php://input');
$data = json_decode($json_data, true);

// Si no hay datos JSON, usar $_POST (desde el formulario original de PHP)
if (empty($data)) {
    $data = $_POST;
}

$response = ['success' => false, 'message' => 'Método de solicitud no válido.'];
$is_ajax_request = (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($data['username']) && isset($data['password'])) {
        $documento = trim($data['username']);
        $password = trim($data['password']);

        if (empty($documento) || empty($password)) {
            $response = ['success' => false, 'message' => 'Los campos no pueden estar vacíos.'];
        } else {
            try {
                $stmt = $pdo->prepare("SELECT * FROM Usuarios WHERE DocumentoIdentidad = ?");
                $stmt->execute([$documento]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($user && password_verify($password, $user['ContrasenaHash'])) {
                    $_SESSION['user_id'] = $user['ID_Usuario'];
                    $_SESSION['user_nombre'] = $user['Nombre'] . ' ' . $user['Apellido'];
                    $_SESSION['user_doc'] = $user['DocumentoIdentidad'];
                    $_SESSION['user_foto'] = $user['FotoPerfilRuta'];
                    $_SESSION['user_rol_id'] = $user['ID_Rol'];
                    
                    if ($is_ajax_request) {
                        header('Content-Type: application/json');
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
                        header('Location: ../index.php?page=inicio');
                        exit();
                    }
                } else {
                    $response = ['success' => false, 'message' => 'Credenciales inválidas.'];
                }
            } catch (PDOException $e) {
                $response = ['success' => false, 'message' => 'Error de la base de datos.'];
            }
        }
    } else {
        $response = ['success' => false, 'message' => 'Datos de usuario o contraseña no recibidos.'];
    }
}

if ($is_ajax_request) {
    header('Content-Type: application/json');
    echo json_encode($response);
} else {
    // Para el caso original de PHP, si falla, redirigimos con un mensaje de error en la URL
    header('Location: ../index.php?page=login&error=' . urlencode($response['message']));
}
exit();
?>