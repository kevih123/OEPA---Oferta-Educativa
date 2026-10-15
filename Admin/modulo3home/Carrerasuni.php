```php
<?php
$idu = isset($_GET["idu"]) ? (int)$_GET["idu"] : 0;

require '../conn.php';

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

$instru = "SELECT
              u.universidad_id,
              u.nombre_institucion,
              c.id_carrera,
              a.id AS id_area,
              a.nombre_area,
              c.nombre_carrera,
              c.descripcion,
              c.certificaciones
           FROM carreras c
           JOIN areas a ON c.id_area = a.id
           JOIN universidades_carreras uc ON c.id_carrera = uc.id_carrera
           JOIN universidades u ON u.universidad_id = uc.id_universidad
           WHERE u.universidad_id = $idu";

if ($modalidadActual !== 'todas') {
    $idModalidad = $modalidadesFiltro[$modalidadActual];

    $instru .= " AND EXISTS (
                    SELECT 1
                    FROM carrera_modalidades AS cm
                    WHERE cm.id_carrera = c.id_carrera
                    AND cm.id_modalidad = $idModalidad
                )";
}

$instru .= " ORDER BY c.id_carrera ASC";

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
    <title>Carreras universidad</title>
    <link rel="stylesheet" href="../Estilos/general.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>

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
            <a href="Carreras.php">Carreras</a>
        </nav>
    </header>

    <?php include 'Modalmodalidades.php'; ?>

    <main class="admin-main">

        <div class="admin-toolbar">
            <?php if (!empty($carreras)): ?>
                <h2>
                    Carreras de la universidad
                    <?php echo htmlspecialchars($carreras[0]['nombre_institucion']); ?>
                </h2>
            <?php else: ?>
                <h2>Carreras de la universidad</h2>
            <?php endif; ?>
        </div>

        <section class="cards-grid" id="cardsGrid">

            <?php if (empty($carreras)): ?>
                <p style="grid-column: 1/-1; text-align:center; color:#666; padding: 40px;">
                    No hay carreras registradas para esta universidad y modalidad.
                </p>
            <?php else: ?>

                <?php foreach ($carreras as $c): ?>
                    <article class="uni-card" data-id="<?php echo $c['id_carrera']; ?>">

                        <div class="card-actions"></div>

                        <span class="card-badge">
                            <?php echo htmlspecialchars($c['nombre_area']); ?>
                        </span>

                        <p class="card-nombre">
                            <?php echo htmlspecialchars($c['nombre_carrera']); ?>
                        </p>

                        <p class="card-desc">
                            <?php echo htmlspecialchars($c['descripcion'] ?? ''); ?>
                        </p>

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

    <script src="buscador.js"></script>

</body>
</html>
```