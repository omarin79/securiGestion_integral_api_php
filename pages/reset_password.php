<?php
$token = $_GET['token'] ?? '';
if (empty($token)) {
    echo "Token no proporcionado.";
    exit();
}
?>

<div id="login-page">
    <div class="login-container">
        <h1>Crear Nueva Contraseña</h1>
        <p>Por favor, introduce tu nueva contraseña.</p>

        <form action="actions/update_password_action.php" method="POST">
            <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">

            <label for="new_password">Nueva Contraseña:</label>
            <input type="password" id="new_password" name="new_password" required>

            <label for="confirm_new_password">Confirmar Nueva Contraseña:</label>
            <input type="password" id="confirm_new_password" name="confirm_new_password" required>

            <button type="submit">Actualizar Contraseña</button>
        </form>
    </div>
</div>