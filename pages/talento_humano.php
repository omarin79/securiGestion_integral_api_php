<?php
// pages/talento_humano.php

// Definimos los roles que tienen permisos administrativos para este módulo
define('ROLES_ADMIN', [1, 2, 3]); // 1:Admin, 2:Supervisor, 3:Coordinador
$es_admin = isset($_SESSION['user_rol_id']) && in_array($_SESSION['user_rol_id'], ROLES_ADMIN);

?>
<div id="talento-humano-page" class="page-content active">
    <main class="registro-container">
        <section>
            <h1>Gestión de Talento Humano</h1>
            <p>Seleccione el tipo de documento que desea generar o consultar.</p>
        </section>

        <section class="form-section">
            <h2>Generar Documentos</h2>
            <form id="form-generar-documento-th" action="actions/generar_documento_action.php" method="POST" target="_blank">
                <input type="hidden" name="tipo_documento" value="carta_laboral">

                <?php if ($es_admin): ?>
                    <label for="cedula-empleado">Cédula del Empleado:</label>
                    <input type="text" id="cedula-empleado" name="cedula_empleado" data-autocomplete-nombre="nombre-empleado-th" placeholder="Buscar por cédula..." required>
                    <label for="nombre-empleado-th">Nombre del Empleado:</label>
                    <input type="text" id="nombre-empleado-th" name="nombre_empleado" readonly>
                <?php else: ?>
                    <label for="cedula-empleado">Cédula del Empleado:</label>
                    <input type="text" id="cedula-empleado" name="cedula_empleado" value="<?= htmlspecialchars($_SESSION['user_doc'] ?? '') ?>" readonly>
                    <label for="nombre-empleado-th">Nombre del Empleado:</label>
                    <input type="text" id="nombre-empleado-th" name="nombre_empleado" value="<?= htmlspecialchars(($_SESSION['user_nombre'] ?? '') . ' ' . ($_SESSION['user_apellido'] ?? '')) ?>" readonly>
                <?php endif; ?>

                <label for="tipo_salario">Opción de Salario a Incluir:</label>
                <select id="tipo_salario" name="tipo_salario" required>
                    <option value="con_salario_basico">Mostrar salario básico</option>
                    <option value="con_salario_extras">Mostrar salario básico más extras</option>
                    <option value="sin_salario">No mostrar salario</option>
                </select>

                <label for="dirigido_a">Dirigida a:</label>
                <input type="text" id="dirigido_a" name="dirigido_a" value="A quien interese">

                <button type="submit" style="margin-top: 15px;">Generar Carta</button>
            </form>
        </section>

        <hr style="margin: 40px 0;">

        <section class="form-section">
            <h2>Consultar Documentos Solicitados</h2>
            <form id="form-consultar-documentos-th">
                <?php if ($es_admin): ?>
                    <label for="th-cedula-consulta-docs">Cédula del Empleado:</label>
                    <input type="text" id="th-cedula-consulta-docs" name="cedula_empleado_consulta_docs" 
                           data-autocomplete-nombre="th-nombre-consulta-docs" 
                           placeholder="Ingrese cédula para consultar" autocomplete="off" required>
                    <label for="th-nombre-consulta-docs">Nombre del Empleado:</label>
                    <input type="text" id="th-nombre-consulta-docs" name="nombre_empleado_consulta_docs" readonly>
                <?php else: ?>
                    <label for="th-cedula-consulta-docs">Cédula del Empleado:</label>
                    <input type="text" id="th-cedula-consulta-docs" name="cedula_empleado_consulta_docs" 
                           value="<?= htmlspecialchars($_SESSION['user_doc'] ?? '') ?>" readonly>
                <?php endif; ?>

                <button type="submit">Consultar Documentos</button>
            </form>
            <div id="resultados-documentos" style="margin-top: 20px;">
                </div>
        </section>
        </main>
</div>