<?php
// generar_programacion.php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once __DIR__ . '/includes/db.php';

echo "<h1>Generador de Programación Mensual de Ejemplo</h1>";

try {
    $pdo->beginTransaction();

    // 1. Obtener IDs de usuarios operativos (Vigilantes, Supervisores, Operadores de Medios)
    $stmt_usuarios = $pdo->query("SELECT ID_Usuario FROM Usuarios WHERE ID_Rol IN (2, 4, 5)");
    $usuarios = $stmt_usuarios->fetchAll(PDO::FETCH_COLUMN);

    // 2. Obtener Clientes/Puestos
    $stmt_clientes = $pdo->query("SELECT ID_Cliente FROM Clientes");
    $clientes = $stmt_clientes->fetchAll(PDO::FETCH_COLUMN);

    if (empty($usuarios) || empty($clientes)) {
        throw new Exception("No hay suficientes usuarios o clientes para generar la programación.");
    }

    // 3. Limpiar la programación del mes actual para evitar duplicados
    $pdo->exec("DELETE FROM Programacion WHERE YEAR(Fecha) = YEAR(CURDATE()) AND MONTH(Fecha) = MONTH(CURDATE())");
    echo "<p>Programación del mes actual eliminada para evitar duplicados.</p>";

    // 4. Generar programación para el mes actual
    $mes = date('m');
    $anio = date('Y');
    $dias_del_mes = cal_days_in_month(CAL_GREGORIAN, $mes, $anio);
    $turnos = ['DIA', 'NOCHE', 'DESCANSO'];
    $stmt_insert = $pdo->prepare(
        "INSERT INTO Programacion (ID_Usuario, ID_Cliente, Fecha, Turno) VALUES (?, ?, ?, ?)"
    );

    foreach ($usuarios as $id_usuario) {
        for ($dia = 1; $dia <= $dias_del_mes; $dia++) {
            $fecha = sprintf('%s-%s-%s', $anio, $mes, str_pad($dia, 2, '0', STR_PAD_LEFT));
            
            // Asignación aleatoria simple
            $turno_aleatorio = $turnos[array_rand($turnos)];
            $cliente_aleatorio = $clientes[array_rand($clientes)];
            
            $stmt_insert->execute([$id_usuario, $cliente_aleatorio, $fecha, $turno_aleatorio]);
        }
    }

    $pdo->commit();
    echo "<h2 style='color:green;'>¡Éxito! Se ha generado la programación de ejemplo para " . count($usuarios) . " usuarios para el mes actual.</h2>";
    echo "<a href='index.php?page=plataforma_operativa'>Volver a la Plataforma Operativa</a>";

} catch (Exception $e) {
    $pdo->rollBack();
    die("<p style='color:red;'><strong>ERROR:</strong> No se pudo completar el proceso. Detalle: " . $e->getMessage() . "</p>");
}
?>