(function() {
    const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    
    // Elementos Paso 1
    const selectCurso = document.getElementById('selectCurso');
    const campoBusqueda = document.getElementById('campoBusqueda');
    const textoBusqueda = document.getElementById('textoBusqueda');
    const btnBuscar = document.getElementById('btnBuscar');
    const listaResultados = document.getElementById('listaResultados');
    const msgSinEstudiante = document.getElementById('msgSinEstudiante');
    const errorPaso1 = document.getElementById('errorPaso1');
    const btnContinuar = document.getElementById('btnContinuar');
    
    // Elementos Paso 2
    const paso1 = document.getElementById('paso1');
    const paso2 = document.getElementById('paso2');
    const step1badge = document.getElementById('step1badge');
    const step2badge = document.getElementById('step2badge');
    const resumenEstudiante = document.getElementById('resumenEstudiante');
    const btnVolver = document.getElementById('btnVolver');
    const formPago = document.getElementById('formPago');
    const bloqueCuota = document.getElementById('bloqueCuota');
    const nroCuota = document.getElementById('nroCuota');
    const errorPaso2 = document.getElementById('errorPaso2');
    
    let selectedStudent = null;
    let selectedCursoLabel = '';

    // Mostrar/ocultar pasos
    function setStep(step) {
        if (step === 1) {
            paso1.hidden = false;
            paso2.hidden = true;
            step1badge.classList.add('active');
            step2badge.classList.remove('active');
        } else {
            paso1.hidden = true;
            paso2.hidden = false;
            step1badge.classList.remove('active');
            step2badge.classList.add('active');
        }
    }

    // Buscar estudiantes
    btnBuscar.addEventListener('click', async function() {
        errorPaso1.hidden = true;
        const idCur = selectCurso.value;
        if (!idCur) {
            showError('Seleccione un curso.', errorPaso1);
            return;
        }
        const q = textoBusqueda.value.trim();
        if (!q) {
            showError('Ingrese un termino de busqueda.', errorPaso1);
            return;
        }
        
        listaResultados.innerHTML = '<p class="hint" style="padding:12px">Buscando...</p>';
        listaResultados.hidden = false;
        msgSinEstudiante.hidden = true;
        selectedStudent = null;
        
        try {
            const params = new URLSearchParams({
                Id_Cur: idCur,
                campo: campoBusqueda.value,
                q: q
            });
            const response = await fetch('/api/estudiantes/buscar?' + params.toString(), {
                headers: { 'Accept': 'application/json' }
            });
            const data = await response.json();
            
            if (!response.ok) {
                const err = data.errors ? Object.values(data.errors).flat().join(' ') : (data.message || 'Error en la busqueda');
                showError(err, errorPaso1);
                listaResultados.hidden = true;
                msgSinEstudiante.hidden = false;
                return;
            }
            
            const rows = data.data || [];
            listaResultados.innerHTML = '';
            
            if (!rows.length) {
                listaResultados.innerHTML = '<p class="hint" style="padding:12px">No hay resultados.</p>';
                return;
            }
            
            rows.forEach(function(row) {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'search-result-item';
                btn.dataset.id = row.Id_E;
                btn.dataset.nombre = row.nombre_completo;
                btn.textContent = row.nombre_completo + ' — Reg. ' + row.RegistroE + ' — CI ' + row.CedulaE;
                btn.addEventListener('click', function() {
                    listaResultados.querySelectorAll('.search-result-item').forEach(function(x) {
                        x.classList.remove('selected');
                    });
                    btn.classList.add('selected');
                    selectedStudent = {
                        Id_E: Number(btn.dataset.id),
                        nombre_completo: btn.dataset.nombre
                    };
                });
                listaResultados.appendChild(btn);
            });
        } catch (e) {
            showError('No se pudo completar la busqueda.', errorPaso1);
            listaResultados.hidden = true;
            msgSinEstudiante.hidden = false;
        }
    });

    // Continuar al paso 2
    btnContinuar.addEventListener('click', function() {
        errorPaso1.hidden = true;
        if (!selectCurso.value) {
            showError('Seleccione un curso.', errorPaso1);
            return;
        }
        if (!selectedStudent) {
            showError('Seleccione un estudiante de la lista de resultados.', errorPaso1);
            return;
        }
        
        selectedCursoLabel = selectCurso.options[selectCurso.selectedIndex]?.text || '';
        resumenEstudiante.textContent = 'Estudiante: ' + selectedStudent.nombre_completo + ' — Curso: ' + selectedCursoLabel;
        
        document.getElementById('Id_E').value = selectedStudent.Id_E;
        document.getElementById('Id_Cur').value = selectCurso.value;
        
        setStep(2);
    });

    // Volver al paso 1
    btnVolver.addEventListener('click', function() {
        errorPaso2.hidden = true;
        setStep(1);
    });

    // Mostrar/ocultar cuota
    formPago.querySelectorAll('input[name="TipoP"]').forEach(function(radio) {
        radio.addEventListener('change', function() {
            if (this.value === 'Cuota') {
                bloqueCuota.hidden = false;
                nroCuota.setAttribute('required', 'required');
            } else {
                bloqueCuota.hidden = true;
                nroCuota.removeAttribute('required');
                nroCuota.value = '';
            }
        });
    });

    // Enviar formulario
    formPago.addEventListener('submit', function(e) {
        errorPaso2.hidden = true;
        
        const tipo = formPago.querySelector('input[name="TipoP"]:checked')?.value;
        if (!tipo) {
            showError('Seleccione el tipo de pago.', errorPaso2);
            e.preventDefault();
            return;
        }
        
        if (tipo === 'Cuota' && (!nroCuota.value || Number(nroCuota.value) < 1)) {
            showError('Seleccione el numero de cuota (1 a 5).', errorPaso2);
            e.preventDefault();
            return;
        }
        
        // Ajustar NroP segun tipo
        const nroPInput = document.createElement('input');
        nroPInput.type = 'hidden';
        nroPInput.name = 'NroP';
        
        if (tipo === 'Matricula') nroPInput.value = '0';
        else if (tipo === 'Defensa') nroPInput.value = '6';
        else nroPInput.value = nroCuota.value;
        
        formPago.appendChild(nroPInput);
    });

    function showError(msg, element) {
        element.textContent = msg;
        element.hidden = false;
    }
})();