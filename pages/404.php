<?php
// pages/404.php
// Establecemos el código de respuesta HTTP a 404
http_response_code(404);
?>
<div class="page-content active" style="text-align: center; padding: 50px;">
    <main class="registro-container">
        <h1 style="font-size: 4em; margin: 0;">404</h1>
        <h2 style="font-size: 1.5em; margin-top: 0;">Página no encontrada</h2>
        <p>La página que buscas no existe o fue movida a otra ubicación.</p>
        <a href="index.php?page=dashboard" style="display: inline-block; margin-top: 20px; padding: 10px 20px; background-color: #007bff; color: white; text-decoration: none; border-radius: 5px;">
            Volver al Inicio
        </a>
    </main>
</div>