<?php
require '../conn.php';

if (
    !isset($_GET['id_carrera'], $_GET['id_universidad']) ||
    empty($_GET['id_carrera']) ||
    empty($_GET['id_universidad'])
) {
    header('Location: CarrerasAreas.php');
    exit();
}

$id_carrera = (int)$_GET['id_carrera'];
$id_universidad = (int)$_GET['id_universidad'];

$modalidadesFiltro = [
    'presencial' => 1,
    'en_linea' => 2,
    'hibrida' => 3
];

$modalidadActual = $_GET['modalidad'] ?? 'todas';

if (
    $modalidadActual !== 'todas' &&
    !isset($modalidadesFiltro[$modalidadActual])
) {
    $modalidadActual = 'todas';
}

$idModalidad = $modalidadActual !== 'todas'
    ? $modalidadesFiltro[$modalidadActual]
    : null;

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
                    JOIN universidades AS u
                        ON uc.id_universidad = u.universidad_id
                    JOIN carreras AS c
                        ON uc.id_carrera = c.id_carrera
                    WHERE uc.id_carrera = $id_carrera
                    AND uc.id_universidad = $id_universidad";

if ($idModalidad !== null) {
    $query_principal .= " AND EXISTS (
                            SELECT 1
                            FROM carrera_modalidades AS cm
                            WHERE cm.id_carrera = c.id_carrera
                            AND cm.id_modalidad = $idModalidad
                          )";
}

$res_principal = mysqli_query($conectar, $query_principal);
$detalle = mysqli_fetch_assoc($res_principal);

if (!$detalle) {
    mysqli_close($conectar);
    header('Location: CarrerasAreas.php');
    exit();
}

$query_otras_unis = "SELECT
                        u.universidad_id,
                        u.nombre_institucion
                     FROM universidades_carreras AS uc
                     JOIN universidades AS u
                        ON uc.id_universidad = u.universidad_id
                     WHERE uc.id_carrera = $id_carrera
                     AND uc.id_universidad != $id_universidad";

if ($idModalidad !== null) {
    $query_otras_unis .= " AND EXISTS (
                            SELECT 1
                            FROM carrera_modalidades AS cm
                            WHERE cm.id_carrera = uc.id_carrera
                            AND cm.id_modalidad = $idModalidad
                          )";
}

$query_otras_unis .= " ORDER BY u.nombre_institucion ASC";

$res_otras = mysqli_query($conectar, $query_otras_unis);

$otras_universidades = [];

while ($fila = mysqli_fetch_assoc($res_otras)) {
    $otras_universidades[] = $fila;
}

mysqli_close($conectar);

