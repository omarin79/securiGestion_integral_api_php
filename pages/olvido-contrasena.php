<div id="olvido-contrasena-page" class="page-content active">
    <div class="login-container">
        <img src="images/logo_jh.png" alt="Logo SecuriGestiónIntegral" class="logo">
        <h1>Recuperar Contraseña</h1>
        <p>Ingresa tu correo electrónico y te enviaremos instrucciones.</p>
        
        <form id="recuperar-form" action="actions/recuperar_action.php" method="POST">
            <label for="email-recuperar">Correo Electrónico:</label>
            <input type="email" id="email-recuperar" name="email_recuperar" required>
            <button type="submit">Enviar Instrucciones</button>
            <div class="login-links">
                <a href="index.php?page=login">Volver al Inicio de Sesión</a>
            </div>
        </form>
    </div>
</div>