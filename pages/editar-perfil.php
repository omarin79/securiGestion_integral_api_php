<?php
// C:\xampp\htdocs\securigestion\pages\editar-perfil.php

// Iniciar la sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. Obtener el ID del usuario de la sesión
$user_id = $_SESSION['user_id'] ?? 0;
$user_data = null;
$error_message = '';

if ($user_id) {
    try {
        // 2. Consultar la base de datos para obtener la información actual del usuario
        $stmt = $pdo->prepare(
            "SELECT Nombre, Apellido, DocumentoIdentidad, CorreoElectronico, Telefono, Direccion 
             FROM Usuarios 
             WHERE ID_Usuario = ?"
        );
        $stmt->execute([$user_id]);
        $user_data = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $error_message = "Error al cargar los datos del perfil.";
    }
} else {
    $error_message = "No se pudo identificar al usuario.";
}
?>

<div id="editar-perfil-page" class="page-content active">
    <main>
        <section>
            <h1>Actualizar mis Datos</h1>
            <p>Modifica la información que deseas actualizar y haz clic en "Guardar Cambios".</p>
        </section>

        <section>
            <?php if ($error_message): ?>
                <div class="error-message"><?= htmlspecialchars($error_message) ?></div>
            <?php elseif ($user_data): ?>
                
                <form id="form-editar-perfil" action="actions/update_perfil_action.php" method="POST">
                    
                    <h2>Información Personal y de Contacto</h2>
                    
                    <label for="edit-nombre">Nombres:</label>
                    <input type="text" id="edit-nombre" name="nombre" value="<?= htmlspecialchars($user_data['Nombre']) ?>" required>

                    <label for="edit-apellido">Apellidos:</label>
                    <input type="text" id="edit-apellido" name="apellido" value="<?= htmlspecialchars($user_data['Apellido']) ?>" required>

                    <label for="edit-telefono">Teléfono:</label>
                    <input type="tel" id="edit-telefono" name="telefono" value="<?= htmlspecialchars($user_data['Telefono']) ?>">

                    <label for="edit-direccion">Dirección:</label>
                    <input type="text" id="edit-direccion" name="direccion" value="<?= htmlspecialchars($user_data['Direccion']) ?>">
                    
                    <hr>
                    
                    <h2>Información de la Cuenta</h2>
                    
                    <label for="edit-email">Correo Electrónico:</label>
                    <input type="email" id="edit-email" name="email" value="<?= htmlspecialchars($user_data['CorreoElectronico']) ?>" required>

                    <label for="edit-password">Nueva Contraseña (dejar en blanco para no cambiar):</label>
                    <input type="password" id="edit-password" name="password">

                    <label for="edit-password-confirm">Confirmar Nueva Contraseña:</label>
                    <input type="password" id="edit-password-confirm" name="password_confirm">
                    
                    <button type="submit">Guardar Cambios</button>
                    <a href="index.php?page=mi-perfil" class="btn-cancelar">Cancelar</a>
                </form>
            <?php endif; ?>
        </section>
    </main>
</div>