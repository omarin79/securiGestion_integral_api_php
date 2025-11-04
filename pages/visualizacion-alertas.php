<div id="visualizacion-alertas-page" class="page-content active">
    <main>
        <section>
            <h1>Visualización de Alertas</h1>
            <p>Consulte las alertas críticas y pendientes generadas por el sistema.</p>
            
            <form id="filtro-alertas-form" action="actions/alertas_action.php" method="POST">
                <label for="alerta-tipo">Tipo de Alerta:</label>
                <select id="alerta-tipo" name="tipo_alerta">
                    <option value="">Todos</option>
                    <option value="critica">Novedad Crítica</option>
                    <option value="pendiente">Pendiente de Aprobación</option>
                    <option value="documental">Aniversario Documental</option>
                    <option value="sistema">Sistema</option>
                </select>

                <label for="alerta-estado">Estado:</label>
                <select id="alerta-estado" name="estado_alerta">
                    <option value="">Todos</option>
                    <option value="no-leida">No Leída</option>
                    <option value="leida">Leída</option>
                    <option value="resuelta">Resuelta</option>
                </select>

                <button type="submit">Filtrar Alertas</button>
            </form>

            <div id="alertas-list" style="margin-top: 20px;">
                <h2>Alertas Actuales:</h2>
                <ul style="list-style: none; padding: 0;">
                    <li>
                        <strong>Novedad Crítica: Ausencia de Unidad (CC: 123456789)</strong>
                        <br><small>Puesto: Portería Vehicular La Floresta, Turno: Noche, Fecha: 2025-06-01 23:00</small>
                    </li>
                    <li style="margin-top: 10px;">
                        <strong>Alerta: Pendiente de Aprobación (Reporte Cond. Insegura)</strong>
                        <br><small>Ubicación: Pasillo Central, Fecha: 2025-05-30 10:15</small>
                    </li>
                </ul>
            </div>
        </section>
    </main>
</div>