<?php
include '../validar_sesion.php';
// conectar
include '../conn.php';

// consulta para universidades
$sql = "SELECT * FROM universidades ORDER BY nombre_institucion ASC";
$res = mysqli_query($conectar, $sql);

$universidades = [];
while ($fila = mysqli_fetch_assoc($res)) {
    $universidades[] = $fila;
}

mysqli_close($conectar);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrador Universidad</title>
    <link rel="stylesheet" href="../Estilos/general.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>

    <!-- header para navegar  -->
    <header class="admin-header">
        <div class="header-top">
            <h1>OEPA</h1>
            <div class="search-bar">
                <i class="fa-solid fa-magnifying-glass search-icon"></i>
                <input type="text" id="buscador" placeholder="Busca Universidad">
            </div>
        </div>
        <nav class="header-nav">
            <a href="inicioadmin.php" class="nav-active">Universidades</a>
            <a href="../modulo2adminca/admincarreras.php" id="navCarreras">Carreras</a>
            <a href="../login.php" id="navSalir">Cerrar sesión</a>
        </nav>
    </header>

    <!--Contenido -->
    <main class="admin-main">

        <div class="admin-toolbar">
            <h2>Administrar Universidades</h2>
            <button class="btn-primary" id="btnAgregar">
                <i class="fa-solid fa-plus"></i> Agregar Universidad
            </button>
        </div>

        <section class="cards-grid" id="cardsGrid">

            <?php if (empty($universidades)): ?>
                <p style="grid-column: 1/-1; text-align:center; color:#666; padding: 40px;">
                    No hay universidades registradas aún.
                </p>
            <?php else: ?>
                <?php foreach ($universidades as $u): ?>
                    <article class="uni-card" data-id="<?php echo $u['universidad_id']; ?>">
                        <!--Para borrar la uni-->
                        <div class="card-actions">
                            <button class="icon-btn icon-delete"
                                    title="Eliminar"
                                    data-id="<?php echo $u['universidad_id']; ?>"
                                    data-nombre="<?php echo htmlspecialchars($u['nombre_institucion']); ?>">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        <!--Para editar la uni-->
                            <button class="icon-btn icon-edit"
                                    title="Editar"
                                    data-id="<?php echo $u['universidad_id']; ?>"
                                    data-nombre="<?php echo htmlspecialchars($u['nombre_institucion']); ?>"
                                    data-descri="<?php echo htmlspecialchars($u['descripción']); ?>"
                                    data-periodo="<?php echo htmlspecialchars($u['periodo']); ?>"
                                    data-pago="<?php echo htmlspecialchars($u['pago']); ?>"
                                    data-frecuencia="<?php echo htmlspecialchars($u['frecuencia_convocatoria']); ?>"
                                    data-direccion="<?php echo htmlspecialchars($u['dirección']); ?>"
                                    data-telefono="<?php echo htmlspecialchars($u['teléfono']); ?>"
                                    data-sitio="<?php echo htmlspecialchars($u['sitio_web']); ?>"
                                    data-logo="<?php echo htmlspecialchars($u['logo']); ?>">
                                <i class="fa-solid fa-pen"></i>
                            </button>
                        </div>

                        <!--Para presentar la uni en el card-->
                        <div class="card-logo">
                            <img src="<?php echo htmlspecialchars($u['logo']); ?>"
                                alt="<?php echo htmlspecialchars($u['nombre_institucion']); ?>">
                        </div>
                        <p class="card-nombre">
                            <?php echo htmlspecialchars($u['nombre_institucion']); ?>
                        </p>

                        <button class="btn-outline btn-carreras">Administrar Carreras</button>

                    </article>
                <?php endforeach; ?>
            <?php endif; ?>

        </section>

    </main>

    <!-- Modal de agregar uni -->
    <div class="modal-overlay" id="modalAgregar">
        <div class="modal-box">

            <div class="modal-header">
                <h3>Agregar Universidad</h3>
                <button class="modal-close" id="cerrarModal">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <form id="formAgregar" class="modal-body" method="get" action="agrego.php">

                <div class="form-group">
                    <label>Nombre de la Universidad</label>
                    <input type="text" name="nombre" placeholder="Ej. Universidad Autónoma de Aguascalientes" required>
                </div>

                <div class="form-group">
                    <label>Descripción</label>
                    <textarea name="descri" rows="2" placeholder="Breve descripción" required></textarea>
                </div>

                <div class="form-group">
                    <label>Periodo</label>
                    <input type="text" name="pe" placeholder="Ej. Semestral" required>
                </div>

                <div class="form-group">
                    <label>Precio</label>
                    <input type="text" name="pa" placeholder="Ej. 8500" required>
                </div>

                <div class="form-group">
                    <label>Frecuencia de convocatoria</label>
                    <input type="text" name="fe" placeholder="Ej. Anual" required>
                </div>

                <div class="form-group">
                    <label>Dirección</label>
                    <input type="text" name="dire" placeholder="Ej. Av. Universidad 123" required>
                </div>

                <div class="form-group">
                    <label>Teléfono</label>
                    <input type="text" name="te" placeholder="Ej. 4491234567" required>
                </div>

                <div class="form-group">
                    <label>Sitio web</label>
                    <input type="text" name="sitio" placeholder="https://..." required>
                </div>

                <div class="form-group">
                    <label>URL del Logo</label>
                    <input type="text" name="logo" placeholder="../Imagenes/logo.jpeg" required>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn-cancel" id="cancelarModal">Cancelar</button>
                    <button type="submit" class="btn-primary">
                        <i class="fa-solid fa-floppy-disk"></i> Guardar
                    </button>
                </div>

            </form>

        </div>
    </div>
    <!-- Modal para dar de baja-->
    <div class="modal-overlay" id="modalEliminar">
        <div class="modal-box" style="max-width: 420px;">

            <div class="modal-header" style="background: #e74c3c;">
                <h3>Confirmar baja</h3>
                <button class="modal-close" id="cerrarEliminar">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <div class="modal-body" style="text-align: center;">

                <i class="fa-solid fa-triangle-exclamation"
                style="font-size: 3rem; color: #e74c3c; margin-bottom: 16px;"></i>

                <p style="font-size: 1rem; margin-bottom: 10px;">
                    ¿Estás seguro de dar de baja esta universidad?
                </p>

                <p id="nombreEliminar"
                style="font-weight: 700; font-size: 1.05rem; color: #333; margin-bottom: 20px;">
                    —
                </p>

                <input type="hidden" id="idEliminar">

                <div class="modal-footer" style="justify-content: center;">
                    <button type="button" class="btn-cancel" id="cancelarEliminar">
                        Cancelar
                    </button>
                    <button type="button" class="btn-primary"
                            id="confirmarEliminar"
                            style="background: #e74c3c; box-shadow: 0 3px 8px rgba(231,76,60,.4);">
                        <i class="fa-solid fa-trash"></i> Sí, eliminar
                    </button>
                </div>

            </div>

        </div>
    </div>

    <!-- Modal de editar universidad-->
