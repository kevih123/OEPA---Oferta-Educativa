
<?php
// Modalmodalidades.php
// Componente reutilizable para el filtro de modalidades de OEPA.

// Obtener la modalidad seleccionada desde la URL.
$modalidadActual = $_GET['modalidad'] ?? 'todas';

// Opciones disponibles para el menú.
$modalidadesDisponibles = [
    'todas'      => 'Todas las modalidades',
    'presencial' => 'Presencial',
    'en_linea'   => 'En línea',
    'hibrida'    => 'Híbrida'
];

// Validar la selección para evitar valores no permitidos.
if (!array_key_exists($modalidadActual, $modalidadesDisponibles)) {
    $modalidadActual = 'todas';
}

// Texto que aparecerá dentro del botón.
$textoModalidad = $modalidadActual === 'todas'
    ? 'Modalidades'
    : $modalidadesDisponibles[$modalidadActual];
?>

<style>
    /* Contenedor principal del componente */
    .oepa-modalidades {
        position: relative;
        display: inline-block;
        font-family: Arial, sans-serif;
        z-index: 1000;
    }

    /* Botón principal */
    .oepa-modalidades-boton {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        padding: 12px 18px;
        border: none;
        border-radius: 10px;
        background: #172554;
        color: #ffffff;
        font-size: 15px;
        font-weight: bold;
        cursor: pointer;
        transition: background 0.2s ease;
    }

    .oepa-modalidades-boton:hover {
        background: #123b4a;
    }

    /* Menú desplegable */
    .oepa-modalidades-menu {
        position: absolute;
        top: calc(100% + 8px);
        left: 0;
        min-width: 220px;
        padding: 7px;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
    }

    /* Ocultar el menú cuando está cerrado */
    .oepa-modalidades-menu[hidden] {
        display: none;
    }

    /* Opciones del menú */
    .oepa-modalidad-opcion {
        display: block;
        width: 100%;
        padding: 12px;
        border: none;
        border-radius: 8px;
        background: transparent;
        color: #172554;
        text-align: left;
        font-size: 14px;
        cursor: pointer;
    }

    .oepa-modalidad-opcion:hover {
        background: #ede9fe;
    }

    .oepa-modalidad-opcion.seleccionada {
        background: #cefadd;
        font-weight: bold;
    }

    .oepa-modalidad-flecha {
        transition: transform 0.2s ease;
    }

    .oepa-modalidades-boton[aria-expanded="true"]
    .oepa-modalidad-flecha {
        transform: rotate(180deg);
    }
</style>

<div class="oepa-modalidades" id="oepaModalidades">

    <!-- Botón que abre y cierra el menú -->
    <button
        type="button"
        class="oepa-modalidades-boton"
        id="oepaModalidadesBoton"
        aria-expanded="false"
        aria-controls="oepaModalidadesMenu"
    >
        <span>
            <i class="fa-solid fa-graduation-cap"></i>
            <?= htmlspecialchars($textoModalidad, ENT_QUOTES, 'UTF-8') ?>
        </span>

        <i class="fa-solid fa-chevron-down oepa-modalidad-flecha"></i>
    </button>

    <!-- Formulario que conserva los parámetros actuales -->
    <form
        action="<?= htmlspecialchars(basename($_SERVER['PHP_SELF']), ENT_QUOTES, 'UTF-8') ?>"
        method="GET"
        id="oepaModalidadesFormulario"
    >
        <?php foreach ($_GET as $clave => $valor): ?>
            <?php
            // No duplicar el parámetro de modalidad.
            if ($clave === 'modalidad' || !is_scalar($valor)) {
                continue;
            }
            ?>
            <input
                type="hidden"
                name="<?= htmlspecialchars($clave, ENT_QUOTES, 'UTF-8') ?>"
                value="<?= htmlspecialchars((string)$valor, ENT_QUOTES, 'UTF-8') ?>"
            >
        <?php endforeach; ?>

        <div
            class="oepa-modalidades-menu"
            id="oepaModalidadesMenu"
            hidden
        >
            <?php foreach ($modalidadesDisponibles as $valor => $nombre): ?>
                <button
                    type="submit"
                    name="modalidad"
                    value="<?= htmlspecialchars($valor, ENT_QUOTES, 'UTF-8') ?>"
                    class="oepa-modalidad-opcion <?= $modalidadActual === $valor ? 'seleccionada' : '' ?>"
                    <?= $modalidadActual === $valor ? 'aria-current="true"' : '' ?>
                >
                    <?= htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8') ?>

                    <?php if ($modalidadActual === $valor): ?>
                        <i class="fa-solid fa-check"></i>
                    <?php endif; ?>
                </button>
            <?php endforeach; ?>
        </div>
    </form>

</div>

<script>
(function () {
    // Evitar registrar eventos repetidos si se incluye más de una vez.
    const contenedor = document.getElementById('oepaModalidades');
    if (!contenedor || contenedor.dataset.inicializado === 'true') return;

    contenedor.dataset.inicializado = 'true';

    const boton = document.getElementById('oepaModalidadesBoton');
    const menu = document.getElementById('oepaModalidadesMenu');

    // Abrir o cerrar el menú.
    boton.addEventListener('click', function () {
        const abierto = boton.getAttribute('aria-expanded') === 'true';

        boton.setAttribute('aria-expanded', String(!abierto));
        menu.hidden = abierto;
    });

    // Cerrar el menú al hacer clic fuera de él.
    document.addEventListener('click', function (evento) {
        if (!contenedor.contains(evento.target)) {
            boton.setAttribute('aria-expanded', 'false');
            menu.hidden = true;
        }
    });

    // Cerrar el menú con la tecla Escape.
    document.addEventListener('keydown', function (evento) {
        if (evento.key === 'Escape') {
            boton.setAttribute('aria-expanded', 'false');
            menu.hidden = true;
            boton.focus();
        }
    });
})();
</script>
