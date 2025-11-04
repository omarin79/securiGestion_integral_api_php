<?php
// C:\xampp\htdocs\securigestion\pages\reset-password.php

// Obtiene el token de la URL. Si no hay token, no muestra el formulario.
$token = $_GET['token'] ?? '';
if (empty($token)) {
    echo "<h1>Token inválido o no proporcionado.</h1>";
    exit();
}
?>

<div id="reset-password-page" class="page-content active">
    <div class="login-container">
        <img src="images/logo_jh.png" alt="Logo SecuriGestiónIntegral" class="logo">
        <h1>Restablecer Contraseña</h1>
        <p>Por favor, ingrese su nueva contraseña.</p>
        
        <form id="reset-form" action="actions/reset_password_action.php" method="POST">
            <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
            
            <label for="new_password">Nueva Contraseña:</label>
            <input type="password" id="new_password" name="new_password" required>

            <label for="confirm_new_password">Confirmar Nueva Contraseña:</label>
            <input type="password" id="confirm_new_password" name="confirm_new_password" required>

            <button type="submit">Guardar Nueva Contraseña</button>
        </form>
    </div>
</div>