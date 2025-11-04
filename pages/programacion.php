<?php
// pages/programacion.php

// Cargar la lista de clientes (puestos de trabajo)
try {
    $stmt_clientes = $pdo->query("SELECT ID_Cliente, NombreEmpresa FROM Clientes ORDER BY NombreEmpresa");
    $clientes = $stmt_clientes->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $clientes = [];
    // Manejo básico de errores
    echo "<p class='error-message'>No se pudieron cargar los puestos de trabajo.</p>";
}
?>

<div id="programacion-page" class="page-content active">
    <main class="registro-container">
        <section>
            <h1>Programación y Rastreo Satelital</h1>
            <p>Este módulo permite gestionar la ausencia de una unidad y asignar un reemplazo notificando a las unidades disponibles.</p>
        </section>

        <section class="form-section">
            <h2>Registrar Ausencia y Buscar Reemplazo</h2>
            <form id="form-buscar-reemplazo" action="actions/programacion_action.php" method="POST">
                <input type="hidden" name="action" value="buscar_reemplazo">

                <label for="cedula-ausente">Cédula de la Unidad Ausente:</label>
                <input type="text" id="cedula-ausente" name="cedula_ausente" 
                       data-autocomplete-nombre="nombre-ausente" 
                       placeholder="Buscar por cédula..." required>
                
                <label for="nombre-ausente">Nombre de la Unidad Ausente:</label>
                <input type="text" id="nombre-ausente" name="nombre_ausente" readonly>

                <label for="puesto-trabajo">Puesto de Trabajo Afectado:</label>
                <select id="puesto-trabajo" name="id_cliente" required>
                    <option value="">-- Seleccione un Puesto --</option>
                    <?php foreach ($clientes as $cliente): ?>
                        <option value="<?= htmlspecialchars($cliente['ID_Cliente']) ?>">
                            <?= htmlspecialchars($cliente['NombreEmpresa']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <label for="fecha-turno">Fecha del Turno a Cubrir:</label>
                <input type="date" id="fecha-turno" name="fecha_turno" required>

                <label for="turno">Turno:</label>
                <select id="turno" name="turno" required>
                    <option value="Dia">Día</option>
                    <option value="Noche">Noche</option>
                </select>

                <button type="submit">Buscar Unidades Disponibles</button>
            </form>
        </section>

        <section id="resultados-disponibles">
            <h2>Unidades Disponibles</h2>
            <div id="lista-unidades">
                <p>Aquí se mostrarán las unidades disponibles después de realizar una búsqueda.</p>
            </div>
        </section>
    </main>
</div>