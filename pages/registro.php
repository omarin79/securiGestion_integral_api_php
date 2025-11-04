<?php
// Obtener los roles desde la base de datos para el menú desplegable
try {
    $stmt_roles = $pdo->query("SELECT ID_Rol, NombreRol FROM Roles ORDER BY NombreRol");
    $roles = $stmt_roles->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $roles = [];
    echo "<p style='color:red;'>Error al cargar los roles de usuario.</p>";
}
?>

<div id="registro-page" class="page-content active">
    <div class="login-container">
        <main>
            <section>
                <h1>Registro de Nuevo Usuario</h1>
                <p>Complete el formulario para crear un nuevo empleado en el sistema.</p>
            </section>

            <?php if (isset($_GET['status'])): ?>
                <div class="<?= $_GET['status'] === 'success' ? 'success-message' : 'error-message' ?>" style="display:block;">
                    <?= htmlspecialchars($_GET['message']) ?>
                </div>
            <?php endif; ?>

            <form id="form-registro-usuario" action="actions/registro_action.php" method="POST" enctype="multipart/form-data">

                <h2>Información Personal</h2>
                <div class="form-row">
                    <div class="form-group">
                        <label for="reg-nombre">Nombres:</label>
                        <input type="text" id="reg-nombre" name="nombre" required>
                    </div>
                    <div class="form-group">
                        <label for="reg-apellido">Apellidos:</label>
                        <input type="text" id="reg-apellido" name="apellido" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="reg-documento">Documento de Identidad:</label>
                        <input type="text" id="reg-documento" name="documento" required>
                    </div>
                    <div class="form-group">
                        <label for="reg-telefono">Teléfono:</label>
                        <input type="tel" id="reg-telefono" name="telefono">
                    </div>
                </div>

                <div class="form-group">
                    <label for="reg-direccion">Dirección:</label>
                    <input type="text" id="reg-direccion" name="direccion">
                </div>

                <hr>

                <h2>Información de la Cuenta y Laboral</h2>
                <div class="form-row">
                    <div class="form-group">
                        <label for="reg-email">Correo Electrónico:</label>
                        <input type="email" id="reg-email" name="email" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="reg-password">Contraseña:</label>
                        <input type="password" id="reg-password" name="password" required>
                    </div>
                    <div class="form-group">
                        <label for="reg-password-confirm">Confirmar Contraseña:</label>
                        <input type="password" id="reg-password-confirm" name="password_confirm" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="reg-rol">Rol en el Sistema:</label>
                        <select id="reg-rol" name="id_rol" required>
                            <option value="">-- Seleccione un Rol --</option>
                            <?php foreach ($roles as $rol): ?>
                                <option value="<?= htmlspecialchars($rol['ID_Rol']) ?>">
                                    <?= htmlspecialchars($rol['NombreRol']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="reg-fecha-contratacion">Fecha de Contratación:</label>
                        <input type="date" id="reg-fecha-contratacion" name="fecha_contratacion">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="reg-foto">Foto de Perfil (Opcional):</label>
                    <input type="file" id="reg-foto" name="foto_perfil" accept="image/jpeg, image/png">
                </div>

                <button type="submit">Registrar Usuario</button>

                <div class="login-links" style="text-align: center; margin-top: 20px; padding-top: 15px; border-top: 1px solid #eee;">
                    <p style="margin-bottom: 5px;">¿Ya tienes una cuenta?</p>
                    <a href="index.php?page=login" class="btn-secondary">Ir a Inicio de Sesión</a>
                </div>
                
            </form>
        </main>
    </div>
</div>