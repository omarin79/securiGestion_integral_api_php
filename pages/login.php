<div id="login-page" class="page-content active">
    <div class="login-container">
        <img src="images/logo_jh.png" alt="Logo SecuriGestiónIntegral" class="logo">
        <h1>SecuriGestiónIntegral</h1>
        
        <form id="login-form" action="actions/login_action.php" method="POST">
            
            <?php if (isset($_GET['error'])): ?>
                <div class="error-message" style="display: block;">
                    <?php
                        switch ($_GET['error']) {
                            case 'empty_fields':
                                echo 'Por favor, ingrese su cédula y contraseña.';
                                break;
                            case 'invalid_credentials':
                                echo 'La cédula o la contraseña son incorrectas.';
                                break;
                            case 'db_error':
                                echo 'Error del sistema. Por favor, contacte al administrador.';
                                break;
                            default:
                                echo 'Ha ocurrido un error inesperado.';
                                break;
                        }
                    ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['success'])): ?>
                <div class="success-message" style="display: block;">
                    <?= htmlspecialchars($_GET['success']) ?>
                </div>
            <?php endif; ?>

            <label for="username">Nombre de usuario (Cédula):</label>
            <input type="text" id="username" name="username" required>

            <label for="password">Contraseña:</label>
            <input type="password" id="password" name="password" required>

            <button type="submit">Ingresar</button>

            <div class="login-links">
                <a href="index.php?page=olvido-contrasena">¿Olvidó su contraseña?</a>
                <a href="index.php?page=registro">Registrarse</a>
            </div>
        </form>
    </div>
</div>