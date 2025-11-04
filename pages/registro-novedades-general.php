<?php
// pages/registro-novedades-general.php
// Obtenemos la lista de clientes/puestos desde la base de datos
try {
    $stmt_clientes = $pdo->query("SELECT ID_Cliente, NombreEmpresa FROM Clientes ORDER BY NombreEmpresa");
    $clientes = $stmt_clientes->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $clientes = [];
    error_log("Error al cargar clientes para novedades: " . $e->getMessage());
}
$clientes_json = json_encode($clientes);
?>
<div id="registro-novedades-general-page" class="page-content active">
    <main>
        <section>
            <h1>Registro de Novedades</h1>
            <p>Seleccione el tipo de novedad que desea registrar para mostrar el formulario correspondiente.</p>

            <?php if (isset($_GET['success'])): ?>
                <div class="success-message" style="display:block;"><?php echo htmlspecialchars(urldecode($_GET['success'])); ?></div>
            <?php elseif (isset($_GET['error'])): ?>
                 <div class="error-message" style="display:block;"><?php echo htmlspecialchars(urldecode($_GET['error'])); ?></div>
            <?php endif; ?>

            <label for="tipo-novedad-registro">Tipo de Novedad a Registrar:</label>
            <select id="tipo-novedad-registro" name="tipo-novedad-registro" required>
                <option value="">-- Seleccione una opción --</option>
                <option value="ausencia">Ausencia de Unidad (No Evasión)</option>
                <option value="incapacidad">Incapacidad</option>
                <option value="licencia-remunerada">Licencia Remunerada</option>
                <option value="permiso-remunerado">Permiso Remunerado</option>
                <option value="licencia-no-remunerada">Licencia No Remunerada</option>
                <option value="permiso-no-remunerado">Permiso No Remunerado</option>
                <option value="unidad-evadida">Unidad Evadida</option>
                <option value="condicion-insegura">Reporte de Condición Insegura</option>
            </select>

            <div id="novedad-form-container" style="margin-top: 20px; border-top: 1px solid #eee; padding-top: 15px;">
                <p>Seleccione un tipo de novedad para ver el formulario.</p>
            </div>
        </section>
        
        <section>
            <h2>Consultar Novedades Existentes</h2>
            <button id="btn-consultar-novedades" type="button">Cargar Novedades</button>
             <div id="resultados-novedades" style="margin-top: 20px;">
                </div>
        </section>
    </main>
</div>

<script>
    // Pasamos la lista de clientes a JavaScript
    const listaClientes = <?php echo $clientes_json; ?>;
</script>