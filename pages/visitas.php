<?php
// C:\xampp\htdocs\securigestion\Seguri_gestion_integral_PHP\pages\visitas.php

// Obtener clientes para el desplegable
$stmt_clientes = $pdo->query("SELECT ID_Cliente, NombreEmpresa FROM Clientes ORDER BY NombreEmpresa");
$clientes = $stmt_clientes->fetchAll(PDO::FETCH_ASSOC);

// --- Cargar checklists filtrados por el rol del usuario ---
$checklists = [];
try {
    // Asegúrate de que el ID del rol existe en la sesión
    if (isset($_SESSION['user_rol_id'])) {
        $id_rol_usuario = $_SESSION['user_rol_id'];
        
        $stmt_checklists = $pdo->prepare(
            "SELECT c.ID_Checklist, c.NombreChecklist
             FROM Checklists c
             JOIN Checklists_Roles cr ON c.ID_Checklist = cr.ID_Checklist
             WHERE c.Activo = 1 AND cr.ID_Rol = ?"
        );
        $stmt_checklists->execute([$id_rol_usuario]);
        $checklists = $stmt_checklists->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (PDOException $e) {
    // Si hay un error, el desplegable simplemente estará vacío para no detener la página.
    error_log("Error al cargar checklists por rol: " . $e->getMessage());
    $checklists = [];
}
?>

<div id="visitas-page" class="page-content active">
    <main class="registro-container">
        <section>
            <h1>Registro de Visita de Supervisión</h1>
            <p>Complete el siguiente formulario para registrar una nueva visita y su checklist asociado.</p>
        </section>

        <form id="form-visita-supervision" action="actions/visita_action.php" method="POST" enctype="multipart/form-data">

            <h2>1. Detalles de la Visita</h2>

            <label for="visita-cliente">Cliente Visitado:</label>
            <select id="visita-cliente" name="id_cliente" required>
                <option value="">-- Seleccione un Cliente --</option>
                <?php foreach ($clientes as $cliente): ?>
                    <option value="<?= htmlspecialchars($cliente['ID_Cliente']) ?>">
                        <?= htmlspecialchars($cliente['NombreEmpresa']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="visita-vigilante-cedula">Cédula del Vigilante Auditado:</label>
            <input type="text" id="visita-vigilante-cedula" name="cedula_auditado"
                   data-autocomplete-nombre="visita-vigilante-nombre"
                   placeholder="Buscar por cédula..." autocomplete="off" required>

            <label for="visita-vigilante-nombre">Nombre del Vigilante:</label>
            <input type="text" id="visita-vigilante-nombre" name="nombre_auditado" readonly>

            <hr>

            <h2>2. Checklist de la Visita</h2>
            <label for="visita-checklist-tipo">Tipo de Checklist a Realizar:</label>
            <select id="visita-checklist-tipo" name="id_checklist" required>
                <option value="">-- Seleccione un Checklist --</option>
                <?php if (empty($checklists)): ?>
                    <option value="" disabled>No hay checklists asignados a su rol</option>
                <?php else: ?>
                    <?php foreach ($checklists as $checklist): ?>
                        <option value="<?= htmlspecialchars($checklist['ID_Checklist']) ?>">
                            <?= htmlspecialchars($checklist['NombreChecklist']) ?>
                        </option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>

            <div id="checklist-container" style="margin-top: 20px;">
                </div>

            <hr>

            <h2>3. Hallazgos y Recomendaciones</h2>
            <label for="visita-hallazgos">Hallazgos (Positivos y Negativos):</label>
            <textarea id="visita-hallazgos" name="hallazgos_generales" rows="5"></textarea>

            <label for="visita-recomendaciones">Recomendaciones (Acciones Correctivas y Preventivas):</label>
            <textarea id="visita-recomendaciones" name="recomendaciones" rows="5"></textarea>

            <button type="submit" style="margin-top: 20px;">Guardar Visita y Checklist</button>
        </form>

        <hr style="margin: 40px 0;">

        <section id="consultar-visitas">
            <h2>Consultar Visitas de Supervisión</h2>
            <p>Aquí puedes ver un historial de las últimas visitas registradas en el sistema.</p>
            
            <button id="btn-consultar-visitas" type="button">Cargar Últimas Visitas</button>

            <div id="resultados-visitas-container" style="margin-top: 20px;">
                </div>
        </section>
    </main>
</div>