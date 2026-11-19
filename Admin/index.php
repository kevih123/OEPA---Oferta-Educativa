<?php
// Opcional: Si manejas sesiones o configuraciones globales, puedes requerirlas aquí
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OEPA - Universidades Públicas de Aguascalientes</title>
    <!-- Estilos generales del sistema y estilos específicos de inicio -->
    <link rel="stylesheet" href="Estilos_a/general.css">
    <link rel="stylesheet" href="Estilos_a/Inicio.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="icon" type="image/png" href="Imagenes/oepa-logo-min.png">
</head>
<body>

    <!-- Navbar moderno adaptado -->
    <header class="oepa-landing-nav">
        <div class="nav-container">
            <div class="logo-marca">
                <img src="Imagenes/oepa-logo-min.png" class="logo-min" alt="Logo OEPA">
                
                <!-- Contenedor para mantener el título y subtítulo juntos al lado del logo -->
                <div class="logo-text">
                    <h1>OEPA</h1>
                    <span>Oferta Educativa Pública Aguascalientes</span>
                </div>

            </div>
            <div class="nav-acciones">
                <a href="login.php" class="btn-admin-nav" target="_blank" rel="noopener noreferrer">
                    <i class="fa-solid fa-user-shield"></i> ¿Eres administrador?
                </a>
            </div>
        </div>
    </header>

    <!-- Sección Principal (Hero Section) -->
    <section class="hero-section">
        <div class="hero-content">
            <span class="badge-hero">¡Te ayudamos a decidir tu futuro!</span>
            <h2>La información para decidir tu futuro en un solo lugar</h2>
            <p>Consulta de forma rápida y centralizada las universidades públicas de Aguascalientes, sus carreras disponibles, planes de estudio, costos y modalidades.</p>
            
            <div class="hero-botones">
                <a href="modulo3home/Universidades/UniversidadesInicio.php" class="btn-principal-cta">
                    <i class="fa-solid fa-compass"></i> Busca tu universidad ideal
                </a>
            </div>
        </div>
        <div class="hero-image-container">
            <!-- Imagen ilustrativa moderna de estudiantes -->
            <img src="../Imagenes/estudiantes_hero.jpg" alt="Estudiantes universitarios" onerror="this.src='https://images.unsplash.com/photo-1523240795612-9a054b0db644?auto=format&fit=crop&w=800&q=80'">
        </div>
    </section>

    <!-- Sección: ¿Quiénes Somos / Qué ofrecemos? -->
    <section class="propposito-section">
        <div class="section-title">
            <h3>¿Qué encontrarás en OEPA?</h3>
            <p>Diseñado para facilitar tu transición a la educación superior</p>
        </div>

        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon"><i class="fa-solid fa-building-columns"></i></div>
                <h4>Universidades Públicas</h4>
                <p>Accede a información detallada, ubicación, contactos y sitios oficiales de las principales instituciones públicas del estado.</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon"><i class="fa-solid fa-graduation-cap"></i></div>
                <h4>Oferta Académica Clara</h4>
                <p>Explora perfiles de ingreso, egreso, duración, turnos y enlaces directos a los planes de estudio oficiales de cada carrera.</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon"><i class="fa-solid fa-filter"></i></div>
                <h4>Filtros por Modalidad</h4>
                <p>Encuentra programas que se adapten a tu estilo de vida: presenciales, en línea o en modalidad híbrida.</p>
            </div>
        </div>
    </section>

    <!-- Footer sencillo -->
    <footer class="landing-footer">
        <p>&copy; 2026 OEPA - Oferta Educativa Pública de Aguascalientes. Todos los derechos reservados.</p>
    </footer>

</body>
</html>