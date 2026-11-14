<?php
include '../validar_sesion.php';
include '../conn.php';

// 1. Validar que se reciba el ID de la universidad
if (!isset($_GET['id_universidad']) || empty($_GET['id_universidad'])) {
    header('Location: inicioadmin.php');
    exit();
}

$id_universidad = (int)$_GET['id_universidad'];

// 2. Obtener datos de la universidad para mostrar el nombre en el título
$sql_uni = "SELECT nombre_institucion FROM universidades WHERE universidad_id = $id_universidad";
$res_uni = mysqli_query($conectar, $sql_uni);
$uni_info = mysqli_fetch_assoc($res_uni);

if (!$uni_info) {
    header('Location: inicioadmin.php');
    exit();
}

$nombre_universidad = $uni_info['nombre_institucion'];

// 3. Obtener las carreras asignadas a esta universidad
$sql_carreras_uni = "SELECT 
                        uc.id_universidades_carreras,
                        uc.id_carrera,
                        c.nombre_carrera,
                        a.nombre_area,
                        uc.descripcion_general,
                        uc.duracion,
                        uc.plan_de_estudios,
                        uc.turno,
                        uc.perfil_ingreso,
                        uc.perfil_egreso,
                        uc.demanda,
                        uc.cupo
                     FROM universidades_carreras AS uc
                     JOIN carreras AS c ON uc.id_carrera = c.id_carrera
                     JOIN areas AS a ON c.id_area = a.id
                     WHERE uc.id_universidad = $id_universidad
                     ORDER BY c.nombre_carrera ASC";
$res_carreras_uni = mysqli_query($conectar, $sql_carreras_uni);

$carreras_asignadas = [];
while ($fila = mysqli_fetch_assoc($res_carreras_uni)) {
    $carreras_asignadas[] = $fila;
}

// 4. Obtener TODAS las carreras generales (para el select del modal de agregar)
$sql_todas = "SELECT id_carrera, nombre_carrera FROM carreras ORDER BY nombre_carrera ASC";
$res_todas = mysqli_query($conectar, $sql_todas);
$catalogo_carreras = [];
while ($fila = mysqli_fetch_assoc($res_todas)) {
    $catalogo_carreras[] = $fila;
}

mysqli_close($conectar);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrar Carreras - <?php echo htmlspecialchars($nombre_universidad); ?></title>
    <link rel="stylesheet" href="../Estilos_a/general.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        .table-container {
            background: #fff;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            margin-top: 20px;
            overflow-x: auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }
        th, td {
            padding: 12px 15px;
            border-bottom: 1px solid #ddd;
            font-size: 0.95rem;
        }
        th {
            background-color: #f8fafc;
            color: #1e293b;
        }
        tr:hover {
            background-color: #f1f5f9;
        }
        .breadcrumb {
            font-size: 1rem;
            color: #64748b;
            margin-bottom: 15px;
        }
        .breadcrumb a {
            color: #0f4c81;
            text-decoration: none;
        }
    </style>
