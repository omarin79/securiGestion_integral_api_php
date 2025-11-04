<?php
// pages/detalle-novedad.php

// 1. Verificar que se proporcionó un ID de novedad válido
$id_novedad = filter_input(INPUT_GET, 'id_novedad', FILTER_VALIDATE_INT);
if (!$id_novedad) {
    die("Error: No se ha especificado un ID de novedad válido.");
}

$novedad_details = null;
$novedad_campos = [];

try {
    // 2. Obtener los detalles generales de la novedad
    $stmt_novedad = $pdo->prepare("
        SELECT 
            n.TipoNovedad,
            n.FechaHoraRegistro,
            n.EstadoNovedad,
            CONCAT(u.Nombre, ' ', u.Apellido) AS UsuarioReporta
        FROM Novedades n
        JOIN Usuarios u ON n.ID_Usuario_Reporta = u.ID_Usuario
        WHERE n.ID_Novedad = ?
    ");
    $stmt_novedad->execute([$id_novedad]);
    $novedad_details = $stmt_novedad->fetch(PDO::FETCH_ASSOC);

    if (!$novedad_details) {
        die("Error: La novedad con el ID $id_novedad no fue encontrada.");
    }

    // 3. Obtener todos los campos y valores del formulario guardados
    $stmt_campos = $pdo->prepare("
        SELECT Campo, Valor
        FROM DetallesNovedad
        WHERE ID_Novedad = ?
    ");
    $stmt_campos->execute([$id_novedad]);
    // Usamos PDO::FETCH_KEY_PAIR para tener un array asociativo [Campo => Valor]
    $novedad_campos = $stmt_campos->fetchAll(PDO::FETCH_KEY_PAIR);

} catch (PDOException $e) {
    die("Error al consultar los detalles de la novedad: " . $e->getMessage());
}

// Función para limpiar y formatear el nombre del campo para mostrarlo al usuario
function formatar_nombre_campo($campo) {
    return ucwords(str_replace('_', ' ', $campo));
}
?>

<div id="detalle-novedad-page" class="page-content active">
    <main class="registro-container">
        <section>
            <h1>Detalle de la Novedad #<?php echo htmlspecialchars($id_novedad); ?></h1>
            <a href="index.php?page=novedades" style="display: inline-block; margin-bottom: 20px;">&larr; Volver a la lista de novedades</a>
        </section>

        <section>
            <h2>Información General</h2>
            <p><strong>Tipo de Novedad:</strong> <?php echo htmlspecialchars(formatar_nombre_campo($novedad_details['TipoNovedad'])); ?></p>
            <p><strong>Fecha de Registro:</strong> <?php echo htmlspecialchars($novedad_details['FechaHoraRegistro']); ?></p>
            <p><strong>Reportado por:</strong> <?php echo htmlspecialchars($novedad_details['UsuarioReporta']); ?></p>
            <p><strong>Estado:</strong> <?php echo htmlspecialchars($novedad_details['EstadoNovedad']); ?></p>
        </section>

        <section>
            <h2>Formulario Diligenciado</h2>
            <div style="background-color: #f9f9f9; border: 1px solid #ddd; padding: 15px; border-radius: 5px;">
                <?php foreach ($novedad_campos as $campo => $valor): ?>
                    <div style="margin-bottom: 12px; padding-bottom: 12px; border-bottom: 1px solid #eee;">
                        <strong><?php echo htmlspecialchars(formatar_nombre_campo($campo)); ?>:</strong>
                        <div>
                            <?php
                            // Verificamos si el valor es una ruta a un archivo de imagen o PDF
                            if (preg_match('/\.(jpg|jpeg|png|gif|pdf)$/i', $valor)) {
                                if (preg_match('/\.(jpg|jpeg|png|gif)$/i', $valor)) {
                                    // Si es imagen, la mostramos
                                    echo '<a href="' . htmlspecialchars($valor) . '" target="_blank" title="Ver evidencia completa">';
                                    echo '<img src="' . htmlspecialchars($valor) . '" alt="Evidencia adjunta" style="max-width: 200px; max-height: 200px; border-radius: 5px; margin-top: 5px;">';
                                    echo '</a>';
                                } else {
                                    // Si es PDF, mostramos un enlace
                                    echo '<a href="' . htmlspecialchars($valor) . '" target="_blank">Ver documento adjunto (PDF)</a>';
                                }
                            } else {
                                // Si es texto normal, lo mostramos
                                echo nl2br(htmlspecialchars($valor));
                            }
                            ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    </main>
</div>