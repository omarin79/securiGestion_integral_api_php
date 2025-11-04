<?php
// pages/reporte_disciplinario.php

// Cargar la lista de clientes (puestos de trabajo) para el menú desplegable
try {
    $stmt_clientes = $pdo->query("SELECT ID_Cliente, NombreEmpresa FROM Clientes ORDER BY NombreEmpresa");
    $clientes = $stmt_clientes->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $clientes = [];
}
?>

<div id="reporte-disciplinario-page" class="page-content active">
    <main class="registro-container">
        <section>
            <h1>Registro de Novedad Disciplinaria</h1>
            <p>Diligencie el formato para registrar un llamado de atención o una falta al reglamento interno de trabajo.</p>
            
            <?php if (isset($_GET['success'])): ?>
                <div class="success-message" style="display:block;"><?php echo htmlspecialchars(urldecode($_GET['success'])); ?></div>
            <?php elseif (isset($_GET['error'])): ?>
                 <div class="error-message" style="display:block;"><?php echo htmlspecialchars(urldecode($_GET['error'])); ?></div>
            <?php endif; ?>
        </section>

        <section class="form-section">
            <form id="form-reporte-disciplinario" action="actions/reporte_disciplinario_action.php" method="POST" enctype="multipart/form-data">

                <label for="puesto-trabajo">Puesto de Trabajo:</label>
                <select id="puesto-trabajo" name="id_cliente" required>
                    <option value="">-- Seleccione un Puesto --</option>
                    <?php foreach ($clientes as $cliente): ?>
                        <option value="<?= htmlspecialchars($cliente['ID_Cliente']) ?>">
                            <?= htmlspecialchars($cliente['NombreEmpresa']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <label for="cedula-unidad">Cédula de la Unidad:</label>
                <input type="text" id="cedula-unidad" name="cedula_unidad" 
                       data-autocomplete-nombre="nombre-unidad" 
                       placeholder="Buscar por cédula..." required>
                
                <label for="nombre-unidad">Nombre de la Unidad:</label>
                <input type="text" id="nombre-unidad" name="nombre_unidad_afectado" readonly>

                <label for="tipo-falta">Tipo de Falta:</label>
                <select id="tipo-falta" name="tipo_falta" required>
                    <option value="">-- Seleccione el tipo --</option>
                    <option value="Leve">Leve</option>
                    <option value="Grave">Grave</option>
                    <option value="Gravísima">Gravísima</option>
                </select>

                <label for="descripcion-novedad">Descripción de la Novedad / Hechos:</label>
                <textarea id="descripcion-novedad" name="descripcion" rows="5" required></textarea>

                <div id="campo-adjunto" style="display:none; margin-top: 15px;">
                    <label for="foto-documento">Adjuntar Foto del Informe Disciplinario Físico:</label>
                    <input type="file" id="foto-documento" name="foto_documento" accept="image/*">
                </div>

                <hr style="margin: 30px 0;">

                <h3>Firmas de los Involucrados</h3>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="firma-empleado-pad">Firma del Empleado:</label>
                        <div style="border: 1px solid #ccc; border-radius: 5px; max-width:400px;">
                            <canvas id="firma-empleado-pad" width="400" height="200"></canvas>
                        </div>
                        <button type="button" class="limpiar-firma" data-pad="firma-empleado-pad" style="background-color: #6c757d; width: auto; padding: 5px 10px; font-size: 0.8em;">Limpiar Firma</button>
                        <input type="hidden" name="firma_empleado_base64" id="firma_empleado_base64">
                    </div>
                    <div class="form-group">
                        <label for="firma-reporta-pad">Firma de Quien Reporta:</label>
                        <div style="border: 1px solid #ccc; border-radius: 5px; max-width:400px;">
                            <canvas id="firma-reporta-pad" width="400" height="200"></canvas>
                        </div>
                        <button type="button" class="limpiar-firma" data-pad="firma-reporta-pad" style="background-color: #6c757d; width: auto; padding: 5px 10px; font-size: 0.8em;">Limpiar Firma</button>
                        <input type="hidden" name="firma_reporta_base64" id="firma_reporta_base64">
                    </div>
                </div>
                
                <button type="submit" style="margin-top:20px;">Registrar Reporte</button>
            </form>
        </section>
    </main>
</div>
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>