</head>
<body>

    <!-- HEADER -->
    <header class="admin-header">
        <div class="header-top">
            <h1>OEPA</h1>
        </div>
        <nav class="header-nav">
            <a href="inicioadmin.php" class="nav-active">Universidades</a>
            <a href="../login.php">Cerrar sesión</a>
        </nav>
    </header>

    <!-- CONTENIDO -->
    <main class="admin-main">

        <div class="breadcrumb">
            <a href="inicioadmin.php">Universidades</a> &gt; <span><?php echo htmlspecialchars($nombre_universidad); ?></span>
        </div>

        <div class="admin-toolbar">
            <h2>Carreras de: <?php echo htmlspecialchars($nombre_universidad); ?></h2>
            <button class="btn-primary" id="btnAgregarUniCarrera">
                <i class="fa-solid fa-plus"></i> Ofertar Nueva Carrera
            </button>
        </div>

        <div class="table-container">
            <?php if (empty($carreras_asignadas)): ?>
                <p style="text-align:center; color:#666; padding: 30px;">
                    Esta universidad aún no tiene carreras registradas en su oferta educativa.
                </p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Carrera</th>
                            <th>Área</th>
                            <th>Duración</th>
                            <th>Turno</th>
                            <th>Cupo</th>
                            <th style="text-align: center;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($carreras_asignadas as $c): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($c['nombre_carrera']); ?></strong></td>
                                <td><?php echo htmlspecialchars($c['nombre_area']); ?></td>
                                <td><?php echo htmlspecialchars($c['duracion']); ?></td>
                                <td><?php echo htmlspecialchars($c['turno']); ?></td>
                                <td><?php echo htmlspecialchars($c['cupo']); ?></td>
                                <td style="text-align: center; display: flex; gap: 8px; justify-content: center;">
                                    <!-- Botón de editar -->
                                    <button type="button" class="icon-btn icon-edit btn-editar" 
                                        title="Editar"
                                        data-id="<?php echo $c['id_universidades_carreras']; ?>"
                                        data-carrera="<?php echo htmlspecialchars($c['nombre_carrera']); ?>"
                                        data-desc="<?php echo htmlspecialchars($c['descripcion_general'] ?? ''); ?>"
                                        data-duracion="<?php echo htmlspecialchars($c['duracion']); ?>"
                                        data-turno="<?php echo htmlspecialchars($c['turno']); ?>"
                                        data-plan="<?php echo htmlspecialchars($c['plan_de_estudios']); ?>"
                                        data-ingreso="<?php echo htmlspecialchars($c['perfil_ingreso'] ?? ''); ?>"
                                        data-egreso="<?php echo htmlspecialchars($c['perfil_egreso'] ?? ''); ?>"
                                        data-demanda="<?php echo htmlspecialchars($c['demanda']); ?>"
                                        data-cupo="<?php echo htmlspecialchars($c['cupo']); ?>"
                                        style="background: #fef3c7; color: #d97706; padding: 8px 12px; border-radius: 6px; border: none; cursor: pointer;">
                                        <i class="fa-solid fa-pen"></i>
                                    </button>
                                    <!-- Botón de eliminar -->
                                    <a href="eliminar_uni_carrera.php?id=<?php echo $c['id_universidades_carreras']; ?>&id_uni=<?php echo $id_universidad; ?>" 
                                        class="icon-btn icon-delete" 
                                        title="Eliminar" 
                                        onclick="return confirm('¿Estás seguro de quitar esta carrera de la universidad?');"
                                        style="background: #fee2e2; color: #dc2626; padding: 8px 12px; border-radius: 6px; text-decoration: none;">
                                        <i class="fa-solid fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

    </main>

    <!-- MODAL AGREGAR CARRERA A UNIVERSIDAD -->
    <div class="modal-overlay" id="modalAgregarUniCarrera" style="display: none;">
        <div class="modal-box" style="max-width: 850px; width: 95%;">
            <div class="modal-header">
                <h3>Vincular Carrera a la Universidad</h3>
                <button class="modal-close" id="cerrarModal"><i class="fa-solid fa-xmark"></i></button>
            </div>

            <form action="guardar_uni_carrera.php" method="POST" class="modal-body">
                <input type="hidden" name="id_universidad" value="<?php echo $id_universidad; ?>">

                <!-- Contenedor de error -->
                <div id="error-agregar" style="display: none; grid-column: 1 / -1; background-color: #fee2e2; color: #dc2626; padding: 10px; border-radius: 6px; margin-bottom: 10px; font-size: 0.9rem; font-weight: 500;"></div>

                <!-- Grid de 2 columnas para optimizar espacio vertical -->
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; grid-column: 1 / -1;">
                    <div class="form-group" style="grid-column: 1 / -1;">
                        <label>Seleccionar Carrera</label>
                        <select name="id_carrera" required style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid #cbd5e1;">
                            <option value="">-- Elige una carrera del catálogo --</option>
                            <?php foreach ($catalogo_carreras as $cat): ?>
                                <option value="<?php echo $cat['id_carrera']; ?>"><?php echo htmlspecialchars($cat['nombre_carrera']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group" style="grid-column: 1 / -1;">
                        <label>Descripción General / Enfoque específico</label>
                        <textarea name="descripcion_general" rows="2" placeholder="Detalles particulares de esta carrera en la institución" required></textarea>
                    </div>

                    <div class="form-group">
                        <label>Duración</label>
                        <input type="text" name="duracion" placeholder="Ej. 9 semestres" required>
                    </div>

                    <div class="form-group">
                        <label>Turno</label>
                        <select name="turno" required style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid #cbd5e1;">
                            <option value="Matutino">Matutino</option>
                            <option value="Vespertino">Vespertino</option>
                            <option value="Ambos">Ambos</option>
                        </select>
                    </div>

                    <div class="form-group" style="grid-column: 1 / -1;">
                        <label>Plan de Estudios (URL o enlace)</label>
                        <input type="url" name="plan_de_estudios" placeholder="https://ejemplo.com/plan.pdf" required>
                    </div>

                    <div class="form-group">
                        <label>Perfil de Ingreso</label>
                        <textarea name="perfil_ingreso" rows="2" required></textarea>
                    </div>

                    <div class="form-group">
                        <label>Perfil de Egreso</label>
                        <textarea name="perfil_egreso" rows="2" required></textarea>
                    </div>

                    <div class="form-group">
                        <label>Demanda</label>
                        <input type="text" name="demanda" placeholder="Ej. Alta / Media" required>
                    </div>

                    <div class="form-group">
                        <label>Cupo</label>
                        <input type="text" name="cupo" placeholder="Ej. 40 alumnos" required>
                    </div>
                </div>

                <div class="modal-footer" style="grid-column: 1 / -1; margin-top: 15px;">
                    <button type="button" class="btn-cancel" id="cancelarModal">Cancelar</button>
                    <button type="submit" class="btn-primary"><i class="fa-solid fa-floppy-disk"></i> Vincular Carrera</button>
                </div>
            </form>
        </div>
    </div>

    <!-- SCRIPT PARA CONTROLAR EL MODAL -->
    <script>
        const modal = document.getElementById('modalAgregarUniCarrera');
        const btnAbrir = document.getElementById('btnAgregarUniCarrera');
        const btnCerrar = document.getElementById('cerrarModal');
        const btnCancelar = document.getElementById('cancelarModal');

        btnAbrir.addEventListener('click', () => { modal.style.display = 'flex'; });
        btnCerrar.addEventListener('click', () => { modal.style.display = 'none'; });
        btnCancelar.addEventListener('click', () => { modal.style.display = 'none'; });
        window.addEventListener('click', (e) => { if (e.target === modal) modal.style.display = 'none'; });

        document.addEventListener('DOMContentLoaded', () => {
            const modalEditar = document.getElementById('modalEditarUniCarrera');
            const cerrarEditar = document.getElementById('cerrarModalEditar');
            const cancelarEditar = document.getElementById('cancelarModalEditar');
            const botonesEditar = document.querySelectorAll('.btn-editar');

            // Depuración: Verifica en la consola (F12) si detecta los botones
            console.log("Botones de editar encontrados:", botonesEditar.length);

            botonesEditar.forEach(btn => {
                btn.addEventListener('click', () => {
                    // Extraer los atributos data-*
                    document.getElementById('edit_id_relacion').value = btn.getAttribute('data-id');
                    document.getElementById('edit_nombre_carrera').value = btn.getAttribute('data-carrera');
                    document.getElementById('edit_descripcion_general').value = btn.getAttribute('data-desc');
                    document.getElementById('edit_duracion').value = btn.getAttribute('data-duracion');
                    document.getElementById('edit_turno').value = btn.getAttribute('data-turno');
                    document.getElementById('edit_plan_de_estudios').value = btn.getAttribute('data-plan');
                    document.getElementById('edit_perfil_ingreso').value = btn.getAttribute('data-ingreso');
                    document.getElementById('edit_perfil_egreso').value = btn.getAttribute('data-egreso');
                    document.getElementById('edit_demanda').value = btn.getAttribute('data-demanda');
                    document.getElementById('edit_cupo').value = btn.getAttribute('data-cupo');

                    // Mostrar el modal
                    if (modalEditar) {
                        modalEditar.style.display = 'flex';
                    }
                });
            });

            if (cerrarEditar) {
                cerrarEditar.addEventListener('click', () => { modalEditar.style.display = 'none'; });
            }
            if (cancelarEditar) {
                cancelarEditar.addEventListener('click', () => { modalEditar.style.display = 'none'; });
            }
            window.addEventListener('click', (e) => { 
                if (e.target === modalEditar) {
                    modalEditar.style.display = 'none'; 
                }
            });
        });

        document.addEventListener('DOMContentLoaded', () => {
            // Función auxiliar para validar URLs mediante JS
            function esUrlValida(url) {
                try {
                    const parsed = new URL(url);
                    return parsed.protocol === "http:" || parsed.protocol === "https:";
                } catch (_) {
                    return false;
                }
            }

            // 1. Validación para el formulario de AGREGAR
            const formAgregar = document.querySelector('#modalAgregarUniCarrera form');
            if (formAgregar) {
                formAgregar.addEventListener('submit', function(e) {
                    const inputPlan = formAgregar.querySelector('input[name="plan_de_estudios"]');
                    const errorDiv = document.getElementById('error-agregar');

                    if (!esUrlValida(inputPlan.value.trim())) {
                        e.preventDefault(); // Detiene el envío del formulario
                        errorDiv.textContent = "⚠️ El campo 'Plan de Estudios' debe ser una URL válida (ej. https://ejemplo.com).";
                        errorDiv.style.display = 'block';
                        inputPlan.focus();
                    }
                });
            }

            // 2. Validación para el formulario de EDITAR
            const formEditar = document.querySelector('#modalEditarUniCarrera form');
            if (formEditar) {
                formEditar.addEventListener('submit', function(e) {
                    const inputPlan = document.getElementById('edit_plan_de_estudios');
                    const errorDiv = document.getElementById('error-editar');

                    if (!esUrlValida(inputPlan.value.trim())) {
                        e.preventDefault(); // Detiene el envío del formulario
                        errorDiv.textContent = "⚠️ El campo 'Plan de Estudios' debe ser una URL válida (ej. https://ejemplo.com).";
                        errorDiv.style.display = 'block';
                        inputPlan.focus();
                    }
                });
            }
        });

        
        document.addEventListener('DOMContentLoaded', () => {
            function esUrlValida(url) {
                try {
                    const parsed = new URL(url);
                    return parsed.protocol === "http:" || parsed.protocol === "https:";
                } catch (_) {
                    return false;
                }
            }

            // Validación formulario Agregar
            const formAgregar = document.querySelector('#modalAgregarUniCarrera form');
            if (formAgregar) {
                formAgregar.addEventListener('submit', function(e) {
                    const inputPlan = formAgregar.querySelector('input[name="plan_de_estudios"]');
                    const errorDiv = document.getElementById('error-agregar');

                    if (!esUrlValida(inputPlan.value.trim())) {
                        e.preventDefault();
                        errorDiv.textContent = "⚠️ El campo 'Plan de Estudios' debe ser una URL válida (ej. https://ejemplo.com).";
                        errorDiv.style.display = 'block';
                        errorDiv.scrollIntoView({ behavior: 'smooth', block: 'nearest' }); // Desplaza la vista al error
                        inputPlan.focus();
                    }
                });
            }

            // Validación formulario Editar
            const formEditar = document.querySelector('#modalEditarUniCarrera form');
            if (formEditar) {
                formEditar.addEventListener('submit', function(e) {
                    const inputPlan = document.getElementById('edit_plan_de_estudios');
                    const errorDiv = document.getElementById('error-editar');

                    if (!esUrlValida(inputPlan.value.trim())) {
                        e.preventDefault();
                        errorDiv.textContent = "⚠️ El campo 'Plan de Estudios' debe ser una URL válida (ej. https://ejemplo.com).";
                        errorDiv.style.display = 'block';
                        errorDiv.scrollIntoView({ behavior: 'smooth', block: 'nearest' }); // Desplaza la vista al error
                        inputPlan.focus();
                    }
                });
            }
        });
    </script>

    <!-- MODAL EDITAR CARRERA DE UNIVERSIDAD -->
    <div class="modal-overlay" id="modalEditarUniCarrera" style="display: none;">
        <div class="modal-box" style="max-width: 850px; width: 95%;">
            <div class="modal-header" style="background: #f0a500; color: #fff;">
                <h3>Editar Información de Carrera</h3>
                <button class="modal-close" id="cerrarModalEditar"><i class="fa-solid fa-xmark"></i></button>
            </div>

            <form action="actualizar_uni_carrera.php" method="POST" class="modal-body">
                <input type="hidden" name="id_universidades_carreras" id="edit_id_relacion">
                <input type="hidden" name="id_universidad" value="<?php echo $id_universidad; ?>">

                <!-- Contenedor de error -->
                <div id="error-editar" style="display: none; grid-column: 1 / -1; background-color: #fee2e2; color: #dc2626; padding: 10px; border-radius: 6px; margin-bottom: 10px; font-size: 0.9rem; font-weight: 500;"></div>

                <!-- Grid de 2 columnas -->
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; grid-column: 1 / -1;">
                    <div class="form-group" style="grid-column: 1 / -1;">
                        <label>Carrera</label>
                        <input type="text" id="edit_nombre_carrera" disabled style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid #cbd5e1; background: #f1f5f9; color: #555;">
                    </div>

                    <div class="form-group" style="grid-column: 1 / -1;">
                        <label>Descripción General / Enfoque específico</label>
                        <textarea name="descripcion_general" id="edit_descripcion_general" rows="2" required></textarea>
                    </div>

                    <div class="form-group">
                        <label>Duración</label>
                        <input type="text" name="duracion" id="edit_duracion" required>
                    </div>

                    <div class="form-group">
                        <label>Turno</label>
                        <select name="turno" id="edit_turno" required style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid #cbd5e1;">
                            <option value="Matutino">Matutino</option>
                            <option value="Vespertino">Vespertino</option>
                            <option value="Ambos">Ambos</option>
                        </select>
                    </div>

                    <div class="form-group" style="grid-column: 1 / -1;">
                        <label>Plan de Estudios (URL o enlace)</label>
                        <input type="url" name="plan_de_estudios" id="edit_plan_de_estudios" placeholder="https://ejemplo.com/plan.pdf" required>
                    </div>

                    <div class="form-group">
                        <label>Perfil de Ingreso</label>
                        <textarea name="perfil_ingreso" id="edit_perfil_ingreso" rows="2" required></textarea>
                    </div>

                    <div class="form-group">
                        <label>Perfil de Egreso</label>
                        <textarea name="perfil_egreso" id="edit_perfil_egreso" rows="2" required></textarea>
                    </div>

                    <div class="form-group">
                        <label>Demanda</label>
                        <input type="text" name="demanda" id="edit_demanda" required>
                    </div>

                    <div class="form-group">
                        <label>Cupo</label>
                        <input type="text" name="cupo" id="edit_cupo" required>
                    </div>
                </div>

                <div class="modal-footer" style="grid-column: 1 / -1; margin-top: 15px;">
                    <button type="button" class="btn-cancel" id="cancelarModalEditar">Cancelar</button>
                    <button type="submit" class="btn-primary" style="background:#f0a500; border:none;"><i class="fa-solid fa-floppy-disk"></i> Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>