<?php
// pages/detalle-visita.php

// 1. Verificar si se proporcionó un ID de visita
$id_visita = $_GET['id_visita'] ?? 0;
if (!$id_visita) {
    die("Error: No se ha especificado un ID de visita.");
}

$visita_details = null;
$checklist_respuestas = [];

try {
    // 2. Obtener los detalles generales de la visita
    $stmt_visita = $pdo->prepare("
        SELECT 
            v.FechaVisita,
            v.Hallazgos,
            v.Recomendaciones,
            COALESCE(CONCAT(sup.Nombre, ' ', sup.Apellido), 'N/A') AS Supervisor,
            COALESCE(CONCAT(aud.Nombre, ' ', aud.Apellido), pa.nombre_completo, 'N/A') AS Auditado,
            COALESCE(cli.NombreEmpresa, 'N/A') AS Cliente,
            COALESCE(chk.NombreChecklist, 'N/A') AS Checklist
        FROM Visitas v
        LEFT JOIN Usuarios sup ON v.ID_Usuario_Supervisor = sup.ID_Usuario
        LEFT JOIN Clientes cli ON v.ID_Cliente = cli.ID_Cliente
        LEFT JOIN Checklists chk ON v.ID_Checklist = chk.ID_Checklist
        LEFT JOIN Usuarios aud ON v.ID_Usuario_Auditado = aud.ID_Usuario
        LEFT JOIN personal_autocompletar pa ON v.Documento_Auditado = pa.documento
        WHERE v.ID_Visita = ?
    ");
    $stmt_visita->execute([$id_visita]);
    $visita_details = $stmt_visita->fetch(PDO::FETCH_ASSOC);

    // 3. Obtener las preguntas, respuestas y EVIDENCIAS del checklist
    $stmt_respuestas = $pdo->prepare("
        SELECT
            ic.Seccion,
            ic.Pregunta,
            r.Respuesta,
            r.RutaEvidencia
        FROM RespuestasChecklist r
        JOIN ItemsChecklist ic ON r.ID_Item = ic.ID_Item
        WHERE r.ID_Visita = ?
        ORDER BY ic.Seccion, ic.Orden, ic.ID_Item
    ");
    $stmt_respuestas->execute([$id_visita]);
    $checklist_respuestas = $stmt_respuestas->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Error al consultar los detalles de la visita: " . $e->getMessage());
}

if (!$visita_details) {
    die("Error: La visita con el ID $id_visita no fue encontrada.");
}
?>

<div id="detalle-visita-page" class="page-content active">
    <main class="registro-container">
        <section>
            <h1>Detalle de la Visita de Supervisión #<?php echo htmlspecialchars($id_visita); ?></h1>
            <a href="index.php?page=visitas" style="display: inline-block; margin-bottom: 20px;">&larr; Volver a la lista de visitas</a>
        </section>

        <section>
            <h2>1. Información General</h2>
            <p><strong>Fecha de la Visita:</strong> <?php echo htmlspecialchars($visita_details['FechaVisita']); ?></p>
            <p><strong>Supervisor:</strong> <?php echo htmlspecialchars($visita_details['Supervisor']); ?></p>
            <p><strong>Empleado Auditado:</strong> <?php echo htmlspecialchars($visita_details['Auditado']); ?></p>
            <p><strong>Cliente:</strong> <?php echo htmlspecialchars($visita_details['Cliente']); ?></p>
        </section>

        <section>
            <h2>2. Checklist Diligenciado: "<?php echo htmlspecialchars($visita_details['Checklist']); ?>"</h2>
            <?php if (empty($checklist_respuestas)): ?>
                <p>No se encontraron respuestas de checklist para esta visita.</p>
            <?php else: ?>
                <?php 
                $current_section = '';
                foreach ($checklist_respuestas as $item):
                    if ($item['Seccion'] !== $current_section):
                        $current_section = $item['Seccion'];
                ?>
                        <h3 style="margin-top: 20px; border-top: 1px solid #eee; padding-top: 15px;"><?php echo htmlspecialchars($current_section); ?></h3>
                <?php endif; ?>
                    <div style="padding: 12px; border-bottom: 1px solid #f0f0f0;">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span><?php echo htmlspecialchars($item['Pregunta']); ?></span>
                            <strong style="min-width: 50px; text-align: right;"><?php echo htmlspecialchars($item['Respuesta']); ?></strong>
                        </div>
                        <?php if (!empty($item['RutaEvidencia'])): ?>
                            <div style="margin-top: 10px;">
                                <a href="<?php echo htmlspecialchars($item['RutaEvidencia']); ?>" target="_blank" title="Ver imagen completa">
                                    <img src="<?php echo htmlspecialchars($item['RutaEvidencia']); ?>" alt="Evidencia" style="max-width: 150px; max-height: 150px; border-radius: 5px; border: 1px solid #ccc;">
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </section>
        
        <section>
            <h2>3. Hallazgos y Recomendaciones</h2>
            <h4>Hallazgos:</h4>
            <p style="background-color: #f9f9f9; border: 1px solid #ddd; padding: 10px; border-radius: 4px; min-height: 50px;">
                <?php echo nl2br(htmlspecialchars($visita_details['Hallazgos'])); ?>
            </p>

            <h4>Recomendaciones:</h4>
            <p style="background-color: #f9f9f9; border: 1px solid #ddd; padding: 10px; border-radius: 4px; min-height: 50px;">
                <?php echo nl2br(htmlspecialchars($visita_details['Recomendaciones'])); ?>
            </p>
        </section>
    </main>
</div>