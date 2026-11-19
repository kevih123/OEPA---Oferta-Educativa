document.addEventListener("DOMContentLoaded", () => {

    /* --- LÓGICA DE PRECIOS DINÁMICOS --- */
    const freqAgregar = document.getElementById('freqPagoAgregar');
    const montoAgregar = document.getElementById('montoPagoAgregar');
    const freqEditar = document.getElementById('freqPagoEditar');
    const montoEditar = document.getElementById('montoPagoEditar');

    if (freqAgregar) {
        freqAgregar.addEventListener('change', function() {
            if (this.value) {
                montoAgregar.disabled = false;
                montoAgregar.style.background = '#ffffff';
                montoAgregar.style.cursor = 'text';
                montoAgregar.focus();
            } else {
                montoAgregar.value = '';
                montoAgregar.disabled = true;
                montoAgregar.style.background = '#e2e8f0';
                montoAgregar.style.cursor = 'not-allowed';
            }
        });
    }

    if (freqEditar) {
        freqEditar.addEventListener('change', function() {
            if (this.value) {
                montoEditar.disabled = false;
                montoEditar.style.background = '#ffffff';
                montoEditar.style.cursor = 'text';
            } else {
                montoEditar.value = '';
                montoEditar.disabled = true;
                montoEditar.style.background = '#e2e8f0';
                montoEditar.style.cursor = 'not-allowed';
            }
        });
    }

    /* --- RESTRICCIÓN: SOLO NÚMEROS EN TELÉFONOS Y MONTOS --- */
    function aplicarSoloNumeros(input) {
        if (!input) return;
        input.addEventListener('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '');
        });
    }
    aplicarSoloNumeros(document.getElementById('telAgregar'));
    aplicarSoloNumeros(document.getElementById('editTelefono'));
    aplicarSoloNumeros(montoAgregar);
    aplicarSoloNumeros(montoEditar);

    // Validaciones de campos de texto y combinar precio
    function configurarValidacionYEnvio(formId, errorId, freqId, montoId, combinadoId) {
        const form = document.getElementById(formId);
        if (!form) return;

        form.addEventListener("submit", function(e) {
            const errorDiv = document.getElementById(errorId);
            if (errorDiv) errorDiv.style.display = 'none';

            // 1. Validar campos de texto (evitar solo números)
            const inputsTexto = form.querySelectorAll('input[type="text"]:not([name="te"]):not([name="n_te"]):not(#montoPagoAgregar):not(#montoPagoEditar), textarea');
            for (let input of inputsTexto) {
                const valor = input.value.trim();
                if (/^\d+$/.test(valor) && valor !== '') {
                    e.preventDefault();
                    if (errorDiv) {
                        const labelText = input.previousElementSibling ? input.previousElementSibling.textContent : 'Campo';
                        errorDiv.textContent = `⚠️ El campo "${labelText}" no puede contener únicamente números. Debe ingresar texto descriptivo.`;
                        errorDiv.style.display = 'block';
                        errorDiv.scrollIntoView({ behavior: 'smooth', block: 'end' });
                    }
                    input.focus();
                    return;
                }
            }

            // 2. Combinar el precio antes de enviar
            const freq = document.getElementById(freqId).value;
            const monto = document.getElementById(montoId).value.trim();
            if (freq && monto) {
                const montoFormateado = Number(monto).toLocaleString('en-US');
                document.getElementById(combinadoId).value = `Pago ${freq.toLowerCase()} de $${montoFormateado}`;
            }
        });
    }

    /*modal para agregar*/
    const modalAgregar  = document.getElementById("modalAgregar");
    const btnAgregar    = document.getElementById("btnAgregar");
    const cerrarModal   = document.getElementById("cerrarModal");
    const cancelarModal = document.getElementById("cancelarModal");

    btnAgregar.addEventListener("click",  () => modalAgregar.classList.add("active"));
    cerrarModal.addEventListener("click", () => modalAgregar.classList.remove("active"));
    cancelarModal.addEventListener("click", () => modalAgregar.classList.remove("active"));

    modalAgregar.addEventListener("click", (e) => {
        if (e.target === modalAgregar) modalAgregar.classList.remove("active");
    });

    /* modal para eliminar */
    const modalEliminar    = document.getElementById("modalEliminar");
    const cerrarEliminar   = document.getElementById("cerrarEliminar");
    const cancelarEliminar = document.getElementById("cancelarEliminar");
    const confirmarEliminar= document.getElementById("confirmarEliminar");
    const nombreEliminar   = document.getElementById("nombreEliminar");
    const idEliminar       = document.getElementById("idEliminar");

    document.querySelectorAll(".icon-delete").forEach(btn => {
        btn.addEventListener("click", () => {
            idEliminar.value           = btn.dataset.id;
            nombreEliminar.textContent = btn.dataset.nombre;
            modalEliminar.classList.add("active");
        });
    });

    const cerrarModalEliminar = () => modalEliminar.classList.remove("active");

    cerrarEliminar.addEventListener("click", cerrarModalEliminar);
    cancelarEliminar.addEventListener("click", cerrarModalEliminar);

    modalEliminar.addEventListener("click", (e) => {
        if (e.target === modalEliminar) cerrarModalEliminar();
    });

    confirmarEliminar.addEventListener("click", async () => {
        const id = idEliminar.value;
        if (!id) return;

        try {
            const res  = await fetch("borro.php?id_uni=" + encodeURIComponent(id));
            const data = await res.json();

            if (data.success) {
                document.querySelector(`.uni-card[data-id="${id}"]`)?.remove();
                cerrarModalEliminar();
            } else {
                alert(data.mensaje);
            }
        } catch (err) {
            console.error(err);
            alert("Error al eliminar la universidad");
        }
    });

    /*  Modal par editar */
    const modalEditar    = document.getElementById("modalEditar");
    const cerrarEditar   = document.getElementById("cerrarEditar");
    const cancelarEditar = document.getElementById("cancelarEditar");
    const formEditar     = document.getElementById("formEditar");

    document.querySelectorAll(".icon-edit").forEach(btn => {
        btn.addEventListener("click", () => {
            // Función auxiliar para asignar valores de forma segura sin romper el script
            const setValor = (id, valor) => {
                const el = document.getElementById(id);
                if (el) el.value = valor !== undefined ? valor : "";
            };

            setValor("editId", btn.dataset.id);
            setValor("editNombre", btn.dataset.nombre);
            setValor("editDescri", btn.dataset.descri);
            setValor("editFrecuencia", btn.dataset.frecuencia);
            setValor("editDireccion", btn.dataset.direccion);
            setValor("editTelefono", btn.dataset.telefono);
            setValor("editSitio", btn.dataset.sitio);
            setValor("editLogo", btn.dataset.logo);

            // 1. Manejar el Periodo con Radio Buttons (S o C) de forma segura
            const periodoVal = btn.dataset.periodo;
            const radioS = document.getElementById("editPeriodoS");
            const radioC = document.getElementById("editPeriodoC");
            if (radioS) radioS.checked = (periodoVal === 'S');
            if (radioC) radioC.checked = (periodoVal === 'C');

            // 2. Desglosar el campo 'pago' en Frecuencia y Cantidad
            const pagoTexto = btn.dataset.pago || "";
            let freqEncontrada = "";
            if (pagoTexto.toLowerCase().includes("mensual")) {
                freqEncontrada = "Mensual";
            } else if (pagoTexto.toLowerCase().includes("semestral")) {
                freqEncontrada = "Semestral";
            } else if (pagoTexto.toLowerCase().includes("cuatrimestral")) {
                freqEncontrada = "Cuatrimestral";
            }
            const numerosMonto = pagoTexto.replace(/[^0-9]/g, "");

            const selectFreq = document.getElementById("freqPagoEditar");
            if (selectFreq) selectFreq.value = freqEncontrada;

            if (montoEditar) {
                if (freqEncontrada) {
                    montoEditar.disabled = false;
                    montoEditar.style.background = "#ffffff";
                    montoEditar.style.cursor = "text";
                    montoEditar.value = numerosMonto;
                } else {
                    montoEditar.value = "";
                    montoEditar.disabled = true;
                    montoEditar.style.background = "#e2e8f0";
                }
            }

            // Abrir el modal de forma garantizada
            if (modalEditar) {
                modalEditar.classList.add("active");
            }
        });
    });

    const cerrarModalEditar = () => modalEditar.classList.remove("active");

    cerrarEditar.addEventListener("click", cerrarModalEditar);
    cancelarEditar.addEventListener("click", cerrarModalEditar);

    modalEditar.addEventListener("click", (e) => {
        if (e.target === modalEditar) cerrarModalEditar();
    });

    formEditar.addEventListener("submit", async (e) => {
        e.preventDefault();

        const formData = new FormData(formEditar);

        try {
            const res = await fetch("actualizo.php", {
                method: "POST",
                body: formData
            });

            const data = await res.json();

            if (data.success) {
                const id   = formData.get("id_uni");
                const card = document.querySelector(`.uni-card[data-id="${id}"]`);

                if (card) {
                    card.querySelector(".card-nombre").textContent = formData.get("nuev_no_uni");

                    const img = card.querySelector(".card-logo img");
                    img.src = formData.get("n_logo");
                    img.alt = formData.get("nuev_no_uni");

                    const btnEdit = card.querySelector(".icon-edit");
                    btnEdit.dataset.nombre     = formData.get("nuev_no_uni");
                    btnEdit.dataset.descri     = formData.get("n_descri");
                    btnEdit.dataset.periodo    = formData.get("n_pe");
                    btnEdit.dataset.pago       = formData.get("n_pa");
                    btnEdit.dataset.frecuencia = formData.get("n_fe");
                    btnEdit.dataset.direccion  = formData.get("n_dire");
                    btnEdit.dataset.telefono   = formData.get("n_te");
                    btnEdit.dataset.sitio      = formData.get("n_sitio");
                    btnEdit.dataset.logo       = formData.get("n_logo");
                }

                cerrarModalEditar();
                alert("Universidad actualizada correctamente");
            } else {
                alert(data.mensaje);
            }

        } catch (err) {
            console.error(err);
            alert("Error al actualizar la universidad");
        }
    });

    /* Para buscar*/
    const buscador = document.getElementById("buscador");

    buscador.addEventListener("input", (e) => {
        const q = e.target.value.toLowerCase().trim();
        document.querySelectorAll(".uni-card").forEach(card => {
            const txt = card.querySelector(".card-nombre").textContent.toLowerCase();
            card.style.display = txt.includes(q) ? "" : "none";
        });
    });

    /* Administrar carrerasm aún no redirige a la página para administrar las carreras de la uni */
    document.querySelectorAll(".btn-carreras").forEach(btn => {
        btn.addEventListener("click", () => {
            const nombre = btn.closest(".uni-card").querySelector(".card-nombre").textContent;
            window.location.href = "";
        });
    });

    /* para cerrar sesión */
    

    // Activar validaciones y combinación de precios para Agregar y Editar
    configurarValidacionYEnvio('formAgregar', 'error-agregar', 'freqPagoAgregar', 'montoPagoAgregar', 'paCombinadoAgregar');
    configurarValidacionYEnvio('formEditar', 'error-editar', 'freqPagoEditar', 'montoPagoEditar', 'paCombinadoEditar');
});
window.addEventListener("pageshow", function(event) {
    if (event.persisted) {
        window.location.reload();
    }
});