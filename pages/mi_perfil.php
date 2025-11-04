<?php
// C:\xampp\htdocs\securigestion\pages\mi-perfil.php

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
        // 2. Consultar la base de datos para obtener toda la información del usuario
        $stmt = $pdo->prepare(
            "SELECT u.Nombre, u.Apellido, u.DocumentoIdentidad, u.CorreoElectronico, u.Telefono, u.Direccion, u.FotoPerfilRuta, u.FechaContratacion, r.NombreRol
             FROM Usuarios u
             JOIN Roles r ON u.ID_Rol = r.ID_Rol
             WHERE u.ID_Usuario = ?"
        );
        $stmt->execute([$user_id]);
        $user_data = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $error_message = "Error al cargar los datos del perfil.";
    }
} else {
    $error_message = "No se pudo identificar al usuario.";
}

// Ruta por defecto para la foto de perfil si no existe o no se encuentra el archivo
$foto_perfil = 'images/default_avatar.png';
if (!empty($user_data['FotoPerfilRuta']) && file_exists($user_data['FotoPerfilRuta'])) {
    $foto_perfil = $user_data['FotoPerfilRuta'];
}
?>

<div id="mi-perfil-page" class="page-content active">
    <main>
        <section>
            <h1>Mi Perfil</h1>
            
            <?php if (isset($_GET['success'])): ?>
                <div class="success-message" style="display:block;"><?php echo htmlspecialchars(urldecode($_GET['success'])); ?></div>
            <?php elseif (isset($_GET['error'])): ?>
                 <div class="error-message" style="display:block;"><?php echo htmlspecialchars(urldecode($_GET['error'])); ?></div>
            <?php endif; ?>
            
            <?php if ($error_message): ?>
                <div class="error-message"><?= htmlspecialchars($error_message) ?></div>
            <?php elseif ($user_data): ?>
                
                <div class="perfil-container">
                    <div class="perfil-foto">
                        <img src="<?= htmlspecialchars($foto_perfil) ?>" alt="Foto de Perfil" class="avatar-grande">
                    </div>

                    <div class="perfil-datos">
                        <h2><?= htmlspecialchars($user_data['Nombre'] . ' ' . $user_data['Apellido']) ?></h2>
                        <p class="rol-perfil"><?= htmlspecialchars($user_data['NombreRol']) ?></p>
                        
                        <hr>

                        <p><strong>Documento:</strong> <?= htmlspecialchars($user_data['DocumentoIdentidad']) ?></p>
                        <p><strong>Correo Electrónico:</strong> <?= htmlspecialchars($user_data['CorreoElectronico']) ?></p>
                        <p><strong>Teléfono:</strong> <?= htmlspecialchars($user_data['Telefono'] ?? 'No registrado') ?></p>
                        <p><strong>Dirección:</strong> <?= htmlspecialchars($user_data['Direccion'] ?? 'No registrada') ?></p>
                        
                        <p><strong>Fecha de Contratación:</strong> 
                            <?php 
                                if (!empty($user_data['FechaContratacion'])) {
                                    // Formateamos la fecha para que sea más legible (ej: 15 de Julio de 2025)
                                    $fecha = new DateTime($user_data['FechaContratacion']);
                                    echo $fecha->format('d \d\e F \d\e Y');
                                } else {
                                    echo 'No registrada';
                                }
                            ?>
                        </p>

                        <a href="index.php?page=editar-perfil" class="btn-actualizar">Actualizar Datos Personales</a>
                    </div>
                </div>

            <?php endif; ?>
        </section>

        <section>
            <fieldset>
                <legend>Cambiar Foto de Perfil</legend>
                <form action="actions/perfil_action.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="form_type" value="upload_photo">
                    
                    <label for="foto_perfil">Selecciona una nueva imagen:</label>
                    <input type="file" name="foto_perfil" id="foto_perfil" accept="image/*" required>
                    
                    <small>Archivos permitidos: JPG, PNG. Tamaño máximo: 5MB.</small>
                    <button type="submit">Subir Nueva Foto</button>
                </form>
            </fieldset>
        </section>
        
    </main>
</div>