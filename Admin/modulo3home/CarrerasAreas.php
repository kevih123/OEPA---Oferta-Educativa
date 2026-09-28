<?php
require '../conn.php'; 

// Obtener todas las áreas junto con sus carreras asociadas
$query = "SELECT 
              a.id AS id_area,
              a.nombre_area,
              a.descripcion AS desc_area,
              c.id_carrera,
              c.nombre_carrera,
              c.descripcion AS desc_carrera,
              c.certificaciones
           FROM areas AS a
           LEFT JOIN carreras AS c ON a.id = c.id_area
           ORDER BY a.id ASC, c.nombre_carrera ASC";

$resultado = mysqli_query($conectar, $query);

// Estructurar la información agrupada por áreas
$areas = [];

while ($fila = mysqli_fetch_assoc($resultado)) {
    $id_area = $fila['id_area'];
    
    // Jalar cada área de la BD y añadirla al arreglo de áreas
    if (!isset($areas[$id_area])) {
        $areas[$id_area] = [
            'nombre_area' => $fila['nombre_area'],
            'desc_area'   => $fila['desc_area'],
            'carreras'    => []
        ];
    }
    
    // Agregar las carreras que pertenecen a cada área
    if (!empty($fila['id_carrera'])) {
        $areas[$id_area]['carreras'][] = [
            'id_carrera'     => $fila['id_carrera'],
            'nombre_carrera' => $fila['nombre_carrera'],
            'desc_carrera'   => $fila['desc_carrera'],
            'certificaciones'=> $fila['certificaciones']
        ];
    }
}

mysqli_close($conectar);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Oferta Educativa - OEPA</title>
    <link rel="stylesheet" href="../Estilos/general.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <!-- Estilos específicos para la vista de áreas desplegables -->
    <link rel="stylesheet" href="../Estilos/CarrerasAreas.css">
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
            <a href="admincarreras.php" class="nav-active">Carreras</a>
        </nav>
    </header>

    <!-- ============ CONTENIDO ============ -->
    <main class="admin-main">

        <div class="admin-toolbar">
            <h2>Oferta Educativa</h2>
        </div>

        <section class="accordion-container" id="accordionContainer">

            <?php if (empty($areas)): ?>
                <p style="text-align:center; color:#666; padding: 40px;">
                    No hay categorías registradas aún.
                </p>
            <?php else: ?>
                <?php foreach ($areas as $id_area => $area): ?>
                    <article class="accordion-item" data-area-id="<?php echo $id_area; ?>">
                        
                        <!-- Encabezado de la Categoría -->
                        <button class="accordion-header" type="button">
                            <div>
                                <p class="area-title"><?php echo htmlspecialchars($area['nombre_area']); ?></p>
                                <p class="area-desc"><?php echo htmlspecialchars($area['desc_area'] ?? 'Información...'); ?></p>
                            </div>
                            <i class="fa-solid fa-chevron-down icon-chevron"></i>
                        </button>

                        <!-- Contenido Desplegable (Lista de Carreras) -->
                        <div class="accordion-content">
                            <div class="carreras-list">
                                <?php if (empty($area['carreras'])): ?>
                                    <p style="padding: 15px 0; color: #888;">No hay carreras registradas en esta categoría.</p>
                                <?php else: ?>
                                    <?php foreach ($area['carreras'] as $carrera): ?>
                                        <div class="carrera-row" data-id="<?php echo $carrera['id_carrera']; ?>">
                                            <div class="carrera-info">
                                                <h4><?php echo htmlspecialchars($carrera['nombre_carrera']); ?></h4>
                                                <p><?php echo htmlspecialchars($carrera['desc_carrera']); ?></p>
                                            </div>
                                            <!-- Botón con redirección pasando el ID de la carrera -->
                                            <a href="CarrerasUniversidades.php?id_carrera=<?php echo $carrera['id_carrera']; ?>" class="btn-ver-unis">
                                                Ver Universidades &gt;
                                            </a>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>

                    </article>
                <?php endforeach; ?>
            <?php endif; ?>

        </section>

    </main>


    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const accordionHeaders = document.querySelectorAll('.accordion-header');

            accordionHeaders.forEach(header => {
                header.addEventListener('click', () => {
                    const item = header.parentElement;
                    const content = item.querySelector('.accordion-content');

                    // Alternar estado activo
                    const isActive = item.classList.contains('active');

                    // Cerrar los demás desplegables (comportamiento acordeón estricto)
                    document.querySelectorAll('.accordion-item').forEach(otherItem => {
                        otherItem.classList.remove('active');
                        otherItem.querySelector('.accordion-content').style.maxHeight = null;
                    });

                    // Si no estaba activo, abrirlo
                    if (!isActive) {
                        item.classList.add('active');
                        content.style.maxHeight = content.scrollHeight + "px";
                    }
                });
            });
        });
    </script>
    <script src="buscador.js"></script>
</body>
</html>