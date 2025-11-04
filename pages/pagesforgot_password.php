<div id="login-page"> <div class="login-container">
        <img src="images/logo_jh.png" alt="Logo SecuriGestiónIntegral" class="logo">
        <h1>Restablecer Contraseña</h1>
        <p>Ingresa tu correo electrónico y te enviaremos un enlace para restablecer tu contraseña.</p>

        <?php if (isset($_GET['message'])): ?>
            <div class="<?= ($_GET['status'] ?? '') === 'success' ? 'success-message' : 'error-message' ?>" style="display:block;">
                <?= htmlspecialchars($_GET['message']) ?>
            </div>
        <?php endif; ?>

        <form action="actions/request_reset_action.php" method="POST">
            <label for="email">Correo Electrónico:</label>
            <input type="email" id="email" name="email" required>
            <button type="submit">Enviar Enlace de Restablecimiento</button>
            <div class="login-links" style="margin-top: 15px;">
                <a href="index.php?page=login">Volver a Inicio de Sesión</a>
            </div>
        </form>
    </div>
</div>