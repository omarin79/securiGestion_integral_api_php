<div id="registro-visita-page" class="page-content active">
    <main class="registro-container">
        <img src="images/logo_jh.png" alt="Logo JH SAS" class="logo">
        <h1>Registro de Visita de Supervisión</h1>
        <form id="visita-form" action="actions/visita_action.php" method="POST">
            <label for="fecha-visita">Fecha:</label>
            <input type="date" id="fecha-visita" name="fecha-visita" required>
            
            <label for="puesto">1. Seleccionar puesto a visitar:</label>
            <select id="puesto" name="puesto" required>
                <option value="">Seleccione...</option>
                <option value="1">Recepción Principal Edificio ABC</option>
                <option value="2">Portería Vehicular La Floresta</option>
            </select>

            <fieldset>
                <legend>Checklist Presentación:</legend>
                <div><input type="checkbox" id="item1" name="checklist_items[]" value="uniforme_completo"><label for="item1">Porta uniforme completo</label></div>
                <div><input type="checkbox" id="item2" name="checklist_items[]" value="carnet_visible"><label for="item2">Porta carnet visible</label></div>
                <div><input type="checkbox" id="item3" name="checklist_items[]" value="cumple_protocolos"><label for="item3">Cumple protocolos establecidos</label></div>
            </fieldset>

            <label for="novedades-operativas">Novedades Operativas:</label>
            <textarea id="novedades-operativas" name="novedades-operativas" rows="3"></textarea>

            <label for="novedades-nomina">Novedades Nómina:</label>
            <textarea id="novedades-nomina" name="novedades-nomina" rows="3"></textarea>

            <button type="submit">Guardar Visita</button>
        </form>
    </main>
</div>