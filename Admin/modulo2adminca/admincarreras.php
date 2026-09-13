<?php

$server   = "localhost";
$user     = "root";
$password = "Dilialaide123";
$db       = "oepa";

$conectar = mysqli_connect($server, $user, $password, $db) or die("Error al conectar");

if (mysqli_connect_errno()) {
    die("No conectó");
}

// consultas de carreras
$instru = "SELECT 
              c.id_carrera,
              a.id AS id_area,
              a.nombre_area,
              c.nombre_carrera,
              c.descripcion,
              c.certificaciones
           FROM carreras AS c
           JOIN areas AS a ON c.id_area = a.id
           ORDER BY c.id_carrera ASC";

$re = mysqli_query($conectar, $instru);

$carreras = [];
while ($fila = mysqli_fetch_assoc($re)) {
    $carreras[] = $fila;
}

mysqli_close($conectar);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Carreras</title>
    <link rel="stylesheet" href="../Estilos/general.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>

    <!-- ============ HEADER ============ -->
    <header class="admin-header">
        <div class="header-top">
            <h1>UNIVERSIDADES AGS</h1>
            <div class="search-bar">
                <i class="fa-solid fa-magnifying-glass search-icon"></i>
                <input type="text" id="buscador" placeholder="Busca Carrera">
            </div>
        </div>
        <nav class="header-nav">
            <a href="../modulo1adminuni/inicioadmin.php">Universidades</a>
            <a href="admincarreras.php" class="nav-active">Carreras</a>
            <a href="../login.php" id="navSalir">Cerrar sesión</a>
        </nav>
    </header>

    <!-- ============ CONTENIDO ============ -->
    <main class="admin-main">

        <div class="admin-toolbar">
            <h2>Administrar Carreras</h2>
            <button class="btn-primary" id="btnAgregar">
                <i class="fa-solid fa-plus"></i> Agregar Carrera
            </button>
        </div>

        <section class="cards-grid" id="cardsGrid">

            <?php if (empty($carreras)): ?>
                <p style="grid-column: 1/-1; text-align:center; color:#666; padding: 40px;">
                    No hay carreras registradas aún.
                </p>
            <?php else: ?>
                <?php foreach ($carreras as $c): ?>
                    <article class="uni-card" data-id="<?php echo $c['id_carrera']; ?>">

                        <div class="card-actions">
                            <!-- BOTÓN ELIMINAR -->
                            <button class="icon-btn icon-delete"
                                    title="Eliminar"
                                    data-id="<?php echo $c['id_carrera']; ?>"
                                    data-nombre="<?php echo htmlspecialchars($c['nombre_carrera']); ?>">
                                <i class="fa-solid fa-trash"></i>
                            </button>

                            <!-- BOTÓN EDITAR -->
                            <button class="icon-btn icon-edit"
                                    title="Editar"
                                    data-id="<?php echo $c['id_carrera']; ?>"
                                    data-nombre="<?php echo htmlspecialchars($c['nombre_carrera']); ?>"
                                    data-descripcion="<?php echo htmlspecialchars($c['descripcion']); ?>"
                                    data-certificaciones="<?php echo htmlspecialchars($c['certificaciones']); ?>"
                                    data-area="<?php echo $c['id_area']; ?>">
                                <i class="fa-solid fa-pen"></i>
                            </button>
                        </div>

                        <!-- Badge del área -->
                        <span class="card-badge">
                            <?php echo htmlspecialchars($c['nombre_area']); ?>
                        </span>

                        <!-- Nombre de la carrera -->
                        <p class="card-nombre">
                            <?php echo htmlspecialchars($c['nombre_carrera']); ?>
                        </p>

                        <!-- Descripción -->
                        <p class="card-desc">
                            <?php echo htmlspecialchars($c['descripcion']); ?>
                        </p>

                        <!-- Certificaciones -->
                        <?php if (!empty($c['certificaciones'])): ?>
                            <p class="card-cert">
                                <i class="fa-solid fa-certificate"></i>
                                <?php echo htmlspecialchars($c['certificaciones']); ?>
                            </p>
                        <?php endif; ?>

                    </article>
                <?php endforeach; ?>
            <?php endif; ?>

        </section>

    </main>

    <!-- ============ MODAL: AGREGAR CARRERA ============ -->
    <div class="modal-overlay" id="modalAgregar">
        <div class="modal-box">

            <div class="modal-header">
                <h3>Agregar Carrera</h3>
                <button class="modal-close" id="cerrarModal">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <form id="formAgregar" class="modal-body" method="get" action="agregoc.php">

                <div class="form-group">
                    <label>ID del Área</label>
                    <input type="text" name="ida" placeholder="Ej. 1" required>
                </div>

                <div class="form-group">
                    <label>Nombre de la Carrera</label>
                    <input type="text" name="nom" placeholder="Ej. Ingeniería en Sistemas" required>
                </div>

                <div class="form-group">
                    <label>Descripción</label>
                    <textarea name="des" rows="2" placeholder="Breve descripción" required></textarea>
                </div>

                <div class="form-group">
                    <label>Certificaciones</label>
                    <input type="text" name="cer" placeholder="Ej. ISO, CONAIC..." required>
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

    <!-- ============ MODAL: CONFIRMAR ELIMINACIÓN ============ -->
    <div class="modal-overlay" id="modalEliminar">
        <div class="modal-box" style="max-width: 420px;">

            <div class="modal-header" style="background: #e74c3c;">
                <h3>Confirmar eliminación</h3>
                <button class="modal-close" id="cerrarEliminar">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <div class="modal-body" style="text-align: center;">

                <i class="fa-solid fa-triangle-exclamation"
                   style="font-size: 3rem; color: #e74c3c; margin-bottom: 16px;"></i>

                <p style="font-size: 1rem; margin-bottom: 10px;">
                    ¿Estás seguro de eliminar esta carrera?
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

    <!-- ============ MODAL: EDITAR CARRERA ============ -->
    <div class="modal-overlay" id="modalEditar">
        <div class="modal-box">

            <div class="modal-header" style="background: #f0a500;">
                <h3>Editar Carrera</h3>
                <button class="modal-close" id="cerrarEditar">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <form id="formEditar" class="modal-body">

                <input type="hidden" id="editId" name="id_carrera">

                <div class="form-group">
                    <label>ID del Área</label>
                    <input type="text" id="editArea" name="n_ida" required>
                </div>

                <div class="form-group">
                    <label>Nombre de la Carrera</label>
                    <input type="text" id="editNombre" name="n_nom" required>
                </div>

                <div class="form-group">
                    <label>Descripción</label>
                    <textarea id="editDescripcion" name="n_des" rows="2" required></textarea>
                </div>

                <div class="form-group">
                    <label>Certificaciones</label>
                    <input type="text" id="editCertificaciones" name="n_cer" required>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn-cancel" id="cancelarEditar">Cancelar</button>
                    <button type="submit" class="btn-primary"
                            style="background:#f0a500; box-shadow: 0 3px 8px rgba(240,165,0,.4);">
                        <i class="fa-solid fa-floppy-disk"></i> Guardar cambios
                    </button>
                </div>

            </form>

        </div>
    </div>

    <script src="admincarreras.js"></script>
</body>
</html>