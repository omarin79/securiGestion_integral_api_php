<?php
// generar_pagos.php - Herramienta para crear registros de nómina de ejemplo para todos los usuarios en un periodo específico.

ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/includes/db.php';

// --- INICIO DE LA PÁGINA HTML ---
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Generador de Registros de Nómina</title>
    <style>
        body { font-family: sans-serif; padding: 20px; line-height: 1.6; }
        .container { max-width: 800px; margin: auto; border: 1px solid #ddd; padding: 20px; border-radius: 5px; }
        h1, h2 { text-align: center; }
        form { display: flex; justify-content: center; align-items: center; gap: 15px; margin-bottom: 20px; }
        input[type="month"], button { padding: 10px; font-size: 16px; }
        .log { border-top: 1px solid #ddd; padding-top: 15px; }
        .success { color: green; font-weight: bold; }
        .error { color: red; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Generador Masivo de Registros de Nómina</h1>
        <p>Esta herramienta creará registros de pago de ejemplo para todos los empleados en la base de datos para el periodo que selecciones.</p>
        
        <form method="POST" action="">
            <label for="periodo">Selecciona el Periodo (Año y Mes):</label>
            <input type="month" id="periodo" name="periodo" required>
            <button type="submit">Generar Pagos</button>
        </form>

        <div class="log">
<?php
// --- INICIO DE LA LÓGICA PHP ---

// Esta parte solo se ejecuta cuando se envía el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $periodo = $_POST['periodo'] ?? null;
    if (empty($periodo)) {
        die("<p class='error'>Error: Debes seleccionar un periodo.</p>");
    }
    
    // Formateamos la fecha para guardarla en la base de datos (siempre el día 01)
    $fecha_periodo = $periodo . '-01';
    
    echo "<p>Iniciando proceso para el periodo: <strong>" . htmlspecialchars($periodo) . "</strong></p><hr>";

    try {
        $pdo->beginTransaction();

        $stmt_usuarios = $pdo->query("SELECT ID_Usuario, DocumentoIdentidad FROM Usuarios WHERE ID_Rol > 1");
        $usuarios = $stmt_usuarios->fetchAll(PDO::FETCH_ASSOC);

        if (empty($usuarios)) {
            die("<p class='error'>No se encontraron empleados para generar pagos.</p>");
        }

        $pagos_creados = 0;
        $stmt_insert = $pdo->prepare(
            "INSERT INTO PagosNomina (ID_Usuario, Periodo, SalarioBase, HorasExtras, Bonificaciones, AporteSalud, AportePension, OtrasDeducciones, TotalDevengado, TotalDeducido, NetoAPagar) 
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );

        foreach ($usuarios as $usuario) {
            // Lógica de ejemplo para calcular el pago
            $salario_base = 1300000;
            $horas_extras = rand(50000, 250000);
            $bonificaciones = rand(0, 100000);
            $aporte_salud = $salario_base * 0.04;
            $aporte_pension = $salario_base * 0.04;
            $otras_deducciones = rand(10000, 50000);
            $total_devengado = $salario_base + $horas_extras + $bonificaciones;
            $total_deducido = $aporte_salud + $aporte_pension + $otras_deducciones;
            $neto_a_pagar = $total_devengado - $total_deducido;

            $stmt_insert->execute([
                $usuario['ID_Usuario'], $fecha_periodo, $salario_base, $horas_extras, $bonificaciones,
                $aporte_salud, $aporte_pension, $otras_deducciones, $total_devengado, $total_deducido, $neto_a_pagar
            ]);
            
            $pagos_creados++;
            echo "✅ Pago creado para el empleado con cédula: " . htmlspecialchars($usuario['DocumentoIdentidad']) . "<br>";
        }

        $pdo->commit();
        echo "<hr><h2 class='success'>¡Proceso completado!</h2>";
        echo "<p><strong>Total de registros de pago creados:</strong> $pagos_creados</p>";

    } catch (PDOException $e) {
        $pdo->rollBack();
        die("<p class='error'><strong>ERROR:</strong> No se pudo completar el proceso. Detalle: " . $e->getMessage() . "</p>");
    }
}
?>
        </div>
    </div>
</body>
</html>