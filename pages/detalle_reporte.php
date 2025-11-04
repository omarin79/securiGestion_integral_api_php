<?php
// pages/detalle_reporte.php (Versión Corregida)

$id_novedad = filter_input(INPUT_GET, 'id_novedad', FILTER_VALIDATE_INT);
if (!$id_novedad) {
    die("ID de novedad no válido.");
}

try {
    // Obtener información general de la novedad
    $stmt_novedad = $pdo->prepare("
        SELECT n.*, CONCAT(u.Nombre, ' ', u.Apellido) AS UsuarioReporta, pa.nombre_completo AS PersonalAfectado
        FROM Novedades n
        JOIN Usuarios u ON n.ID_Usuario_Reporta = u.ID_Usuario
        LEFT JOIN personal_autocompletar pa ON n.Documento_Afectado = pa.documento
        WHERE n.ID_Novedad = ?
    ");
    $stmt_novedad->execute([$id_novedad]);
    $novedad = $stmt_novedad->fetch(PDO::FETCH_ASSOC);

    // Obtener los detalles específicos guardados
    $stmt_detalles = $pdo->prepare("SELECT Campo, Valor FROM DetallesNovedad WHERE ID_Novedad = ?");
    $stmt_detalles->execute([$id_novedad]);
    $detalles = $stmt_detalles->fetchAll(PDO::FETCH_KEY_PAIR);

} catch (PDOException $e) {
    die("Error al consultar la base de datos: " . $e->getMessage());
}

if (!$novedad) {
    die("Reporte no encontrado.");
}
?>

<div id="detalle-reporte-page" class="page-content active">
    <main class="registro-container">
        <section>
            <h1>Detalle del Reporte Disciplinario #<?php echo htmlspecialchars($id_novedad); ?></h1>
            <a href="index.php?page=consulta_reportes" style="display: inline-block; margin-bottom: 20px;">&larr; Volver a la lista de reportes</a>
        </section>

        <section>
            <h2>Información del Reporte</h2>
            <p><strong>Fecha de Registro:</strong> <?php echo htmlspecialchars($novedad['FechaHoraRegistro']); ?></p>
            <p><strong>Reportado por:</strong> <?php echo htmlspecialchars($novedad['UsuarioReporta']); ?></p>
            <p><strong>Unidad Afectada:</strong> <?php echo htmlspecialchars($novedad['PersonalAfectado'] . ' (C.C. ' . $novedad['Documento_Afectado'] . ')'); ?></p>
            <p><strong>Tipo de Falta:</strong> <?php echo htmlspecialchars($detalles['tipo_de_falta'] ?? 'No especificada'); ?></p>
            <p><strong>Estado:</strong> <?php echo htmlspecialchars($novedad['EstadoNovedad']); ?></p>
            
            <hr>
            
            <h3>Descripción de los Hechos</h3>
            <p style="background-color: #f9f9f9; border: 1px solid #ddd; padding: 15px; border-radius: 5px; min-height: 80px;">
                <?php echo nl2br(htmlspecialchars($detalles['descripcion_hechos'] ?? 'Sin descripción.')); ?>
            </p>

            <h3>Evidencia Adjunta</h3>
            <?php if (isset($detalles['informe_adjunto_ruta']) && file_exists($detalles['informe_adjunto_ruta'])): ?>
                <div style="margin-top: 10px;">
                    <a href="<?php echo htmlspecialchars($detalles['informe_adjunto_ruta']); ?>" target="_blank" title="Ver imagen completa">
                        <img src="<?php echo htmlspecialchars($detalles['informe_adjunto_ruta']); ?>" alt="Evidencia del reporte" style="max-width: 100%; border-radius: 5px; border: 1px solid #ccc;">
                    </a>
                </div>
            <?php else: ?>
                <p style="color: #777;">No se adjuntó ninguna imagen para este reporte.</p>
            <?php endif; ?>
        </section>
    </main>
</div>