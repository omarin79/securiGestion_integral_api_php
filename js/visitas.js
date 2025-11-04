document.addEventListener('DOMContentLoaded', () => {

    // --- LÓGICA EXCLUSIVA PARA EL MÓDULO DE VISITAS ---

    // Función para obtener las coordenadas GPS
    const btnGps = document.getElementById('btn-obtener-gps');
    if (btnGps) {
        btnGps.addEventListener('click', () => {
            const latInput = document.getElementById('visita-latitud');
            const lonInput = document.getElementById('visita-longitud');
            const statusDiv = document.getElementById('gps-status');

            if (navigator.geolocation) {
                statusDiv.textContent = 'Obteniendo coordenadas...';
                navigator.geolocation.getCurrentPosition(position => {
                    latInput.value = position.coords.latitude;
                    lonInput.value = position.coords.longitude;
                    statusDiv.textContent = 'Coordenadas obtenidas con éxito.';
                    statusDiv.style.color = 'green';
                }, error => {
                    statusDiv.textContent = `Error al obtener GPS: ${error.message}`;
                    statusDiv.style.color = 'red';
                });
            } else {
                statusDiv.textContent = 'La geolocalización no es soportada por este navegador.';
                statusDiv.style.color = 'red';
            }
        });
    }

    // Función para cargar el checklist dinámicamente
    const checklistSelect = document.getElementById('visita-checklist-tipo');
    if (checklistSelect) {
        checklistSelect.addEventListener('change', async () => {
            const checklistId = checklistSelect.value;
            const container = document.getElementById('checklist-container');
            container.innerHTML = '<p>Cargando checklist...</p>';

            if (!checklistId) {
                container.innerHTML = '';
                return;
            }

            try {
                // Forzamos al navegador a no usar la caché para esta solicitud
                const response = await fetch(`actions/get_checklist_items_action.php?id=${checklistId}`, {
                    cache: 'no-store'
                });

                if (!response.ok) {
                    throw new Error('Error en la respuesta del servidor.');
                }
                
                const items = await response.json();

                if (items.error) {
                    throw new Error(items.error);
                }
                
                if (items.length === 0) {
                     container.innerHTML = `<p>No se encontraron preguntas para este checklist.</p>`;
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
                        <div class="checklist-item" style="margin-bottom: 15px;">
                            <p><strong>${item.Pregunta}</strong></p>
                            <label style="display: inline-block; margin-right: 15px;">
                                <input type="radio" name="respuestas[${item.ID_Item}]" value="Si" required> Si
                            </label>
                            <label style="display: inline-block; margin-right: 15px;">
                                <input type="radio" name="respuestas[${item.ID_Item}]" value="No"> No
                            </label>
                            <label style="display: inline-block;">
                                <input type="radio" name="respuestas[${item.ID_Item}]" value="NA"> N/A
                            </label>
                        </div>
                    `;
                });
                container.innerHTML = html;
            } catch (error) {
                console.error("Error al cargar el checklist:", error);
                container.innerHTML = '<p style="color:red;">Ocurrió un error al cargar los items. Revisa la Consola (F12).</p>';
            }
        });
    }
});