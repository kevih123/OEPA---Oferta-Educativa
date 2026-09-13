document.addEventListener("DOMContentLoaded", () => {

    /* ===== MODAL AGREGAR ===== */
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

    /* ===== MODAL ELIMINAR ===== */
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
        const res  = await fetch("borroc.php?id_ca=" + encodeURIComponent(id));
        const data = await res.json();

        if (data.success) {
            document.querySelector(`.uni-card[data-id="${id}"]`)?.remove();
            cerrarModalEliminar();
        } else {
            alert(data.mensaje);
        }
    } catch (err) {
        console.error("Error completo:", err);
        alert("Error de conexión con borroc.php");
    }
});

    /* ===== MODAL EDITAR ===== */
    const modalEditar    = document.getElementById("modalEditar");
    const cerrarEditar   = document.getElementById("cerrarEditar");
    const cancelarEditar = document.getElementById("cancelarEditar");
    const formEditar     = document.getElementById("formEditar");

    document.querySelectorAll(".icon-edit").forEach(btn => {
        btn.addEventListener("click", () => {
            document.getElementById("editId").value             = btn.dataset.id;
            document.getElementById("editNombre").value         = btn.dataset.nombre;
            document.getElementById("editDescripcion").value    = btn.dataset.descripcion;
            document.getElementById("editCertificaciones").value= btn.dataset.certificaciones;
            document.getElementById("editArea").value           = btn.dataset.area;

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
        const res  = await fetch("actualizoc.php", {
            method: "POST",
            body: formData
        });

        const data = await res.json();

        if (data.success) {
            const id   = formData.get("id_carrera");
            const card = document.querySelector(`.uni-card[data-id="${id}"]`);

            if (card) {
                card.querySelector(".card-nombre").textContent = formData.get("n_nom");
                card.querySelector(".card-desc").textContent   = formData.get("n_des");

                const badge = card.querySelector(".card-badge");
                if (badge) badge.textContent = data.carrera.nombre_area;

                const cert = card.querySelector(".card-cert");
                if (cert) {
                    cert.innerHTML = `<i class="fa-solid fa-certificate"></i> ${formData.get("n_cer")}`;
                }

                // Actualizar los datos
                const btnEdit = card.querySelector(".icon-edit");
                btnEdit.dataset.nombre          = formData.get("n_nom");
                btnEdit.dataset.descripcion     = formData.get("n_des");
                btnEdit.dataset.certificaciones = formData.get("n_cer");
                btnEdit.dataset.area            = formData.get("n_ida");
            }

            cerrarModalEditar();
            alert(data.mensaje);

        } else {
            alert(data.mensaje);
        }

    } catch (err) {
        console.error("Error completo:", err);
        alert("Error de conexión con actualizoc.php");
    }
});

    /* buscador */
    const buscador = document.getElementById("buscador");

    buscador.addEventListener("input", (e) => {
        const q = e.target.value.toLowerCase().trim();
        document.querySelectorAll(".uni-card").forEach(card => {
            const txt = card.querySelector(".card-nombre").textContent.toLowerCase();
            card.style.display = txt.includes(q) ? "" : "none";
        });
    });

    /* cerrar sesion */
    document.getElementById("navSalir")?.addEventListener("click", (e) => {
        e.preventDefault();
        window.location.href = "../login.php";
    });
});