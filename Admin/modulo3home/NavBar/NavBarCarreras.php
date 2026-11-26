<html>
<header class="admin-header">
<div class="header-top">
  <img src="../Imagenes/logo-oepa.jpeg" class="logo-min" alt="Logo OEPA">

  <!-- Contenedor para mantener el título y subtítulo juntos al lado del logo -->
  <div class="logo-text">
      <h1>OEPA</h1>
      <span>Oferta Educativa Pública Aguascalientes</span>
  </div>


  <!-- El form actúa directamente como contenedor .search-bar -->
  <form action="ResultadoBusqueda.php" method="GET" class="search-bar">
    <input type="text" id="buscador" name="q" placeholder="Busca Carrera" autocomplete="off">
    
    <button type="submit" class="btn-search-icon">
      <i class="fa-solid fa-magnifying-glass search-icon"></i>
    </button>

    <!-- Caja flotante de predicciones -->
    <div id="sugerencias-box" class="sugerencias-box"></div>
  </form>

  <a class="btn-primary" href="../login.php" target="_blank" rel="noopener noreferrer">¿Eres administrador?</a>
</div>

<nav class="header-nav">
  <a href="Universidades/UniversidadesInicio.php">
    <i class="fas fa-university"></i> Universidades
  </a>
  <a href="../CarrerasAreas.php?modalidad=<?php echo urlencode($modalidadActual); ?>" class="nav-active">
    <i class="fas fa-graduation-cap"></i> Carreras
  </a>
</nav>
</header>
</html>