<div class="modal-overlay" id="modalEditar">
    <div class="modal-box">

        <div class="modal-header" style="background: #f0a500;">
            <h3>Editar Universidad</h3>
            <button class="modal-close" id="cerrarEditar">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <form id="formEditar" class="modal-body">

            <input type="hidden" id="editId" name="id_uni">

            <div class="form-group">
                <label>Nombre de la Universidad</label>
                <input type="text" id="editNombre" name="nuev_no_uni" required>
            </div>

            <div class="form-group">
                <label>Descripción</label>
                <textarea id="editDescri" name="n_descri" rows="2" required></textarea>
            </div>

            <div class="form-group">
                <label>Periodo</label>
                <input type="text" id="editPeriodo" name="n_pe" required>
            </div>

            <div class="form-group">
                <label>Precio</label>
                <input type="text" id="editPago" name="n_pa" required>
            </div>

            <div class="form-group">
                <label>Frecuencia de convocatoria</label>
                <input type="text" id="editFrecuencia" name="n_fe" required>
            </div>

            <div class="form-group">
                <label>Dirección</label>
                <input type="text" id="editDireccion" name="n_dire" required>
            </div>

            <div class="form-group">
                <label>Teléfono</label>
                <input type="text" id="editTelefono" name="n_te" required>
            </div>

            <div class="form-group">
                <label>Sitio web</label>
                <input type="text" id="editSitio" name="n_sitio" required>
            </div>

            <div class="form-group">
                <label>URL del Logo</label>
                <input type="text" id="editLogo" name="n_logo" required>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn-cancel" id="cancelarEditar">Cancelar</button>
                <button type="submit" class="btn-primary" style="background:#f0a500; box-shadow: 0 3px 8px rgba(240,165,0,.4);">
                    <i class="fa-solid fa-floppy-disk"></i> Guardar cambios
                </button>
            </div>

        </form>

    </div>
</div>

    <script src="inicio_admin.js"></script>
</body>
</html>