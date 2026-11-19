<html>
<header class="admin-header">
    <div class="header-top">
        <h1>OEPA</h1>

        <div class="search-bar">
        <input type="text" id="buscador" placeholder="Busca Universidad">
        <i class="fa-solid fa-magnifying-glass search-icon"></i>
        </div>

        <a class="btn-primary" href="../../login.php" target="_blank" rel="noopener noreferrer">¿Eres administrador?</a>
    </div>

    <nav class="header-nav">
        <a href="Universidades/UniversidadesInicio.php" class="nav-active">
        <i class="fas fa-university"></i> Universidades
        </a>
        <a href="../CarrerasAreas.php?modalidad=<?php echo urlencode($modalidadActual); ?>">
        <i class="fas fa-graduation-cap"></i> Carreras
        </a>
    </nav>
</header>
</html>