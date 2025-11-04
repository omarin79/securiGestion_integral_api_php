<?php
// pages/informes.php

// Definimos los roles que tienen permisos para ver este módulo
define('ROLES_ADMIN_INFORMES', [1, 2, 3]); // 1:Admin, 2:Supervisor, 3:Coordinador
$tiene_permiso = isset($_SESSION['user_rol_id']) && in_array($_SESSION['user_rol_id'], ROLES_ADMIN_INFORMES);

?>
<div id="informes-page" class="page-content active">
    <main class="registro-container">
        <section>
            <h1>Gestión de Informes</h1>
            <p>Seleccione los filtros para generar un nuevo informe.</p>
        </section>

        <?php if ($tiene_permiso): ?>
            <section class="form-section">
                <h2>Informe de Visitas de Supervisión</h2>
                <form action="index.php?page=generar-informe" method="POST" target="_blank">
                    <input type="hidden" name="tipo_informe" value="visitas">

                    <label for="fecha_inicio">Fecha de Inicio:</label>
                    <input type="date" id="fecha_inicio" name="fecha_inicio" required>

                    <label for="fecha_fin">Fecha de Fin:</label>
                    <input type="date" id="fecha_fin" name="fecha_fin" required>

                    <button type="submit" style="margin-top: 20px;">Generar Informe</button>
                </form>
            </section>
        <?php else: ?>
            <div class="acceso-denegado">
                <h2>Acceso Denegado</h2>
                <p>No tienes los permisos necesarios para acceder a este módulo.</p>
            </div>
        <?php endif; ?>
    </main>
</div>