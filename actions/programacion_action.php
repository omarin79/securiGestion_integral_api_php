<?php
// actions/programacion_action.php (Version Correcta y Final)

ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
require_once dirname(__DIR__) . '/includes/db.php';
require_once dirname(__DIR__) . '/includes/functions.php';

header('Content-Type: application/json');

if (!is_logged_in() || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['error' => 'Acceso no autorizado.']);
    exit();
}

$action = $_POST['action'] ?? '';

if ($action === 'buscar_reemplazo') {
    $cedula_ausente = $_POST['cedula_ausente'] ?? '';
    $fecha_turno = $_POST['fecha_turno'] ?? '';
    $id_cliente = $_POST['id_cliente'] ?? '';
    $id_usuario_reporta = $_SESSION['user_id'];

    if (empty($cedula_ausente) || empty($fecha_turno) || empty($id_cliente)) {
        echo json_encode(['error' => 'Faltan datos para registrar la ausencia.']);
        exit();
    }

    $pdo->beginTransaction();
    try {
        // 1. REGISTRAR LA NOVEDAD DE AUSENCIA (SIN LA COLUMNA 'Descripcion')
        $stmt_novedad = $pdo->prepare(
            "INSERT INTO Novedades (TipoNovedad, ID_Usuario_Reporta, Documento_Afectado, FechaHoraRegistro, EstadoNovedad) 
             VALUES ('Ausencia', ?, ?, NOW(), 'Abierta')"
        );
        $stmt_novedad->execute([$id_usuario_reporta, $cedula_ausente]);
        
        // 2. BUSCAR UNIDADES DISPONIBLES (CON EMAIL)
        $stmt_disponibles = $pdo->prepare("
            SELECT Nombre, Apellido, DocumentoIdentidad, Telefono, CorreoElectronico 
            FROM Usuarios 
            WHERE ID_Rol = 4 AND DocumentoIdentidad != ? 
            LIMIT 5
        ");
        $stmt_disponibles->execute([$cedula_ausente]);
        $unidades_disponibles = $stmt_disponibles->fetchAll(PDO::FETCH_ASSOC);

        $pdo->commit();
        
        echo json_encode($unidades_disponibles);

    } catch (PDOException $e) {
        $pdo->rollBack();
        echo json_encode(['error' => 'Error en la base de datos: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['error' => 'Acción no reconocida.']);
}
?>