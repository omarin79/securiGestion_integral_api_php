<?php
// Habilitar la visualización de errores para encontrar cualquier problema
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once dirname(__DIR__) . '/includes/db.php';
require_once dirname(__DIR__) . '/includes/functions.php';

// Establecer la cabecera de la respuesta como JSON
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Método no permitido
    echo json_encode(['success' => false, 'message' => 'Método de solicitud no válido.']);
    exit();
}

// 1. Recoger todos los datos del formulario, incluyendo la fecha
$nombre = trim($_POST['nombre'] ?? '');
$apellido = trim($_POST['apellido'] ?? '');
$documento = trim($_POST['documento'] ?? '');
$telefono = trim($_POST['telefono'] ?? null);
$direccion = trim($_POST['direccion'] ?? null);
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$password_confirm = $_POST['password_confirm'] ?? '';
$id_rol = $_POST['id_rol'] ?? '';
$fecha_contratacion = $_POST['fecha_contratacion'] ?? null;

// Validar que los campos obligatorios no estén vacíos
if (empty($nombre) || empty($apellido) || empty($documento) || empty($email) || empty($password) || empty($id_rol)) {
    http_response_code(400); // Bad Request
    echo json_encode(['success' => false, 'message' => 'Por favor, complete todos los campos obligatorios.']);
    exit();
}

// Verificar que las contraseñas coincidan
if ($password !== $password_confirm) {
    http_response_code(400); // Bad Request
    echo json_encode(['success' => false, 'message' => 'Las contraseñas no coinciden.']);
    exit();
}

// Hashear la contraseña de forma segura
$password_hashed = password_hash($password, PASSWORD_BCRYPT);

// Manejar la subida de la foto de perfil
$foto_ruta_relativa = null;
if (isset($_FILES['foto_perfil']) && $_FILES['foto_perfil']['error'] == 0) {
    $upload_dir_absoluta = dirname(__DIR__) . '/uploads/perfiles/';
    if (!is_dir($upload_dir_absoluta)) {
        mkdir($upload_dir_absoluta, 0775, true);
    }
    
    $file_extension = strtolower(pathinfo($_FILES['foto_perfil']['name'], PATHINFO_EXTENSION));
    $foto_nombre = 'perfil_' . $documento . '_' . time() . '.' . $file_extension;
    $ruta_absoluta_destino = $upload_dir_absoluta . $foto_nombre;
    $foto_ruta_relativa = 'uploads/perfiles/' . $foto_nombre;

    if (!move_uploaded_file($_FILES['foto_perfil']['tmp_name'], $ruta_absoluta_destino)) {
        http_response_code(500); // Internal Server Error
        echo json_encode(['success' => false, 'message' => 'Error al subir la foto de perfil.']);
        exit();
    }
}

// Iniciar transacción para asegurar la integridad de los datos
$pdo->beginTransaction();

try {
    // Insertar el nuevo usuario en la tabla `Usuarios`, incluyendo la fecha
    $stmt_usuario = $pdo->prepare("
        INSERT INTO Usuarios 
        (Nombre, Apellido, DocumentoIdentidad, CorreoElectronico, ContrasenaHash, Telefono, Direccion, FechaContratacion, ID_Rol, FotoPerfilRuta)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt_usuario->execute([
        $nombre,
        $apellido,
        $documento,
        $email,
        $password_hashed,
        $telefono,
        $direccion,
        $fecha_contratacion,
        $id_rol,
        $foto_ruta_relativa
    ]);

    // --- NUEVO CÓDIGO AÑADIDO: Insertar contrato por defecto para el nuevo usuario ---
    $nuevo_usuario_id = $pdo->lastInsertId(); // Obtener el ID del usuario recién insertado

    // Puedes establecer aquí los valores por defecto para el contrato
    $fecha_inicio_contrato = date('Y-m-d'); // Fecha actual como inicio del contrato
    $tipo_contrato_default = 'Término Indefinido';
    $salario_base_default = 0.00; // Puedes establecer un salario por defecto o pedirlo en el formulario de registro

    $stmt_contrato = $pdo->prepare("
        INSERT INTO Contratos (ID_Usuario, FechaInicio, FechaFin, TipoContrato, SalarioBase)
        VALUES (?, ?, ?, ?, ?)
    ");
    $stmt_contrato->execute([
        $nuevo_usuario_id,
        $fecha_inicio_contrato,
        null, // FechaFin NULL para contrato indefinido
        $tipo_contrato_default,
        $salario_base_default
    ]);
    // --- FIN NUEVO CÓDIGO AÑADIDO ---

    // Sincronizar con la tabla `personal_autocompletar`
    $stmt_rol_nombre = $pdo->prepare("SELECT NombreRol FROM Roles WHERE ID_Rol = ?");
    $stmt_rol_nombre->execute([$id_rol]);
    $rol_info = $stmt_rol_nombre->fetch(PDO::FETCH_ASSOC);
    $cargo = $rol_info ? $rol_info['NombreRol'] : 'No definido';

    $stmt_autocompletar = $pdo->prepare("
        INSERT INTO personal_autocompletar (nombre_completo, documento, email, cargo)
        VALUES (?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE nombre_completo = VALUES(nombre_completo), email = VALUES(email), cargo = VALUES(cargo)
    ");
    $stmt_autocompletar->execute([
        $nombre . ' ' . $apellido,
        $documento,
        $email,
        $cargo
    ]);

    // Si todo fue exitoso, confirmar la transacción
    $pdo->commit();

    // Enviar una respuesta JSON de éxito al frontend
    echo json_encode(['success' => true, 'message' => '¡Registro exitoso! Ya puedes iniciar sesión.']);
    exit();

} catch (PDOException $e) {
    // Si algo falla, revertir todo y enviar un error JSON
    $pdo->rollBack();

    http_response_code(500); // Internal Server Error
    if ($e->errorInfo[1] == 1062) {
        echo json_encode(['success' => false, 'message' => 'El documento o el correo ya están registrados.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al registrar el usuario: ' . $e->getMessage()]);
    }
    exit();
}
?>