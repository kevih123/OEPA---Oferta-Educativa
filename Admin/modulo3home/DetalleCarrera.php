<?php
require '../conn.php';

// Validar parámetros requeridos por GET
if (!isset($_GET['id_carrera']) || !isset($_GET['id_universidad']) || empty($_GET['id_carrera']) || empty($_GET['id_universidad'])) {
    header('Location: CarrerasAreas.php');
    exit();
}

$id_carrera = (int)$_GET['id_carrera'];
$id_universidad = (int)$_GET['id_universidad'];

// Consulta para obtener detalles específicos de la carrera en esta universidad
$query_principal = "SELECT 
                        u.universidad_id,
                        u.nombre_institucion,
                        u.direccion,
                        u.telefono,
                        u.sitio_web,
                        u.periodo,
                        u.logo,
                        c.id_carrera,
                        c.nombre_carrera,
                        c.descripcion AS desc_general_carrera,
                        c.certificaciones,
                        uc.descripcion_general,
                        uc.duracion,
                        uc.plan_de_estudios,
                        uc.turno,
                        uc.perfil_ingreso,
                        uc.perfil_egreso,
                        uc.demanda,
                        uc.cupo
                    FROM universidades_carreras AS uc
                    JOIN universidades AS u ON uc.id_universidad = u.universidad_id
                    JOIN carreras AS c ON uc.id_carrera = c.id_carrera
                    WHERE uc.id_carrera = $id_carrera AND uc.id_universidad = $id_universidad";

$res_principal = mysqli_query($conectar, $query_principal);
$detalle = mysqli_fetch_assoc($res_principal);

// Si no existe la combinación carrera-universidad, redirigir
if (!$detalle) {
    header('Location: CarrerasAreas.php');
    exit();
}

// Consulta para obtener otras universidades que también ofertan la misma carrera
$query_otras_unis = "SELECT 
                        u.universidad_id,
                        u.nombre_institucion
                     FROM universidades_carreras AS uc
                     JOIN universidades AS u ON uc.id_universidad = u.universidad_id
                     WHERE uc.id_carrera = $id_carrera AND uc.id_universidad != $id_universidad
                     ORDER BY u.nombre_institucion ASC";

$res_otras = mysqli_query($conectar, $query_otras_unis);

$otras_universidades = [];
while ($fila = mysqli_fetch_assoc($res_otras)) {
    $otras_universidades[] = $fila;
}

