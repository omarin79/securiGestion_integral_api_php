<?php
// pages/programacion_general.php
?>
<div id="programacion-general-page" class="page-content active">
    <main class="registro-container">
        <section>
            <h1>Programación General de Personal</h1>
            <p>Visualiza el calendario de turnos de todo el personal. Usa el buscador para filtrar por un empleado específico.</p>
        </section>

        <section class="form-section">
            <h2>Filtro de Búsqueda</h2>
            <div id="filtro-programacion">
                <label for="cedula-empleado-prog">Consultar Cédula Específica (opcional):</label>
                <input type="text" id="cedula-empleado-prog" name="cedula_empleado_prog" 
                       data-autocomplete-nombre="nombre-empleado-prog" 
                       placeholder="Escribe la cédula para buscar..." autocomplete="off">
                
                <label for="nombre-empleado-prog">Nombre del Empleado:</label>
                <input type="text" id="nombre-empleado-prog" name="nombre_empleado_prog" readonly>
                
                <div style="display:flex; gap: 10px; margin-top:15px;">
                    <button id="btn-filtrar-programacion">Buscar</button>
                    <button id="btn-reset-programacion">Mostrar Todos</button>
                </div>
            </div>
        </section>

        <section>
            <h2>Calendario de Turnos</h2>
            <div id="calendario-general-container" style="margin-top: 20px;">
                </div>
        </section>
    </main>
</div>

<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js'></script>