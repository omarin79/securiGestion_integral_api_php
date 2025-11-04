<?php
// actions/consulta_visitas_action.php (Versión Final que une ambas tablas)

ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
require_once dirname(__DIR__) . '/includes/db.php';
require_once dirname(__DIR__) . '/includes/functions.php';

if (!is_logged_in()) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'No autorizado']);
    exit();
}

try {
    $stmt = $pdo->prepare("
        SELECT 
            v.ID_Visita,
            v.FechaVisita,
            COALESCE(CONCAT(sup.Nombre, ' ', sup.Apellido), 'Supervisor no encontrado') AS Supervisor,
            -- Lógica para obtener el nombre del auditado desde cualquiera de las dos tablas
            COALESCE(CONCAT(aud.Nombre, ' ', aud.Apellido), pa.nombre_completo, 'Auditado no encontrado') AS Auditado,
            COALESCE(cli.NombreEmpresa, 'Cliente no encontrado') AS Cliente,
            COALESCE(chk.NombreChecklist, 'Checklist no encontrado') AS Checklist
        FROM Visitas v
        LEFT JOIN Usuarios sup ON v.ID_Usuario_Supervisor = sup.ID_Usuario
        LEFT JOIN Clientes cli ON v.ID_Cliente = cli.ID_Cliente
        LEFT JOIN Checklists chk ON v.ID_Checklist = chk.ID_Checklist
        -- Unir con Usuarios si el ID_Usuario_Auditado no es nulo
        LEFT JOIN Usuarios aud ON v.ID_Usuario_Auditado = aud.ID_Usuario
        -- Unir con personal_autocompletar si el ID_Usuario_Auditado es nulo
        LEFT JOIN personal_autocompletar pa ON v.Documento_Auditado = pa.documento
        ORDER BY v.FechaVisita DESC
        LIMIT 50
    ");
    
    $stmt->execute();
    $visitas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    header('Content-Type: application/json');
    echo json_encode($visitas);

} catch (PDOException $e) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Error en la consulta a la base de datos: ' . $e->getMessage()]);
}
?>