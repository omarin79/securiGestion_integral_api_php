<?php
// pages/gestion-informes-general.php

// Cargar la lista de clientes (puestos de trabajo) para el menú desplegable
try {
    $stmt_clientes = $pdo->query("SELECT ID_Cliente, NombreEmpresa FROM Clientes ORDER BY NombreEmpresa");
    $clientes = $stmt_clientes->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $clientes = []; // Si falla, el select estará vacío pero la página no se romperá
}
?>

<div id="gestion-informes-general-page" class="page-content active">
    <main class="registro-container">
        <section>
            <h1>Gestión de Informes (General)</h1>
            <p>Genere y consulte informes variados del sistema.</p>
            
            <form id="informes-general-form" action="actions/informes_action.php" method="POST" target="_blank">
                
                <label for="informe-tipo">Tipo de Informe:</label>
                <select id="informe-tipo" name="informe-tipo" required>
                    <option value="">Seleccione...</option>
                    <option value="novedades-historicas">Novedades Históricas</option>
                    <option value="visitas-supervision">Visitas de Supervisión</option>
                </select>

                <label for="informe-fecha-inicio">Fecha de Inicio (Opcional):</label>
                <input type="date" id="informe-fecha-inicio" name="informe-fecha-inicio">

                <label for="informe-fecha-fin">Fecha de Fin (Opcional):</label>
                <input type="date" id="informe-fecha-fin" name="informe-fecha-fin">

                <label for="informe-cedula">Cédula del Empleado (Opcional):</label>
                <input type="text" id="informe-cedula" name="informe-cedula" placeholder="Filtrar por C.C. si aplica">

                <label for="informe-puesto">Puesto de Trabajo (Opcional):</label>
                <select id="informe-puesto" name="informe-puesto">
                    <option value="">Todos</option>
                    <?php foreach ($clientes as $cliente): ?>
                        <option value="<?= htmlspecialchars($cliente['ID_Cliente']) ?>">
                            <?= htmlspecialchars($cliente['NombreEmpresa']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <button type="submit">Generar Informe</button>
            </form>
        </section>
    </main>
</div>