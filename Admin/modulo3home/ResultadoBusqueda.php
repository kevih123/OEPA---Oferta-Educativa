<?php
require '../conn.php';

// Función para normalizar texto removiendo acentos en PHP
function normalizarTexto($texto) {
    $texto = mb_strtolower($texto, 'UTF-8');
    $mapa = [
        'á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u',
        'ä' => 'a', 'ë' => 'e', 'ï' => 'i', 'ö' => 'o', 'ü' => 'u',
        'à' => 'a', 'è' => 'e', 'ì' => 'i', 'ò' => 'o', 'ù' => 'u'
    ];
    return strtr($texto, $mapa);
}

$query_original = isset($_GET['q']) ? trim($_GET['q']) : '';
$query_norm = normalizarTexto($query_original);

$carrera_principal = null;
$carreras_similares = [];
$busqueda_realizada = !empty($query_original);

if ($busqueda_realizada) {
    $q_esc = mysqli_real_escape_string($conectar, $query_original);

    // 1. Buscar la coincidencia principal de la carrera usando COLLATE insensibles a acentos
    $sql_principal = "SELECT 
                        c.id_carrera,
                        c.nombre_carrera,
                        c.descripcion,
                        c.certificaciones,
                        c.id_area,
                        a.nombre_area
                      FROM carreras AS c
                      JOIN areas AS a ON c.id_area = a.id
                      WHERE c.nombre_carrera COLLATE utf8mb4_general_ci LIKE '%$q_esc%'
                      LIMIT 1";

    $res_principal = mysqli_query($conectar, $sql_principal);

    if ($res_principal && mysqli_num_rows($res_principal) > 0) {
        $carrera_principal = mysqli_fetch_assoc($res_principal);
        $id_area = $carrera_principal['id_area'];
        $id_carrera_actual = $carrera_principal['id_carrera'];

        // 2. Consultar otras carreras de la misma área
        $sql_similares = "SELECT id_carrera, nombre_carrera, descripcion 
                          FROM carreras 
                          WHERE id_area = $id_area AND id_carrera != $id_carrera_actual 
                          ORDER BY nombre_carrera ASC 
                          LIMIT 6";

        $res_similares = mysqli_query($conectar, $sql_similares);
        while ($fila = mysqli_fetch_assoc($res_similares)) {
            $carreras_similares[] = $fila;
        }
    }
}

mysqli_close($conectar);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultado de Búsqueda - OEPA</title>
    <link rel="stylesheet" href="../Estilos/general.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        .search-results-container {
            max-width: 900px;
            margin: 20px auto;
        }

        .main-result-card {
            background-color: #ffffff;
            border: 2px solid #0f4c81;
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.05);
        }

        .area-badge {
            display: inline-block;
            background-color: #0f4c81;
            color: #fff;
            padding: 4px 12px;
            border-radius: 15px;
            font-size: 0.85rem;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .no-results {
            text-align: center;
            padding: 50px 20px;
            background: #fff;
            border-radius: 10px;
            border: 1px solid #e0e0e0;
        }

        .similares-section {
            margin-top: 30px;
        }

        .similares-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 15px;
            margin-top: 15px;
        }

        .similar-card {
            background: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
        }

        .btn-unis {
            display: inline-block;
            margin-top: 15px;
            background-color: #e2e8f0;
            color: #1a252f;
            padding: 8px 16px;
            border-radius: 20px;
            text-decoration: none;
            font-weight: bold;
            border: 1px solid #111;
        }
    </style>
</head>
<body>

    <header class="admin-header">
        <div class="header-top">
            <h1>OEPA</h1>
            <div class="search-bar-container">
                <form action="ResultadoBusqueda.php" method="GET" class="search-bar">
                    <input type="text" id="buscador" name="q" placeholder="Busca Carrera" autocomplete="off">
                    <button type="submit" class="btn-search-icon">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </button>
                    
                    <!-- El contenedor de sugerencias DENTRO del form para alineación precisa -->
                    <div id="sugerencias-box" class="sugerencias-box" style="position: absolute; top: 100%; left: 0; width: 100%; background: #ffffff; z-index: 99999; display: none; border-radius: 0 0 10px 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.3);"></div>
                </form>
            </div>
            <a class="btn-primary" href="../login.php">¿Eres admin?</a>
        </div>
        <nav class="header-nav">
            <a href="Paginaprincipal.php">Universidades</a>
            <a href="oferta_educativa.php" class="nav-active">Carreras</a>
        </nav>
    </header>

    <main class="admin-main">
        <div class="search-results-container">
            <h2>Resultados de búsqueda para: "<em><?php echo htmlspecialchars($query_original); ?></em>"</h2>

            <?php if ($carrera_principal): ?>
                <!-- Coincidencia Principal -->
                <article class="main-result-card">
                    <span class="area-badge"><?php echo htmlspecialchars($carrera_principal['nombre_area']); ?></span>
                    <h3><?php echo htmlspecialchars($carrera_principal['nombre_carrera']); ?></h3>
                    <p><?php echo htmlspecialchars($carrera_principal['descripcion']); ?></p>
                    <a href="CarrerasUniversidades.php?id_carrera=<?php echo $carrera_principal['id_carrera']; ?>" class="btn-unis">
                        Ver Universidades &gt;
                    </a>
                </article>

                <!-- Otras carreras de la misma área -->
                <?php if (!empty($carreras_similares)): ?>
                    <section class="similares-section">
                        <h3>Otras carreras pertenecientes al área de <?php echo htmlspecialchars($carrera_principal['nombre_area']); ?></h3>
                        <div class="similares-grid">
                            <?php foreach ($carreras_similares as $sim): ?>
                                <article class="similar-card">
                                    <h4><?php echo htmlspecialchars($sim['nombre_carrera']); ?></h4>
                                    <p style="font-size: 0.85rem; color: #666;"><?php echo htmlspecialchars($sim['descripcion']); ?></p>
                                    <a href="CarrerasUniversidades.php?id_carrera=<?php echo $sim['id_carrera']; ?>" class="btn-unis" style="font-size: 0.8rem;">
                                        Ver Universidades &gt;
                                    </a>
                                </article>
                            <?php endforeach; ?>
                        </div>
                    </section>
                <?php endif; ?>

            <?php else: ?>
                <!-- Mensaje de no encontrado -->
                <div class="no-results">
                    <i class="fa-solid fa-file-circle-exclamation" style="font-size: 3rem; color: #999; margin-bottom: 15px;"></i>
                    <h3>No se encontró ninguna carrera que coincida con tu búsqueda.</h3>
                    <p style="color: #666;">Intenta con otras palabras clave o explora la <a href="oferta_educativa.php" style="color: #0f4c81;">Oferta Educativa</a> por categorías.</p>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <script src="busqueda_predictiva.js"></script>
</body>
</html>