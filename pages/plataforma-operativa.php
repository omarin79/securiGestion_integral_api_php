<?php
// C:\xampp\htdocs\securigestion\Seguri_gestion_integral_PHP\pages\plataforma-operativa.php (Versión Definitiva con Estilos Internos)
?>

<style>
/* --- DISEÑO FINAL Y DEFINITIVO DEL DASHBOARD --- */

/* Contenedor principal de la página con fondo gris claro */
#plataforma-operativa-page .page-content main {
    background-color: #f4f6f9 !important; /* Color de fondo gris claro */
    padding: 25px !important;
    border-radius: 10px !important;
    max-width: 1200px;
    margin: 2rem auto; /* Centra el contenedor principal */
}

/* Contenedor de la cuadrícula para las tarjetas */
#plataforma-operativa-page .menu-container {
    display: grid !important;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)) !important;
    gap: 30px !important;
    margin-top: 20px !important;
}

/* Estilo individual de cada tarjeta del menú */
#plataforma-operativa-page .menu-card {
    display: flex !important;
    align-items: center !important;
    padding: 25px !important;
    border-radius: 15px !important;
    text-decoration: none !important;
    color: #fff !important;
    transition: all 0.3s ease !important;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1) !important;
    border: none !important;
}

#plataforma-operativa-page .menu-card:hover {
    transform: translateY(-10px) scale(1.02) !important;
    box-shadow: 0 12px 25px rgba(0,0,0,0.2) !important;
}

/* Contenedor del icono a la izquierda */
#plataforma-operativa-page .menu-card .icon-container {
    font-size: 2.5em !important;
    margin-right: 25px !important;
    flex-shrink: 0 !important;
}

/* Contenedor del texto (título y subtítulo) */
#plataforma-operativa-page .menu-card .text-container {
    text-align: left !important;
}

#plataforma-operativa-page .menu-card .card-title {
    font-size: 1.4em !important;
    font-weight: bold !important;
    margin: 0 !important;
}

#plataforma-operativa-page .menu-card .card-subtitle {
    font-size: 0.9em !important;
    margin: 5px 0 0 0 !important;
    opacity: 0.9 !important;
}


/* Colores de fondo para cada tarjeta */
.card-red { background: linear-gradient(45deg, #d32f2f, #ef5350); }
.card-teal { background: linear-gradient(45deg, #00796B, #00897B); }
.card-orange { background: linear-gradient(45deg, #ff8c00, #ffaf60); }
.card-grey { background: linear-gradient(45deg, #616161, #9E9E9E); }
.card-indigo { background: linear-gradient(45deg, #303f9f, #3f51b5); }
.card-cyan { background: linear-gradient(45deg, #0097a7, #00acc1); }
/* Nuevo color para la tarjeta de programación */
.card-blue { background: linear-gradient(45deg, #0052D4, #4364F7, #6FB1FC); }


</style>
<div id="plataforma-operativa-page" class="page-content active">
    <main>
        <section>
            <h1>Plataforma Operativa</h1>
            <p>Seleccione la opción que desea gestionar:</p>
            
            <div class="menu-container">
                <a href="index.php?page=gestion-informes-general" class="menu-card card-red">
                    <div class="icon-container"><i class="fa-solid fa-chart-simple"></i></div>
                    <div class="text-container"><p class="card-title">Gestión de Informes</p></div>
                </a>
                <a href="index.php?page=registro-novedades-general" class="menu-card card-teal">
                    <div class="icon-container"><i class="fa-solid fa-file-circle-plus"></i></div>
                    <div class="text-container"><p class="card-title">Registro de Novedades</p></div>
                </a>
                <a href="index.php?page=visualizacion-alertas" class="menu-card card-orange">
                    <div class="icon-container"><i class="fa-solid fa-triangle-exclamation"></i></div>
                    <div class="text-container"><p class="card-title">Visualización de Alertas</p></div>
                </a>

                <a href="index.php?page=programacion" class="menu-card card-blue">
                    <div class="icon-container"><i class="fa-solid fa-satellite-dish"></i></div>
                    <div class="text-container">
                        <p class="card-title">Programación y Rastreo</p>
                        <p class="card-subtitle">Asignación de turnos por ausencia</p>
                    </div>
                </a>
                
                <a href="index.php?page=reporte_disciplinario" class="menu-card card-orange">
                    <div class="icon-container"><i class="fa-solid fa-file-signature"></i></div>
                    <div class="text-container">
                        <p class="card-title">Reporte Disciplinario</p>
                        <p class="card-subtitle">Registrar llamado de atención a unidad</p>
                    </div>
                </a>
                <a href="index.php?page=mi-programacion" class="menu-card card-indigo">
                    <div class="icon-container"><i class="fa-solid fa-calendar-days"></i></div>
                    <div class="text-container"><p class="card-title">Mi Programación</p></div>
                </a>
                <a href="index.php?page=preoperacional-vehiculos" class="menu-card card-cyan">
                    <div class="icon-container"><i class="fa-solid fa-car-on"></i></div>
                    <div class="text-container"><p class="card-title">Pre-operacional Vehículos</p></div>
                </a>
                <a href="index.php?page=visitas" class="menu-card card-grey">
                    <div class="icon-container"><i class="fa-solid fa-person-walking-luggage"></i></div>
                    <div class="text-container"><p class="card-title">Registrar Visita</p></div>
                </a>
            </div>
        </section>
    </main>
</div>