<?php
// pages/nomina.php

// Definimos los roles que tienen permisos administrativos para este módulo
define('ROLES_ADMIN_NOMINA', [1, 2, 3]); // 1:Admin, 2:Supervisor, 3:Coordinador

// Verificamos si el rol del usuario actual está en la lista de permitidos
$es_admin = isset($_SESSION['user_rol_id']) && in_array($_SESSION['user_rol_id'], ROLES_ADMIN_NOMINA);
?>

<div id="nomina-page" class="page-content active">
    <main class="registro-container">
        <section>
            <h1>Módulo de Nómina</h1>
            <p>Descarga de documentos y consulta de novedades de nómina.</p>
        </section>

        <section class="form-section">
            <h2>Descargar Desprendible de Pago</h2>
            <form action="actions/nomina_action.php" method="POST" target="_blank">
                <input type="hidden" name="tipo_documento" value="desprendible">
                
                <?php if ($es_admin): ?>
                    <label>Cédula del Empleado:</label>
                    <input type="text" name="cedula" data-autocomplete-nombre="nombre_empleado_nomina_1" required>
                    <label>Nombre del Empleado:</label>
                    <input type="text" id="nombre_empleado_nomina_1" name="nombre_empleado" readonly>
                <?php else: ?>
                    <label>Cédula del Empleado:</label>
                    <input type="text" name="cedula" value="<?= htmlspecialchars($_SESSION['user_doc'] ?? '') ?>" readonly>
                    <label>Nombre del Empleado:</label>
                    <input type="text" name="nombre_empleado" value="<?= htmlspecialchars(($_SESSION['user_nombre'] ?? '') . ' ' . ($_SESSION['user_apellido'] ?? '')) ?>" readonly>
                <?php endif; ?>

                <label>Periodo:</label>
                <input type="month" name="periodo" required>
                <button type="submit">Descargar Desprendible</button>
            </form>
        </section>

        <hr>

        <section class="form-section">
            <h2>Certificado de Ingresos y Retenciones</h2>
            <form action="actions/nomina_action.php" method="POST" target="_blank">
                <input type="hidden" name="tipo_documento" value="certificado_ingresos">

                <?php if ($es_admin): ?>
                    <label>Cédula del Empleado:</label>
                    <input type="text" name="cedula" data-autocomplete-nombre="nombre_empleado_nomina_2" required>
                    <label>Nombre del Empleado:</label>
                    <input type="text" id="nombre_empleado_nomina_2" name="nombre_empleado" readonly>
                <?php else: ?>
                    <label>Cédula del Empleado:</label>
                    <input type="text" name="cedula" value="<?= htmlspecialchars($_SESSION['user_doc'] ?? '') ?>" readonly>
                    <label>Nombre del Empleado:</label>
                    <input type="text" name="nombre_empleado" value="<?= htmlspecialchars(($_SESSION['user_nombre'] ?? '') . ' ' . ($_SESSION['user_apellido'] ?? '')) ?>" readonly>
                <?php endif; ?>
                
                <label>Año:</label>
                <input type="number" name="anio" min="2020" max="<?php echo date('Y'); ?>" value="<?php echo date('Y')-1; ?>" required>
                <button type="submit">Descargar Certificado</button>
            </form>
        </section>

        <hr style="margin: 40px 0;">

        <section class="form-section">
            <h2>Consultar Documentos y Novedades</h2>
            <form id="form-consulta-nomina">
                <?php if ($es_admin): ?>
                    <label for="cedula-consulta-nomina">Cédula del Empleado:</label>
                    <input type="text" id="cedula-consulta-nomina" name="cedula_consulta" 
                           data-autocomplete-nombre="nombre-empleado-nomina-3" 
                           placeholder="Buscar por cédula..." required>
                    <label for="nombre-empleado-nomina-3">Nombre del Empleado:</label>
                    <input type="text" id="nombre-empleado-nomina-3" name="nombre_empleado" readonly>
                <?php else: ?>
                    <label for="cedula-consulta-nomina">Cédula del Empleado:</label>
                    <input type="text" id="cedula-consulta-nomina" name="cedula_consulta" 
                           value="<?= htmlspecialchars($_SESSION['user_doc'] ?? '') ?>" readonly>
                <?php endif; ?>

                <button type="submit">Consultar</button>
            </form>

            <div id="resultados-nomina-container" style="margin-top: 20px;">
                </div>
        </section>
        </main>
</div>