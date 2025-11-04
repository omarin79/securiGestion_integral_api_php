<?php
// pages/novedades.php

// Obtenemos la lista de clientes/puestos desde la base de datos
try {
    $stmt_clientes = $pdo->query("SELECT ID_Cliente, NombreEmpresa FROM Clientes ORDER BY NombreEmpresa");
    $clientes = $stmt_clientes->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Si hay un error, creamos un array vacío para no detener la aplicación
    $clientes = [];
    error_log("Error al cargar clientes para novedades: " . $e->getMessage());
}

// Convertimos la lista a formato JSON para que JavaScript pueda usarla de forma segura
$clientes_json = json_encode($clientes);
?>

<div id="novedades-page" class="page-content active">
    <main class="registro-container">
        <section>
            <h1>Registro de Novedades</h1>
            <p>Seleccione el tipo de novedad que desea registrar y complete el formulario correspondiente.</p>
        </section>

        <label for="tipo-novedad-registro">Tipo de Novedad:</label>
        <select id="tipo-novedad-registro">
            <option value="">-- Seleccione una opción --</option>
            <option value="ausencia">Ausencia de Unidad</option>
            <option value="incapacidad">Incapacidad</option>
            <option value="licencia-remunerada">Licencia Remunerada</option>
            <option value="permiso-remunerado">Permiso Remunerado</option>
            <option value="licencia-no-remunerada">Licencia No Remunerada</option>
            <option value="permiso-no-remunerado">Permiso No Remunerado</option>
            <option value="unidad-evadida">Unidad Evadida</option>
            <option value="condicion-insegura">Condición Insegura</option>
        </select>
        
        <div id="novedad-form-container" style="margin-top: 20px;">
            <p>Seleccione un tipo de novedad para ver el formulario.</p>
        </div>
        
        <hr style="margin: 40px 0;">

        <section id="consultar-novedades-section">
            <h2>Consultar Últimas Novedades</h2>
            <button id="btn-consultar-novedades" type="button">Cargar Novedades</button>
            <div id="resultados-novedades" style="margin-top: 20px;">
                </div>
        </section>
    </main>
</div>

<script>
    const listaClientes = <?php echo $clientes_json; ?>;
</script>