mysqli_close($conectar);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($detalle['nombre_carrera']); ?> - <?php echo htmlspecialchars($detalle['nombre_institucion']); ?></title>
    <link rel="stylesheet" href="../Estilos/general.css">
    <link rel="stylesheet" href="../Estilos/DetalleCarrera.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>

    <!-- ============ HEADER ============ -->
    <header class="admin-header">
        <div class="header-top">
            <h1>OEPA</h1>
            <div class="search-bar">
                <i class="fa-solid fa-magnifying-glass search-icon"></i>
                <input type="text" id="buscador" placeholder="Busca Carrera">
            </div>
            <a class="btn-primary" href="../login.php">¿Eres admin?</a>
        </div>
        <nav class="header-nav">
            <a href="Paginaprincipal.php">Universidades</a>
            <a href="CarrerasAreas.php" class="nav-active">Carreras</a>
        </nav>
    </header>

    <!-- ============ CONTENIDO ============ -->
    <main class="admin-main">

        <!-- Breadcrumb / Ruta de navegación -->
        <div class="breadcrumb">
            <a href="Paginaprincipal.php">Universidades Públicas de Aguascalientes</a> &gt; <span><?php echo htmlspecialchars($detalle['nombre_institucion']); ?></span>
        </div>

        <div class="detalle-grid">

            <!-- 1. COLUMNA IZQUIERDA: TARJETA UNIVERSIDAD -->
            <aside class="card-universidad">
                <!-- Mostrar logo a partir del enlace de la imagen en internet -->
                <?php if (!empty($detalle['logo'])): ?>
                    <img src="<?php echo htmlspecialchars($detalle['logo']); ?>" alt="Logo de <?php echo htmlspecialchars($detalle['nombre_institucion']); ?>" class="uni-logo">
                <?php else: ?>
                    <img src="../Imagenes/default_logo.png" alt="Logo de la Universidad" class="uni-logo">
                <?php endif; ?>

                <h3><?php echo htmlspecialchars($detalle['nombre_institucion']); ?></h3>

                <div class="uni-data-list">
                    <p><strong>Dirección:</strong> <?php echo htmlspecialchars($detalle['direccion'] ?? 'No disponible'); ?></p>
                    <p><strong>Teléfono:</strong> <?php echo htmlspecialchars($detalle['telefono'] ?? 'No disponible'); ?></p>
                    <p><strong>Página:</strong> 
                        <?php if (!empty($detalle['sitio_web'])): ?>
                            <a href="<?php echo htmlspecialchars($detalle['sitio_web']); ?>" target="_blank" style="color:#0f4c81;">Visitar sitio web</a>
                        <?php else: ?>
                            No disponible
                        <?php endif; ?>
                    </p>
                    <p><strong>Modalidad S/C:</strong> <?php echo ($detalle['periodo'] === 'S') ? 'Semestral' : 'Cuatrimestral'; ?></p>
                </div>
            </aside>


            <!-- 2. COLUMNA CENTRAL: INFORMACIÓN DE LA CARRERA -->
            <section class="contenido-carrera">
                <h2><?php echo htmlspecialchars($detalle['nombre_carrera']); ?></h2>

                <!-- Descripción / Información -->
                <div class="seccion-info">
                    <p><?php echo htmlspecialchars(!empty($detalle['descripcion_general']) ? $detalle['descripcion_general'] : $detalle['desc_general_carrera']); ?></p>
                </div>

                <!-- Perfil de Ingreso -->
                <?php if (!empty($detalle['perfil_ingreso'])): ?>
                    <div class="seccion-info">
                        <h4>Perfil de Ingreso</h4>
                        <p><?php echo htmlspecialchars($detalle['perfil_ingreso']); ?></p>
                    </div>
                <?php endif; ?>

                <!-- Perfil de Egreso -->
                <?php if (!empty($detalle['perfil_egreso'])): ?>
                    <div class="seccion-info">
                        <h4>Perfil de Egreso</h4>
                        <p><?php echo htmlspecialchars($detalle['perfil_egreso']); ?></p>
                    </div>
                <?php endif; ?>

                <!-- Horarios / Turno -->
                <div class="seccion-info">
                    <h4>Horarios / Turno</h4>
                    <p><?php echo htmlspecialchars($detalle['turno'] ?? 'No especificado'); ?> (Duración: <?php echo htmlspecialchars($detalle['duracion'] ?? 'Consultar'); ?>)</p>
                </div>

                <!-- Botón Plan de estudios -->
                <?php if (!empty($detalle['plan_de_estudios'])): ?>
                    <a href="<?php echo htmlspecialchars($detalle['plan_de_estudios']); ?>" target="_blank" class="btn-plan-estudios">
                        Plan de estudios
                    </a>
                <?php else: ?>
                    <button class="btn-plan-estudios" disabled style="opacity: 0.6; cursor: not-allowed;">
                        Plan de estudios
                    </button>
                <?php endif; ?>
            </section>


            <!-- 3. COLUMNA DERECHA: OTRAS UNIVERSIDADES -->
            <aside class="panel-lateral">
                <h4>Esta carrera también está en</h4>

                <?php if (empty($otras_universidades)): ?>
                    <div class="item-otra-uni">
                        <p style="margin: 0; font-size: 0.85rem; color: #666;">Única universidad que la oferta actualmente.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($otras_universidades as $otra_uni): ?>
                        <div class="item-otra-uni">
                            <a href="DetalleCarrera.php?id_carrera=<?php echo $id_carrera; ?>&id_universidad=<?php echo $otra_uni['universidad_id']; ?>">
                                <?php echo htmlspecialchars($otra_uni['nombre_institucion']); ?>
                            </a>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>

                <a href="CarrerasAreas.php" class="btn-similares">
                    Ver carreras similares
                </a>
            </aside>

        </div>

    </main>

    <script src="buscador.js"></script>
</body>
</html>