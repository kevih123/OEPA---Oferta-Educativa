```php
<?php
require '../conn2.php';

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

$sql = "SELECT DISTINCT
            u.*
        FROM universidades AS u";

if ($modalidadActual !== 'todas') {
    $idModalidad = $modalidadesFiltro[$modalidadActual];

    $sql .= " WHERE EXISTS (
                  SELECT 1
                  FROM universidades_carreras AS uc
                  INNER JOIN carrera_modalidades AS cm
                      ON cm.id_carrera = uc.id_carrera
                  WHERE uc.id_universidad = u.universidad_id
                  AND cm.id_modalidad = $idModalidad
              )";
}

$sql .= " ORDER BY u.nombre_institucion ASC";

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

    <header class="admin-header">
        <div class="header-top">
            <h1>OEPA</h1>

            <div class="search-bar">
                <i class="fa-solid fa-magnifying-glass search-icon"></i>
                <input type="text" id="buscador" placeholder="Busca Universidad">
            </div>

            <a class="btn-primary" href="../login.php">¿Eres admin?</a>
        </div>

        <nav class="header-nav">
            <a href="Paginaprincipal.php" class="nav-active">Universidades</a>
            <a href="CarrerasAreas.php?modalidad=<?php echo urlencode($modalidadActual); ?>">Carreras</a>
        </nav>
    </header>

    <?php include 'Modalmodalidades.php'; ?>

    <form method="get" action="Carrerasuni.php">
        <input type="hidden" name="modalidad" value="<?php echo htmlspecialchars($modalidadActual, ENT_QUOTES, 'UTF-8'); ?>">

        <main class="admin-main">

            <div class="admin-toolbar">
                <h2>OEPA</h2>
            </div>

            <section class="cards-grid" id="cardsGrid">

                <?php if (empty($universidades)): ?>
                    <p style="grid-column: 1/-1; text-align:center; color:#666; padding: 40px;">
                        No hay universidades que ofrezcan carreras en esta modalidad.
                    </p>
                <?php else: ?>

                    <?php foreach ($universidades as $u): ?>
                        <article class="uni-card" data-id="<?php echo $u['universidad_id']; ?>">

                            <div class="card-logo">
                                <img src="<?php echo htmlspecialchars($u['logo'] ?? ''); ?>"
                                     alt="<?php echo htmlspecialchars($u['nombre_institucion']); ?>">
                            </div>

                            <p class="card-nombre">
                                <?php echo htmlspecialchars($u['nombre_institucion']); ?>
                            </p>

                            <button class="btn-outline btn-carreras"
                                    name="idu"
                                    value="<?php echo $u['universidad_id']; ?>"
                                    type="submit">
                                Ver Carreras de la universidad
                            </button>

                        </article>
                    <?php endforeach; ?>

                <?php endif; ?>

            </section>

        </main>
    </form>

    <script src="buscador.js"></script>

</body>
</html>
```