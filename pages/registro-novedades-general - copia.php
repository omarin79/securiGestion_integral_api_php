<div id="registro-novedades-general-page" class="page-content active">
    <main>
        <section>
            <h1>Registro de Novedades</h1>
            <p>Seleccione el tipo de novedad que desea registrar para mostrar el formulario correspondiente.</p>

            <label for="tipo-novedad-registro">Tipo de Novedad a Registrar:</label>
            <select id="tipo-novedad-registro" name="tipo-novedad-registro" required>
                <option value="">-- Seleccione una opción --</option>
                <option value="ausencia">Ausencia de Unidad (No Evasión)</option>
                <option value="incapacidad">Incapacidad</option>
                <option value="licencia-remunerada">Licencia Remunerada</option>
                <option value="permiso-remunerado">Permiso Remunerado</option>
                <option value="licencia-no-remunerada">Licencia No Remunerada</option>
                <option value="permiso-no-remunerado">Permiso No Remunerado</option>
                <option value="unidad-evadida">Unidad Evadida</option>
                <option value="condicion-insegura">Reporte de Condición Insegura</option>
            </select>

            <div id="novedad-form-container" style="margin-top: 20px; border-top: 1px solid #eee; padding-top: 15px;">
                <p>Seleccione un tipo de novedad para ver el formulario.</p>
            </div>
        </section>
        
        <section>
            <h2>Consultar Novedades Existentes</h2>
            <form id="novedades-consulta-form" action="actions/novedades_consulta_action.php" method="POST">
                 <button type="submit">Buscar Novedades</button>
            </form>
             <div id="novedades-results" style="margin-top: 20px;">
                <p>Resultados de la búsqueda se mostrarán aquí.</p>
            </div>
        </section>
    </main>
</div>