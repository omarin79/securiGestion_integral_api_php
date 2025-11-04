<?php
// C:\xampp\htdocs\securigestion\Seguri_gestion_integral_PHP\pages\inicio.php (Versión Definitiva con Estilos Internos)
?>

<style>
/* --- DISEÑO FINAL Y DEFINITIVO DEL DASHBOARD DE INICIO --- */

/* Contenedor principal de la página con fondo gris claro */
#inicio-page .page-content main {
    background-color: #f4f6f9 !important; /* Color de fondo gris claro */
    padding: 25px !important;
    border-radius: 10px !important;
    max-width: 1200px;
    margin: 2rem auto; /* Centra el contenedor principal */
}

/* Contenedor de la cuadrícula para las tarjetas */
#inicio-page .menu-container {
    display: grid !important;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)) !important;
    gap: 30px !important;
    margin-top: 20px !important;
}

/* Estilo individual de cada tarjeta del menú */
#inicio-page .menu-card {
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

#inicio-page .menu-card:hover {
    transform: translateY(-10px) scale(1.02) !important;
    box-shadow: 0 12px 25px rgba(0,0,0,0.2) !important;
}

/* Contenedor del icono a la izquierda */
#inicio-page .menu-card .icon-container {
    font-size: 2.5em !important;
    margin-right: 25px !important;
    flex-shrink: 0 !important;
}

/* Contenedor del texto (título y subtítulo) */
#inicio-page .menu-card .text-container {
    text-align: left !important;
}

#inicio-page .menu-card .card-title {
    font-size: 1.4em !important;
    font-weight: bold !important;
    margin: 0 !important;
}

#inicio-page .menu-card .card-subtitle {
    font-size: 0.9em !important;
    margin: 5px 0 0 0 !important;
    opacity: 0.9 !important;
}

/* Colores de fondo para cada tarjeta */
.card-blue { background: linear-gradient(45deg, #0052D4, #4364F7, #6FB1FC); }
.card-green { background: linear-gradient(45deg, #136a8a, #267871); }
.card-orange { background: linear-gradient(45deg, #ff8c00, #ffaf60); }
.card-purple { background: linear-gradient(45deg, #473B7B, #3584A7); }

</style>
<div id="inicio-page" class="page-content active">
    <main>
        <section>
            <div style="margin-bottom: 40px; text-align: left;">
                <h1>Bienvenido, <?php echo htmlspecialchars(explode(' ', $_SESSION['user_nombre'])[0]); ?></h1>
                <p>Esta es tu plataforma de gestión integral. Selecciona una opción para comenzar.</p>
            </div>

            <h2 style="text-align: left;">MENÚ PRINCIPAL</h2>
            
            <div class="menu-container">
                <a href="index.php?page=plataforma_operativa" class="menu-card card-blue">
                    <div class="icon-container">
                        <i class="fa-solid fa-shield-halved"></i>
                    </div>
                    <div class="text-container">
                        <p class="card-title">Plataforma Operativa</p>
                        <p class="card-subtitle">Novedades, Alertas, Informes y Visitas</p>
                    </div>
                </a>

                <a href="index.php?page=talento_humano" class="menu-card card-green">
                    <div class="icon-container">
                        <i class="fa-solid fa-users"></i>
                    </div>
                    <div class="text-container">
                        <p class="card-title">Talento Humano</p>
                        <p class="card-subtitle">Gestión de Cartas y Certificados</p>
                    </div>
                </a>

                <a href="index.php?page=nomina" class="menu-card card-orange">
                    <div class="icon-container">
                        <i class="fa-solid fa-file-invoice-dollar"></i>
                    </div>
                    <div class="text-container">
                        <p class="card-title">Nómina</p>
                        <p class="card-subtitle">Consulta de Desprendibles de Pago</p>
                    </div>
                </a>
                
                <a href="index.php?page=mi-perfil" class="menu-card card-purple">
                    <div class="icon-container">
                        <i class="fa-solid fa-user-gear"></i>
                    </div>
                    <div class="text-container">
                        <p class="card-title">Mi Perfil</p>
                        <p class="card-subtitle">Actualiza tus datos personales</p>
                    </div>
                </a>
            </div>
        </section>
    </main>
</div>