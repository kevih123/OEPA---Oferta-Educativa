document.addEventListener("DOMContentLoaded", () => {

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
            document.getElementById("editId").value         = btn.dataset.id;
            document.getElementById("editNombre").value     = btn.dataset.nombre;
            document.getElementById("editDescri").value     = btn.dataset.descri;
            document.getElementById("editPeriodo").value    = btn.dataset.periodo;
            document.getElementById("editPago").value       = btn.dataset.pago;
            document.getElementById("editFrecuencia").value = btn.dataset.frecuencia;
            document.getElementById("editDireccion").value  = btn.dataset.direccion;
            document.getElementById("editTelefono").value   = btn.dataset.telefono;
            document.getElementById("editSitio").value      = btn.dataset.sitio;
            document.getElementById("editLogo").value       = btn.dataset.logo;

            modalEditar.classList.add("active");
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
    document.getElementById("navSalir")?.addEventListener("click", (e) => {
        e.preventDefault();
        window.location.href = "../login.php";
    });
});