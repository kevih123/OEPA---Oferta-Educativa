<?php
$idu=$_GET["idu"];
require '../conn.php'; 

// consultas de carreras
$instru = "SELECT 
            u.universidad_id,u.nombre_institucion,
              c.id_carrera,
              a.id AS id_area,
              a.nombre_area,
              c.nombre_carrera,
              c.descripcion,
              c.certificaciones
           FROM carreras  c
           JOIN areas  a ON c.id_area = a.id JOIN universidades_carreras uc on 
           c.id_carrera=uc.id_carrera 
           join universidades u on u.universidad_id=uc.id_universidad
           where u.universidad_id=$idu
           ORDER BY c.id_carrera ASC;";

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
 <!-- ============ HEADER ============ -->
    <header class="admin-header">
        <div class="header-top">
            <h1>OEPA</h1>
            <div class="search-bar">
                <i class="fa-solid fa-magnifying-glass search-icon"></i>
                <input type="text" id="buscador" placeholder="Busca Carrera">
            </div>
             <a class="btn-primary"href='../login.php'">¿Eres admin? </a>
        </div>
        <nav class="header-nav">
            <a href="Paginaprincipal.php">Universidades</a>
            <a href="Carreras.php" >Carreras</a>
           
        </nav>
    </header>

    <!-- ============ CONTENIDO ============ -->
    <main class="admin-main">

        <div class="admin-toolbar">
           <?php if (!empty($carreras)): ?>
            <h2>Carreras de la universidad <?php echo htmlspecialchars($carreras[0]['nombre_institucion']); ?></h2>
            <?php endif; ?>
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
     <script src="buscador.js"></script>
<body>