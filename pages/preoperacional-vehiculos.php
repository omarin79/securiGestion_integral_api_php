<div id="preoperacional-vehiculos-page" class="page-content active">
    <main class="registro-container">
        <section>
            <h1>Registro Pre-operacional de Vehículos</h1>
            <p>Complete el checklist según el tipo de vehículo asignado.</p>
            
            <?php if (isset($_GET['success'])): ?>
                <div class="success-message" style="display:block;"><?php echo htmlspecialchars(urldecode($_GET['success'])); ?></div>
            <?php elseif (isset($_GET['error'])): ?>
                 <div class="error-message" style="display:block;"><?php echo htmlspecialchars(urldecode($_GET['error'])); ?></div>
            <?php endif; ?>

            <form id="preoperacional-form" action="actions/preoperacional_action.php" method="POST" enctype="multipart/form-data">
                
                <label for="tipo_vehiculo">1. Seleccione el Tipo de Vehículo:</label>
                <select id="tipo_vehiculo" name="tipo_vehiculo" required>
                    <option value="">-- Seleccione --</option>
                    <option value="Carro">Carro</option>
                    <option value="Moto">Moto</option>
                </select>

                <div id="campos-vehiculo" style="display:none; margin-top: 20px;">
                    <h3>2. Datos del Vehículo</h3>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="placa">Placa:</label>
                            <input type="text" id="placa" name="placa" required>
                        </div>
                        <div class="form-group">
                            <label for="marca">Marca:</label>
                            <input type="text" id="marca" name="marca" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="modelo">Modelo (Año):</label>
                            <input type="number" id="modelo" name="modelo" min="1900" max="2100" required>
                        </div>
                        <div class="form-group">
                            <label for="cilindraje">Cilindraje (cc):</label>
                            <input type="text" id="cilindraje" name="cilindraje" required>
                        </div>
                    </div>

                    <fieldset id="checklist_carro" style="display:none; margin-top:15px;">
                        <legend>3. Lista de Chequeo para Carro</legend>
                        <div><label><input type="checkbox" name="items_carro[]" value="luces_delanteras"> Luces delanteras (altas y bajas)</label></div>
                        <div><label><input type="checkbox" name="items_carro[]" value="luces_traseras"> Luces traseras (freno y direccionales)</label></div>
                        <div><label><input type="checkbox" name="items_carro[]" value="nivel_aceite"> Nivel de aceite</label></div>
                        <div><label><input type="checkbox" name="items_carro[]" value="nivel_refrigerante"> Nivel de líquido refrigerante</label></div>
                        <div><label><input type="checkbox" name="items_carro[]" value="presion_llantas"> Presión y estado de las llantas</label></div>
                        <div><label><input type="checkbox" name="items_carro[]" value="estado_frenos"> Estado de los frenos</label></div>
                        <div><label><input type="checkbox" name="items_carro[]" value="documentos_vehiculo"> Documentos del vehículo (SOAT, tecno)</label></div>
                    </fieldset>

                    <fieldset id="checklist_moto" style="display:none; margin-top:15px;">
                        <legend>3. Lista de Chequeo para Moto</legend>
                        <div><label><input type="checkbox" name="items_moto[]" value="luz_frontal"> Luz frontal</label></div>
                        <div><label><input type="checkbox" name="items_moto[]" value="luz_stop"> Luz de stop</label></div>
                        <div><label><input type="checkbox" name="items_moto[]" value="frenos"> Frenos (delantero y trasero)</label></div>
                        <div><label><input type="checkbox" name="items_moto[]" value="presion_llantas"> Presión y estado de las llantas</label></div>
                        <div><label><input type="checkbox" name="items_moto[]" value="cadena"> Tensión y lubricación de la cadena</label></div>
                        <div><label><input type="checkbox" name="items_moto[]" value="espejos"> Espejos retrovisores</label></div>
                        <div><label><input type="checkbox" name="items_moto[]" value="documentos_vehiculo"> Documentos de la moto (SOAT, tecno)</label></div>
                    </fieldset>
                    
                    <label for="foto_vehiculo" style="margin-top: 20px;">4. Adjuntar Foto del Vehículo (Opcional):</label>
                    <input type="file" id="foto_vehiculo" name="foto_vehiculo" accept="image/*">

                    <label for="observaciones" style="margin-top: 20px;">5. Observaciones Adicionales:</label>
                    <textarea id="observaciones" name="observaciones" rows="4"></textarea>

                    <label for="firma-pad" style="margin-top: 20px;">6. Firma de quien realiza la inspección:</label>
                    <div style="border: 1px solid #ccc; border-radius: 5px; max-width:400px;">
                        <canvas id="firma-pad" class="firma-pad" width="400" height="200"></canvas>
                    </div>
                    <button type="button" id="limpiar-firma" style="background-color: #6c757d; width: auto; padding: 5px 10px; font-size: 0.8em;">Limpiar Firma</button>
                    <input type="hidden" name="firma_base64" id="firma_base64">

                    <button type="submit" id="submit_preoperacional">Guardar Registro</button>
                </div>
            </form>
        </section>

        <hr style="margin: 40px 0;">

        <section>
            <h2>Consultar Registros Pre-operacionales</h2>
            <button id="btn-consultar-preoperacionales">Cargar Historial</button>
            <div id="resultados-preoperacional" style="margin-top: 20px;">
                </div>
        </section>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
</div>