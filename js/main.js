document.addEventListener('DOMContentLoaded', function() {

    // --- LÓGICA PARA CARGAR FORMULARIOS DE NOVEDADES DINÁMICAMENTE ---
    const tipoNovedadSelect = document.getElementById('tipo-novedad-registro');
    const formContainer = document.getElementById('novedad-form-container');

    // Función para generar el HTML del desplegable de clientes de forma segura
    function generarOpcionesClientes() {
        let opciones = '<option value="">Seleccione un Puesto...</option>';
        if (typeof listaClientes !== 'undefined' && Array.isArray(listaClientes)) {
            listaClientes.forEach(cliente => {
                opciones += `<option value="${cliente.ID_Cliente}">${cliente.NombreEmpresa}</option>`;
            });
        }
        return opciones;
    }
    
    // Función para resetear el formulario y volver a la selección
    window.resetNovedadForm = function() {
        if(tipoNovedadSelect) tipoNovedadSelect.value = '';
        if(formContainer) formContainer.innerHTML = '<p>Seleccione un tipo de novedad para ver el formulario.</p>';
    }

    const formsHtml = {
        'ausencia': `
            <form action="actions/novedad_action.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="tipo_novedad" value="ausencia">
                <h3>Registrar Ausencia de Unidad</h3>
                <label>Cédula de la Unidad Ausente:</label>
                <input type="text" name="cedula" data-autocomplete-nombre="nombre_unidad_ausencia" required>
                <label>Nombre de la Unidad:</label>
                <input type="text" id="nombre_unidad_ausencia" name="nombre_unidad" readonly>
                <label>Puesto de Trabajo Afectado:</label>
                <select name="puesto_afectado" required>${generarOpcionesClientes()}</select>
                <label>Turno Afectado:</label>
                <select name="turno_afectado" required><option value="">Seleccione...</option><option value="diurno">Diurno</option><option value="noche">Noche</option></select>
                <label>Fecha y Hora de la Ausencia:</label>
                <input type="datetime-local" name="fecha_inicio" required>
                <label>Observaciones Adicionales:</label>
                <textarea name="observaciones" rows="3"></textarea>
                <label>Evidencia (Opcional):</label>
                <input type="file" name="evidencia_ausencia" accept="image/*,application/pdf">
                <div class="form-buttons" style="display: flex; gap: 10px; margin-top: 20px;">
                    <button type="submit">Registrar Ausencia</button>
                    <button type="button" onclick="resetNovedadForm()" class="cancel-btn">Cancelar</button>
                </div>
            </form>`,
        'incapacidad': `
            <form action="actions/novedad_action.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="tipo_novedad" value="incapacidad">
                <h3>Registrar Incapacidad</h3>
                <label>Cédula del Empleado:</label>
                <input type="text" name="cedula" data-autocomplete-nombre="nombre_empleado_incapacidad" required>
                <label>Nombre del Empleado:</label>
                <input type="text" id="nombre_empleado_incapacidad" name="nombre_empleado" readonly>
                <label>Tipo de Incapacidad:</label>
                <select name="tipo_incapacidad" required><option value="">Seleccione...</option><option value="general">Enfermedad General</option><option value="laboral">Accidente Laboral</option><option value="maternidad">Maternidad/Paternidad</option></select>
                <label>Fecha de Inicio de Incapacidad:</label>
                <input type="date" name="fecha_inicio_incapacidad" required>
                <label>Número de Días Incapacidad:</label>
                <input type="number" name="dias_incapacidad" min="1" required>
                <label>Soporte Médico (Certificado):</label>
                <input type="file" name="soporte_incapacidad" accept="image/*,application/pdf" required>
                <div class="form-buttons" style="display: flex; gap: 10px; margin-top: 20px;">
                    <button type="submit">Registrar Incapacidad</button>
                    <button type="button" onclick="resetNovedadForm()" class="cancel-btn">Cancelar</button>
                </div>
            </form>`,
        'licencia-remunerada': `
            <form action="actions/novedad_action.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="tipo_novedad" value="licencia-remunerada">
                <h3>Registrar Licencia Remunerada</h3>
                <label>Cédula Empleado:</label>
                <input type="text" name="cedula" data-autocomplete-nombre="nombre_empleado_lic_rem" required>
                <label>Nombre Empleado:</label>
                <input type="text" id="nombre_empleado_lic_rem" name="nombre_empleado" readonly>
                <label>Motivo:</label>
                <select name="motivo_licencia" required><option value="">Seleccione...</option><option value="luto">Luto</option><option value="matrimonio">Matrimonio</option><option value="licencia_maternidad">Licencia Maternidad</option><option value="licencia_paternidad">Licencia Paternidad</option><option value="calamidad_domestica">Grave Calamidad Doméstica</option></select>
                <label>Fecha Inicio:</label>
                <input type="date" name="fecha_inicio_licencia" required>
                <label>Días:</label>
                <input type="number" name="dias_licencia" min="1" required>
                <label>Soporte:</label>
                <input type="file" name="soporte_licencia" accept="image/*,application/pdf">
                <div class="form-buttons" style="display: flex; gap: 10px; margin-top: 20px;">
                    <button type="submit">Registrar Licencia</button>
                    <button type="button" onclick="resetNovedadForm()" class="cancel-btn">Cancelar</button>
                </div>
            </form>`,
        'permiso-remunerado': `
            <form action="actions/novedad_action.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="tipo_novedad" value="permiso-remunerado">
                <h3>Registrar Permiso Remunerado</h3>
                <label>Cédula Empleado:</label>
                <input type="text" name="cedula" data-autocomplete-nombre="nombre_empleado_perm_rem" required>
                <label>Nombre Empleado:</label>
                <input type="text" id="nombre_empleado_perm_rem" name="nombre_empleado" readonly>
                <label>Motivo:</label>
                <textarea name="motivo_permiso" rows="2" required></textarea>
                <label>Fecha:</label>
                <input type="date" name="fecha_permiso" required>
                <label>Hora Inicio:</label>
                <input type="time" name="hora_inicio" required>
                <label>Hora Fin:</label>
                <input type="time" name="hora_fin" required>
                <div class="form-buttons" style="display: flex; gap: 10px; margin-top: 20px;">
                    <button type="submit">Registrar Permiso</button>
                    <button type="button" onclick="resetNovedadForm()" class="cancel-btn">Cancelar</button>
                </div>
            </form>`,
        'licencia-no-remunerada': `
            <form action="actions/novedad_action.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="tipo_novedad" value="licencia-no-remunerada">
                <h3>Registrar Licencia No Remunerada</h3>
                <label>Cédula Empleado:</label>
                <input type="text" name="cedula" data-autocomplete-nombre="nombre_empleado_lic_no_rem" required>
                <label>Nombre Empleado:</label>
                <input type="text" id="nombre_empleado_lic_no_rem" name="nombre_empleado" readonly>
                <label>Motivo:</label>
                <textarea name="motivo_licencia" rows="3" required></textarea>
                <label>Fecha Inicio:</label>
                <input type="date" name="fecha_inicio_licencia" required>
                <label>Días (Estimado):</label>
                <input type="number" name="dias_licencia" min="1" required>
                <div class="form-buttons" style="display: flex; gap: 10px; margin-top: 20px;">
                    <button type="submit">Registrar Licencia</button>
                    <button type="button" onclick="resetNovedadForm()" class="cancel-btn">Cancelar</button>
                </div>
            </form>`,
        'permiso-no-remunerado': `
            <form action="actions/novedad_action.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="tipo_novedad" value="permiso-no-remunerado">
                <h3>Registrar Permiso No Remunerado</h3>
                <label>Cédula Empleado:</label>
                <input type="text" name="cedula" data-autocomplete-nombre="nombre_empleado_perm_no_rem" required>
                <label>Nombre Empleado:</label>
                <input type="text" id="nombre_empleado_perm_no_rem" name="nombre_empleado" readonly>
                <label>Motivo:</label>
                <textarea name="motivo_permiso" rows="2" required></textarea>
                <label>Fecha:</label>
                <input type="date" name="fecha_permiso" required>
                <label>Hora Inicio:</label>
                <input type="time" name="hora_inicio" required>
                <label>Hora Fin:</label>
                <input type="time" name="hora_fin" required>
                <div class="form-buttons" style="display: flex; gap: 10px; margin-top: 20px;">
                    <button type="submit">Registrar Permiso</button>
                    <button type="button" onclick="resetNovedadForm()" class="cancel-btn">Cancelar</button>
                </div>
            </form>`,
        'unidad-evadida': `
            <form action="actions/novedad_action.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="tipo_novedad" value="unidad-evadida">
                <h3>Registrar Unidad Evadida</h3>
                <label>Cédula Unidad Evadida:</label>
                <input type="text" name="cedula" data-autocomplete-nombre="nombre_unidad_evadida" required>
                <label>Nombre Unidad Evadida:</label>
                <input type="text" id="nombre_unidad_evadida" name="nombre_unidad" readonly>
                <label>Puesto:</label>
                <input type="text" name="puesto_evadido" required>
                <label>Hora Evasión (Estimada):</label>
                <input type="time" name="hora_evasion" required>
                <label>Circunstancias:</label>
                <textarea name="circunstancias" rows="4" required></textarea>
                <div class="form-buttons" style="display: flex; gap: 10px; margin-top: 20px;">
                    <button type="submit">Registrar Novedad</button>
                    <button type="button" onclick="resetNovedadForm()" class="cancel-btn">Cancelar</button>
                </div>
            </form>`,
        'condicion-insegura': `
            <form action="actions/novedad_action.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="tipo_novedad" value="condicion-insegura">
                <h3>Reporte de Condición Insegura</h3>
                <label>Ubicación/Puesto:</label>
                <input type="text" name="ubicacion" required>
                <label>Descripción Detallada:</label>
                <textarea name="descripcion" rows="5" required></textarea>
                <label>Cédula Reportante:</label>
                <input type="text" name="cedula_reportante" data-autocomplete-nombre="nombre_reportante_condicion" required>
                <label>Nombre Reportante:</label>
                <input type="text" id="nombre_reportante_condicion" name="nombre_reportante" readonly>
                <label>Evidencia Fotográfica (Opcional):</label>
                <input type="file" name="evidencia_condicion" accept="image/*">
                <div class="form-buttons" style="display: flex; gap: 10px; margin-top: 20px;">
                    <button type="submit">Reportar Condición</button>
                    <button type="button" onclick="resetNovedadForm()" class="cancel-btn">Cancelar</button>
                </div>
            </form>`
    };

    if (tipoNovedadSelect) {
        tipoNovedadSelect.addEventListener('change', () => {
            const selectedType = tipoNovedadSelect.value;
            if (formContainer && selectedType && formsHtml[selectedType]) {
                formContainer.innerHTML = formsHtml[selectedType];
                activarAutocompleteEnNuevosInputs();
            } else if (formContainer) {
                formContainer.innerHTML = '<p>Seleccione un tipo de novedad para ver el formulario.</p>';
            }
        });
    }

    // --- LÓGICA PARA CONSULTAR ÚLTIMAS NOVEDADES ---
    const btnConsultarNovedades = document.getElementById('btn-consultar-novedades');
    const resultadosNovedadesContainer = document.getElementById('resultados-novedades');

    if (btnConsultarNovedades) {
        btnConsultarNovedades.addEventListener('click', function() {
            resultadosNovedadesContainer.innerHTML = '<p>Buscando novedades...</p>';
            fetch('actions/consulta_novedades_action.php')
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    resultadosNovedadesContainer.innerHTML = `<p style="color: red;">Error: ${data.error}</p>`;
                    return;
                }
                if (data.length === 0) {
                    resultadosNovedadesContainer.innerHTML = '<p>No se encontraron novedades registradas.</p>';
                    return;
                }
                let tablaHTML = `
                    <table style="width:100%; border-collapse: collapse; margin-top: 15px;">
                        <thead>
                            <tr style="background-color: #f2f2f2;">
                                <th style="border: 1px solid #ddd; padding: 8px;">ID</th>
                                <th style="border: 1px solid #ddd; padding: 8px;">Tipo</th>
                                <th style="border: 1px solid #ddd; padding: 8px;">Afectado</th>
                                <th style="border: 1px solid #ddd; padding: 8px;">Fecha</th>
                                <th style="border: 1px solid #ddd; padding: 8px;">Reporta</th>
                                <th style="border: 1px solid #ddd; padding: 8px;">Estado</th>
                                <th style="border: 1px solid #ddd; padding: 8px;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                `;
                data.forEach(novedad => {
                    tablaHTML += `
                        <tr>
                            <td style="border: 1px solid #ddd; padding: 8px;">${novedad.ID_Novedad}</td>
                            <td style="border: 1px solid #ddd; padding: 8px;">${novedad.TipoNovedad}</td>
                            <td style="border: 1px solid #ddd; padding: 8px;">${novedad.PersonalAfectado}</td>
                            <td style="border: 1px solid #ddd; padding: 8px;">${novedad.FechaHoraRegistro}</td>
                            <td style="border: 1px solid #ddd; padding: 8px;">${novedad.UsuarioReporta}</td>
                            <td style="border: 1px solid #ddd; padding: 8px;">${novedad.EstadoNovedad}</td>
                            <td style="border: 1px solid #ddd; padding: 8px; text-align: center;">
                                <a href="index.php?page=detalle-novedad&id_novedad=${novedad.ID_Novedad}" style="text-decoration: none; padding: 5px 10px; background-color: #28a745; color: white; border-radius: 4px;">Ver Detalles</a>
                            </td>
                        </tr>
                    `;
                });
                tablaHTML += '</tbody></table>';
                resultadosNovedadesContainer.innerHTML = tablaHTML;
            })
            .catch(error => {
                console.error('Error en la solicitud:', error);
                resultadosNovedadesContainer.innerHTML = '<p style="color: red;">Ocurrió un error al conectar con el servidor.</p>';
            });
        });
    }

    // --- LÓGICA DE AUTOCOMPLETAR (FUNCIÓN REUTILIZABLE) ---
    function activarAutocompleteEnNuevosInputs() {
        const autocompleteInputs = document.querySelectorAll('input[data-autocomplete-nombre]');
        autocompleteInputs.forEach(inputCedula => {
            if (inputCedula.getAttribute('data-listener-added') === 'true') return;

            const idInputNombre = inputCedula.dataset.autocompleteNombre;
            const inputNombre = document.getElementById(idInputNombre);
            
            let resultsContainer = inputCedula.parentNode.querySelector('.autocomplete-results');
            if (!resultsContainer) {
                resultsContainer = document.createElement('div');
                resultsContainer.className = 'autocomplete-results';
                inputCedula.parentNode.style.position = 'relative';
                inputCedula.parentNode.appendChild(resultsContainer);
            }

            inputCedula.addEventListener('input', async () => {
                const term = inputCedula.value;
                resultsContainer.innerHTML = '';
                if (term.length < 2) return;

                try {
                    const response = await fetch(`actions/autocomplete_action.php?term=${term}`);
                    if (!response.ok) return;
                    const suggestions = await response.json();

                    suggestions.forEach(suggestion => {
                        const suggestionDiv = document.createElement('div');
                        suggestionDiv.className = 'autocomplete-suggestion';
                        suggestionDiv.textContent = suggestion.label;
                        
                        suggestionDiv.addEventListener('click', () => {
                            inputCedula.value = suggestion.value;
                            if (inputNombre) inputNombre.value = suggestion.nombre;
                            resultsContainer.innerHTML = '';
                        });
                        resultsContainer.appendChild(suggestionDiv);
                    });
                } catch (error) {
                    console.error("Error en autocompletar:", error);
                }
            });

            document.addEventListener('click', (e) => {
                if (e.target !== inputCedula) {
                    resultsContainer.innerHTML = '';
                }
            });
            inputCedula.setAttribute('data-listener-added', 'true');
        });
    }
    
    activarAutocompleteEnNuevosInputs();

    // --- LÓGICA EXCLUSIVA PARA EL MÓDULO DE VISITAS ---
    const checklistSelect = document.getElementById('visita-checklist-tipo');
    const checklistContainer = document.getElementById('checklist-container');

    if (checklistSelect && checklistContainer) {
        checklistSelect.addEventListener('change', () => {
            const checklistId = checklistSelect.value;
            checklistContainer.innerHTML = ''; 

            if (!checklistId) {
                return;
            }
            checklistContainer.innerHTML = '<p>Cargando preguntas...</p>';

            fetch(`actions/get_checklist_items_action.php?id_checklist=${checklistId}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`Error del servidor: ${response.status}`);
                    }
                    return response.json();
                })
                .then(items => {
                    if (items.error) {
                        checklistContainer.innerHTML = `<p style="color: orange;">Aviso: ${items.error}</p>`;
                        return;
                    }

                    let html = '';
                    let currentSection = '';

                    items.forEach(item => {
                        if(item.Seccion !== currentSection) {
                            currentSection = item.Seccion;
                            html += `<h3 style="margin-top: 20px; border-top: 1px solid #eee; padding-top: 15px;">${item.Seccion}</h3>`;
                        }
                        html += `
                            <div class="checklist-item" style="margin-bottom: 15px; padding: 10px; border-left: 3px solid #eee;">
                                <p><strong>${item.Pregunta}</strong></p>
                                <div style="display: flex; align-items: center; gap: 20px; flex-wrap: wrap;">
                                    <div class="respuesta-options">
                                        <label style="display: inline-block; margin-right: 15px;"><input type="radio" name="respuestas[${item.ID_Item}]" value="Si" required> Si</label>
                                        <label style="display: inline-block; margin-right: 15px;"><input type="radio" name="respuestas[${item.ID_Item}]" value="No"> No</label>
                                        <label style="display: inline-block;"><input type="radio" name="respuestas[${item.ID_Item}]" value="NA"> N/A</label>
                                    </div>
                                    <div class="evidencia-upload">
                                        <label for="evidencia-${item.ID_Item}" style="font-size: 0.8em; color: #555; cursor: pointer;">Adjuntar Foto (Opcional):</label>
                                        <input type="file" id="evidencia-${item.ID_Item}" name="evidencias[${item.ID_Item}]" accept="image/*" style="display: inline-block; width: auto;">
                                    </div>
                                </div>
                            </div>
                        `;
                    });
                    checklistContainer.innerHTML = html;
                })
                .catch(error => {
                    console.error('Error en la solicitud fetch:', error);
                    checklistContainer.innerHTML = '<p style="color: red;">Ocurrió un error al cargar los ítems. Revisa la Consola (F12).</p>';
                });
        });
    }

    const btnConsultarVisitas = document.getElementById('btn-consultar-visitas');
    const resultadosVisitasContainer = document.getElementById('resultados-visitas-container');

    if (btnConsultarVisitas) {
        btnConsultarVisitas.addEventListener('click', function() {
            resultadosVisitasContainer.innerHTML = '<p>Buscando visitas registradas...</p>';

            fetch('actions/consulta_visitas_action.php')
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    resultadosVisitasContainer.innerHTML = `<p style="color: red;">Error: ${data.error}</p>`;
                    return;
                }
                if (data.length === 0) {
                    resultadosVisitasContainer.innerHTML = '<p>No se encontraron visitas registradas.</p>';
                    return;
                }
                let tablaHTML = `
                    <table style="width:100%; border-collapse: collapse; margin-top: 15px;">
                        <thead>
                            <tr style="background-color: #f2f2f2;">
                                <th style="border: 1px solid #ddd; padding: 8px;">ID</th>
                                <th style="border: 1px solid #ddd; padding: 8px;">Fecha</th>
                                <th style="border: 1px solid #ddd; padding: 8px;">Supervisor</th>
                                <th style="border: 1px solid #ddd; padding: 8px;">Auditado</th>
                                <th style="border: 1px solid #ddd; padding: 8px;">Cliente</th>
                                <th style="border: 1px solid #ddd; padding: 8px;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                `;
                data.forEach(visita => {
                    tablaHTML += `
                        <tr>
                            <td style="border: 1px solid #ddd; padding: 8px;">${visita.ID_Visita}</td>
                            <td style="border: 1px solid #ddd; padding: 8px;">${visita.FechaVisita}</td>
                            <td style="border: 1px solid #ddd; padding: 8px;">${visita.Supervisor}</td>
                            <td style="border: 1px solid #ddd; padding: 8px;">${visita.Auditado}</td>
                            <td style="border: 1px solid #ddd; padding: 8px;">${visita.Cliente}</td>
                            <td style="border: 1px solid #ddd; padding: 8px; text-align: center;">
                                <a href="index.php?page=detalle-visita&id_visita=${visita.ID_Visita}" style="text-decoration: none; padding: 5px 10px; background-color: #007bff; color: white; border-radius: 4px;">Ver Detalles</a>
                            </td>
                        </tr>
                    `;
                });
                tablaHTML += '</tbody></table>';
                resultadosVisitasContainer.innerHTML = tablaHTML;
            })
            .catch(error => {
                console.error('Error en la solicitud:', error);
                resultadosVisitasContainer.innerHTML = '<p style="color: red;">Ocurrió un error al conectar con el servidor.</p>';
            });
        });
    }

    // --- LÓGICA PARA LA PÁGINA DE PROGRAMACIÓN Y RASTREO ---
    const formBuscarReemplazo = document.getElementById('form-buscar-reemplazo');
    if (formBuscarReemplazo) {
        formBuscarReemplazo.addEventListener('submit', function(e) {
            e.preventDefault(); // Evitamos que la página se recargue

            const resultadosContainer = document.getElementById('lista-unidades');
            resultadosContainer.innerHTML = '<p>Buscando unidades disponibles...</p>';

            const formData = new FormData(this);

            fetch('actions/programacion_action.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    resultadosContainer.innerHTML = `<p style="color: red;">${data.error}</p>`;
                    return;
                }

                if (data.length === 0) {
                    resultadosContainer.innerHTML = '<p>No se encontraron unidades disponibles que cumplan con los criterios.</p>';
                    return;
                }

                let html = '<h4>Se ha notificado a las siguientes unidades:</h4><ul style="list-style-type: none; padding: 0;">';
                data.forEach(unidad => {
                    html += `
                        <li style="background-color: #f9f9f9; border: 1px solid #ddd; padding: 10px; margin-bottom: 10px; border-radius: 5px;">
                            <strong>${unidad.Nombre} ${unidad.Apellido}</strong> (C.C. ${unidad.DocumentoIdentidad})
                            <br>
                            <small>Estado: Notificación Enviada al Tel: ${unidad.Telefono || 'N/A'}</small>
                            <div style="margin-top: 5px;">
                                <button class="btn-accept" data-cedula="${unidad.DocumentoIdentidad}">Aceptar Turno</button>
                                <button class="btn-reject" data-cedula="${unidad.DocumentoIdentidad}">Rechazar</button>
                            </div>
                        </li>
                    `;
                });
                html += '</ul>';
                
                resultadosContainer.innerHTML = html;
            })
            .catch(error => {
                console.error('Error:', error);
                resultadosContainer.innerHTML = '<p style="color: red;">Ocurrió un error al procesar la solicitud.</p>';
            });
        });
    }

});
// --- LÓGICA PARA LA PÁGINA DE REPORTE DISCIPLINARIO ---
    const selectTipoFalta = document.getElementById('tipo-falta');
    if (selectTipoFalta) {
        selectTipoFalta.addEventListener('change', function() {
            const campoAdjunto = document.getElementById('campo-adjunto');
            // Muestra el campo si se selecciona "Leve" o "Grave"
            if (this.value === 'Leve' || this.value === 'Grave') {
                campoAdjunto.style.display = 'block';
            } else {
                campoAdjunto.style.display = 'none';
            }
        });
    }
    // --- LÓGICA PARA CONSULTAR REPORTES DISCIPLINARIOS ---
    const btnCargarReportes = document.getElementById('btn-cargar-reportes');
    if (btnCargarReportes) {
        btnCargarReportes.addEventListener('click', function() {
            const resultadosContainer = document.getElementById('resultados-reportes');
            resultadosContainer.innerHTML = '<p>Cargando reportes...</p>';

            fetch('actions/consulta_reportes_action.php')
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        resultadosContainer.innerHTML = `<p style="color: red;">${data.error}</p>`;
                        return;
                    }

                    if (data.length === 0) {
                        resultadosContainer.innerHTML = '<p>No se encontraron reportes disciplinarios.</p>';
                        return;
                    }

                    let tablaHTML = `
                        <table style="width:100%; border-collapse: collapse;">
                            <thead>
                                <tr style="background-color: #f2f2f2;">
                                    <th style="border: 1px solid #ddd; padding: 8px;">ID</th>
                                    <th style="border: 1px solid #ddd; padding: 8px;">Fecha</th>
                                    <th style="border: 1px solid #ddd; padding: 8px;">Personal Afectado</th>
                                    <th style="border: 1px solid #ddd; padding: 8px;">Reportado por</th>
                                    <th style="border: 1px solid #ddd; padding: 8px;">Estado</th>
                                    <th style="border: 1px solid #ddd; padding: 8px;">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>`;
                    
                    data.forEach(reporte => {
                        tablaHTML += `
                            <tr>
                                <td style="border: 1px solid #ddd; padding: 8px;">${reporte.ID_Novedad}</td>
                                <td style="border: 1px solid #ddd; padding: 8px;">${reporte.FechaHoraRegistro}</td>
                                <td style="border: 1px solid #ddd; padding: 8px;">${reporte.PersonalAfectado}</td>
                                <td style="border: 1px solid #ddd; padding: 8px;">${reporte.UsuarioReporta}</td>
                                <td style="border: 1px solid #ddd; padding: 8px;">${reporte.EstadoNovedad}</td>
                                <td style="border: 1px solid #ddd; padding: 8px; text-align:center;">
                                    <a href="index.php?page=detalle_reporte&id_novedad=${reporte.ID_Novedad}" class="btn-secondary" style="padding: 5px 10px; background-color:#007bff; color:white; text-decoration:none; border-radius:4px; font-size:0.9em;">Ver Detalles</a>
                                </td>
                            </tr>
                        `;
                    });

                    tablaHTML += '</tbody></table>';
                    resultadosContainer.innerHTML = tablaHTML;
                })
                .catch(error => {
                    console.error('Error al cargar reportes:', error);
                    resultadosContainer.innerHTML = '<p style="color: red;">Ocurrió un error de red.</p>';
                });
        });
    }
    // --- LÓGICA PARA EL CALENDARIO DE PROGRAMACIÓN GENERAL ---
    const calendarElGeneral = document.getElementById('calendario-general-container');
    if (calendarElGeneral) {
        let calendar = new FullCalendar.Calendar(calendarElGeneral, {
            initialView: 'dayGridMonth',
            locale: 'es',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,listWeek'
            },
            events: 'actions/consulta_programacion_general_action.php',
            eventDidMount: function(info) {
                // Tooltip para ver el nombre del cliente
                if (info.event.extendedProps.cliente) {
                    info.el.setAttribute('title', info.event.extendedProps.cliente);
                }
            }
        });
        calendar.render();

        // Lógica para el botón de filtrar
        document.getElementById('btn-filtrar-programacion').addEventListener('click', () => {
            const cedula = document.getElementById('cedula-empleado-prog').value;
            if (cedula) {
                calendar.setOption('events', `actions/consulta_programacion_general_action.php?cedula=${cedula}`);
            }
        });

        // Lógica para el botón de mostrar todos
        document.getElementById('btn-reset-programacion').addEventListener('click', () => {
            document.getElementById('cedula-empleado-prog').value = '';
            document.getElementById('nombre-empleado-prog').value = '';
            calendar.setOption('events', 'actions/consulta_programacion_general_action.php');
        });
    }
    // --- LÓGICA PARA CONSULTAR DOCUMENTOS SOLICITADOS EN TALENTO HUMANO ---
    const formConsultarDocs = document.getElementById('form-consultar-documentos-th');
    if (formConsultarDocs) {
        formConsultarDocs.addEventListener('submit', function(e) {
            e.preventDefault(); // Prevenir el envío tradicional del formulario
            
            const resultadosContainer = document.getElementById('resultados-documentos');
            resultadosContainer.innerHTML = '<p>Buscando documentos...</p>';
            
            const formData = new FormData(this);

            fetch('actions/consulta_documentos_action.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    resultadosContainer.innerHTML = `<p style="color: red;">${data.error}</p>`;
                    return;
                }

                if (data.length === 0) {
                    resultadosContainer.innerHTML = '<p>No se encontraron documentos solicitados para esta cédula.</p>';
                    return;
                }

                let tablaHTML = `
                    <h4>Historial de Solicitudes</h4>
                    <table style="width:100%; border-collapse: collapse;">
                        <thead>
                            <tr style="background-color: #f2f2f2;">
                                <th style="border: 1px solid #ddd; padding: 8px;">Tipo de Documento</th>
                                <th style="border: 1px solid #ddd; padding: 8px;">Fecha de Solicitud</th>
                                <th style="border: 1px solid #ddd; padding: 8px;">Estado</th>
                            </tr>
                        </thead>
                        <tbody>`;
                
                data.forEach(doc => {
                    tablaHTML += `
                        <tr>
                            <td style="border: 1px solid #ddd; padding: 8px;">${doc.NombreDocumento}</td>
                            <td style="border: 1px solid #ddd; padding: 8px;">${doc.FechaHoraSolicitud}</td>
                            <td style="border: 1px solid #ddd; padding: 8px;">${doc.EstadoSolicitud}</td>
                        </tr>
                    `;
                });

                tablaHTML += '</tbody></table>';
                resultadosContainer.innerHTML = tablaHTML;
            })
            .catch(error => {
                console.error('Error en la consulta:', error);
                resultadosContainer.innerHTML = '<p style="color: red;">Ocurrió un error al conectar con el servidor.</p>';
            });
        });
    }
    // --- LÓGICA PARA CONSULTAR DOCUMENTOS Y NOVEDADES EN EL MÓDULO DE NÓMINA ---
    const formConsultaNomina = document.getElementById('form-consulta-nomina');
    if (formConsultaNomina) {
        formConsultaNomina.addEventListener('submit', function(e) {
            e.preventDefault();

            const resultadosContainer = document.getElementById('resultados-nomina-container');
            resultadosContainer.innerHTML = '<p>Consultando información...</p>';

            const formData = new FormData(this);

            fetch('actions/consulta_nomina_action.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    resultadosContainer.innerHTML = `<p style="color: red;">${data.error}</p>`;
                    return;
                }

                let html = '';

                // Tabla de Documentos Solicitados
                html += '<h4>Historial de Documentos Solicitados</h4>';
                if (data.documentos && data.documentos.length > 0) {
                    html += `
                        <table style="width:100%; border-collapse: collapse; margin-bottom: 20px;">
                            <thead>
                                <tr style="background-color: #f2f2f2;">
                                    <th style="border: 1px solid #ddd; padding: 8px;">Tipo de Documento</th>
                                    <th style="border: 1px solid #ddd; padding: 8px;">Fecha de Solicitud</th>
                                    <th style="border: 1px solid #ddd; padding: 8px;">Estado</th>
                                </tr>
                            </thead>
                            <tbody>`;
                    data.documentos.forEach(doc => {
                        html += `
                            <tr>
                                <td style="border: 1px solid #ddd; padding: 8px;">${doc.NombreDocumento}</td>
                                <td style="border: 1px solid #ddd; padding: 8px;">${doc.FechaHoraSolicitud}</td>
                                <td style="border: 1px solid #ddd; padding: 8px;">${doc.EstadoSolicitud}</td>
                            </tr>`;
                    });
                    html += '</tbody></table>';
                } else {
                    html += '<p>No se encontraron documentos solicitados para esta cédula.</p>';
                }

                // Tabla de Novedades de Nómina
                html += '<h4 style="margin-top: 30px;">Historial de Novedades de Nómina</h4>';
                if (data.novedades && data.novedades.length > 0) {
                    html += `
                        <table style="width:100%; border-collapse: collapse;">
                            <thead>
                                <tr style="background-color: #f2f2f2;">
                                    <th style="border: 1px solid #ddd; padding: 8px;">Tipo de Novedad</th>
                                    <th style="border: 1px solid #ddd; padding: 8px;">Fecha de Registro</th>
                                    <th style="border: 1px solid #ddd; padding: 8px;">Estado</th>
                                </tr>
                            </thead>
                            <tbody>`;
                    data.novedades.forEach(novedad => {
                        html += `
                            <tr>
                                <td style="border: 1px solid #ddd; padding: 8px;">${novedad.TipoNovedad}</td>
                                <td style="border: 1px solid #ddd; padding: 8px;">${novedad.FechaHoraRegistro}</td>
                                <td style="border: 1px solid #ddd; padding: 8px;">${novedad.EstadoNovedad}</td>
                            </tr>`;
                    });
                    html += '</tbody></table>';
                } else {
                    html += '<p>No se encontraron novedades de nómina (ausencias, licencias, etc.) para esta cédula.</p>';
                }

                resultadosContainer.innerHTML = html;
            })
            .catch(error => {
                console.error('Error en la consulta de nómina:', error);
                resultadosContainer.innerHTML = '<p style="color: red;">Ocurrió un error al conectar con el servidor.</p>';
            });
        });
    }
    // --- LÓGICA COMPLETA PARA PRE-OPERACIONAL (FORMULARIO Y CONSULTA) ---
    const preoperacionalPage = document.getElementById('preoperacional-vehiculos-page');
    if (preoperacionalPage) {
        // Lógica del formulario dinámico
        const tipoVehiculoSelect = document.getElementById('tipo_vehiculo');
        const checklistCarro = document.getElementById('checklist_carro');
        const checklistMoto = document.getElementById('checklist_moto');
        const camposVehiculo = document.getElementById('campos-vehiculo');
        
        tipoVehiculoSelect.addEventListener('change', function() {
            checklistCarro.style.display = 'none';
            checklistMoto.style.display = 'none';
            camposVehiculo.style.display = 'none';

            if (this.value === 'Carro') {
                checklistCarro.style.display = 'block';
                camposVehiculo.style.display = 'block';
            } else if (this.value === 'Moto') {
                checklistMoto.style.display = 'block';
                camposVehiculo.style.display = 'block';
            }
        });

        // Lógica de la firma
        const canvas = document.getElementById('firma-pad');
        const signaturePad = new SignaturePad(canvas, {
            backgroundColor: 'rgb(255, 255, 255)'
        });

        document.getElementById('limpiar-firma').addEventListener('click', () => {
            signaturePad.clear();
        });

        document.getElementById('preoperacional-form').addEventListener('submit', function(event) {
            if (!signaturePad.isEmpty()) {
                document.getElementById('firma_base64').value = signaturePad.toDataURL('image/png');
            } else {
                alert("Por favor, debe proporcionar la firma.");
                event.preventDefault(); // Detiene el envío si no hay firma
            }
        });

        // Lógica para el botón de consulta
        const btnConsultar = document.getElementById('btn-consultar-preoperacionales');
        const resultadosContainer = document.getElementById('resultados-preoperacional');

        btnConsultar.addEventListener('click', function() {
            resultadosContainer.innerHTML = '<p>Cargando historial...</p>';
            fetch('actions/consulta_preoperacional_action.php')
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    resultadosContainer.innerHTML = `<p style="color: red;">${data.error}</p>`;
                    return;
                }
                if (data.length === 0) {
                    resultadosContainer.innerHTML = '<p>No se encontraron registros.</p>';
                    return;
                }
                let tablaHTML = `
                    <table style="width:100%; border-collapse: collapse;">
                        <thead>
                            <tr style="background-color: #f2f2f2;">
                                <th style="border: 1px solid #ddd; padding: 8px;">ID</th>
                                <th style="border: 1px solid #ddd; padding: 8px;">Fecha</th>
                                <th style="border: 1px solid #ddd; padding: 8px;">Placa</th>
                                <th style="border: 1px solid #ddd; padding: 8px;">Vehículo</th>
                                <th style="border: 1px solid #ddd; padding: 8px;">Inspector</th>
                            </tr>
                        </thead>
                        <tbody>`;
                data.forEach(reg => {
                    tablaHTML += `
                        <tr>
                            <td style="border: 1px solid #ddd; padding: 8px;">${reg.ID_Registro}</td>
                            <td style="border: 1px solid #ddd; padding: 8px;">${reg.FechaHora}</td>
                            <td style="border: 1px solid #ddd; padding: 8px;">${reg.Placa}</td>
                            <td style="border: 1px solid #ddd; padding: 8px;">${reg.Marca} ${reg.Modelo}</td>
                            <td style="border: 1px solid #ddd; padding: 8px;">${reg.Usuario}</td>
                        </tr>`;
                });
                tablaHTML += '</tbody></table>';
                resultadosContainer.innerHTML = tablaHTML;
            })
            .catch(error => {
                console.error('Error:', error);
                resultadosContainer.innerHTML = '<p style="color: red;">Error de red al consultar los registros.</p>';
            });
        });
    }
    // --- LÓGICA CORREGIDA PARA LA PÁGINA DE REPORTE DISCIPLINARIO CON FIRMAS ---
    const formReporteDisciplinario = document.getElementById('form-reporte-disciplinario');
    if (formReporteDisciplinario) {
        
        const selectTipoFalta = document.getElementById('tipo-falta');
        const campoAdjunto = document.getElementById('campo-adjunto');
        
        // Listener para mostrar/ocultar el campo de adjuntar foto
        selectTipoFalta.addEventListener('change', function() {
            if (this.value === 'Leve' || this.value === 'Grave' || this.value === 'Gravísima') {
                campoAdjunto.style.display = 'block';
            } else {
                campoAdjunto.style.display = 'none';
            }
        });

        // Configuración de los pads de firma
        const canvasEmpleado = document.getElementById('firma-empleado-pad');
        const signaturePadEmpleado = new SignaturePad(canvasEmpleado, { backgroundColor: 'rgb(255, 255, 255)' });

        const canvasReporta = document.getElementById('firma-reporta-pad');
        const signaturePadReporta = new SignaturePad(canvasReporta, { backgroundColor: 'rgb(255, 255, 255)' });

        // Lógica para los botones de limpiar firma
        document.querySelectorAll('.limpiar-firma').forEach(button => {
            button.addEventListener('click', function() {
                const padId = this.getAttribute('data-pad');
                if (padId === 'firma-empleado-pad') {
                    signaturePadEmpleado.clear();
                } else if (padId === 'firma-reporta-pad') {
                    signaturePadReporta.clear();
                }
            });
        });

        // Antes de enviar el formulario, convertir las firmas a dataURL
        formReporteDisciplinario.addEventListener('submit', function(event) {
            // Validar que ambas firmas se hayan realizado
            if (signaturePadEmpleado.isEmpty() || signaturePadReporta.isEmpty()) {
                alert("Ambas firmas son obligatorias para registrar el reporte.");
                event.preventDefault(); // Detiene el envío del formulario
                return;
            }
            // Asignar los valores a los campos ocultos
            document.getElementById('firma_empleado_base64').value = signaturePadEmpleado.toDataURL('image/png');
            document.getElementById('firma_reporta_base64').value = signaturePadReporta.toDataURL('image/png');
        });
    }