$modalidadUrl = '&modalidad=' . urlencode($modalidadActual);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($detalle['nombre_carrera']); ?> - <?php echo htmlspecialchars($detalle['nombre_institucion']); ?></title>
    <link rel="stylesheet" href="../Estilos_a/general.css">
    <link rel="stylesheet" href="../Estilos_a/DetalleCarrera.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>

    <?php include 'NavBar/NavBarCarreras.php'; ?>

    <main class="admin-main">

    <div style="margin-top: 130px; padding: 20px;">
    <!-- Envolvemos todo dentro de un div solo para dar espacio y no quede detras del NavBar -->

        <div class="breadcrumb">
            <a href="Paginaprincipal.php">Universidades Públicas de Aguascalientes</a>
            &gt;
            <span><?php echo htmlspecialchars($detalle['nombre_institucion']); ?></span>
        </div>

        <div class="detalle-grid">

            <aside class="card-universidad">
                <?php if (!empty($detalle['logo'])): ?>
                    <img src="<?php echo htmlspecialchars($detalle['logo']); ?>" alt="Logo de <?php echo htmlspecialchars($detalle['nombre_institucion']); ?>" class="uni-logo">
                <?php else: ?>
                    <img src="../Imagenes/default_logo.png" alt="Logo de la Universidad" class="uni-logo">
                <?php endif; ?>

                <h3><?php echo htmlspecialchars($detalle['nombre_institucion']); ?></h3>

                <div class="uni-data-list">
                    <p><strong>Dirección:</strong> <?php echo htmlspecialchars($detalle['direccion'] ?? 'No disponible'); ?></p>

                    <p><strong>Teléfono:</strong> <?php echo htmlspecialchars($detalle['telefono'] ?? 'No disponible'); ?></p>

                    <?php 
                    $urlWeb = trim($detalle['sitio_web'] ?? '');
                    if (!empty($urlWeb) && !preg_match("~^(http|https)://~i", $urlWeb)) {
                        $urlWeb = "https://" . $urlWeb;
                    }
                    ?>

                    <p><strong>Página:</strong>
                        <?php if (!empty($urlWeb)): ?>
                            <a href="<?php echo htmlspecialchars($urlWeb); ?>" target="_blank" rel="noopener noreferrer" style="color:#0f4c81;">Visitar sitio web</a>
                        <?php else: ?>
                            No disponible
                        <?php endif; ?>
                    </p>
                    <p><strong>Modalidad S/C:</strong> <?php echo ($detalle['periodo'] === 'S') ? 'Semestral' : 'Cuatrimestral'; ?></p>
                </div>
            </aside>

            <section class="contenido-carrera">
                <h2><?php echo htmlspecialchars($detalle['nombre_carrera']); ?></h2>

                <div class="seccion-info">
                    <p><?php echo htmlspecialchars(!empty($detalle['descripcion_general']) ? $detalle['descripcion_general'] : ($detalle['desc_general_carrera'] ?? '')); ?></p>
                </div>

                <?php if (!empty($detalle['perfil_ingreso'])): ?>
                    <div class="seccion-info">
                        <h4>Perfil de Ingreso</h4>
                        <p><?php echo htmlspecialchars($detalle['perfil_ingreso']); ?></p>
                    </div>
                <?php endif; ?>

                <?php if (!empty($detalle['perfil_egreso'])): ?>
                    <div class="seccion-info">
                        <h4>Perfil de Egreso</h4>
                        <p><?php echo htmlspecialchars($detalle['perfil_egreso']); ?></p>
                    </div>
                <?php endif; ?>

                <div class="seccion-info">
                    <h4>Horarios / Turno</h4>
                    <p><?php echo htmlspecialchars($detalle['turno'] ?? 'No especificado'); ?> (Duración: <?php echo htmlspecialchars($detalle['duracion'] ?? 'Consultar'); ?>)</p>
                </div>

                <?php
                $urlPlan = trim($detalle['plan_de_estudios'] ?? '');
                if (!empty($urlPlan) && !preg_match("~^(http|https)://~i", $urlPlan)) {
                    $urlPlan = "https://" . $urlPlan;
                }
                ?>

                <?php if (!empty($urlPlan)): ?>
                    <a href="<?php echo htmlspecialchars($urlPlan); ?>" target="_blank" rel="noopener noreferrer" class="btn-plan-estudios">
                        Plan de estudios
                    </a>
                <?php else: ?>
                    <button class="btn-plan-estudios" disabled style="opacity: 0.6; cursor: not-allowed;">
                        Plan de estudios
                    </button>
                <?php endif; ?>
            </section>

            <aside class="panel-lateral">
                <h4>Esta carrera también está en</h4>

                <?php if (empty($otras_universidades)): ?>
                    <div class="item-otra-uni">
                        <p style="margin: 0; font-size: 0.85rem; color: #666;">No hay otras universidades registradas para esta carrera y modalidad.</p>
                    </div>
                <?php else: ?>

                    <?php foreach ($otras_universidades as $otra_uni): ?>
                        <div class="item-otra-uni">
                            <a href="DetalleCarrera.php?id_carrera=<?php echo $id_carrera; ?>&id_universidad=<?php echo $otra_uni['universidad_id']; ?><?php echo $modalidadUrl; ?>">
                                <?php echo htmlspecialchars($otra_uni['nombre_institucion']); ?>
                            </a>
                        </div>
                    <?php endforeach; ?>

                <?php endif; ?>

                <a href="CarrerasAreas.php?modalidad=<?php echo urlencode($modalidadActual); ?>" class="btn-similares">
                    Ver carreras similares
                </a>
            </aside>

        </div>

    </div>

    </main>

    <script src="busqueda_predictiva.js"></script>

</body>
</html>