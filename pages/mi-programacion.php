<?php
// pages/mi-programacion.php
?>
<div id="mi-programacion-page" class="page-content active">
    <main class="registro-container">
        <section>
            <h1>Mi Programación</h1>
            <p>Aquí puedes consultar tu horario de trabajo asignado para el mes actual.</p>
        </section>

        <section class="form-section">
            <h2>Horario del Mes</h2>
            <div id="calendario-container" style="margin-top: 20px;">
                <p>Cargando tu programación...</p>
            </div>
        </section>
    </main>
</div>

<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js'></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const calendarEl = document.getElementById('calendario-container');

    // Inicializamos el calendario
    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth', // Vista de mes
        locale: 'es', // Idioma español
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek'
        },
        events: 'actions/consulta_programacion_action.php', // Fuente de datos
        eventDidMount: function(info) {
            // Añadimos Tooltips para ver el nombre del cliente al pasar el mouse
            if (info.event.extendedProps.cliente) {
                info.el.setAttribute('title', info.event.extendedProps.cliente);
            }
        }
    });

    // Renderizamos el calendario
    calendar.render();
});
</script>