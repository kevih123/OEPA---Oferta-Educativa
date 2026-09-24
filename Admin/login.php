<?php
// validacion.php

// Iniciar sesión (una sola vez)
session_start();

// Conexión (tu include, no modificar)
include 'conn.php';

// Inicializar variables
$login_ok = false;
$tried_login = false;
$errorMessage = '';

// Procesar POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Obtener y sanitizar datos (igual que antes)
    $usua = isset($_POST['usuario']) ? $_POST['usuario'] : '';
    $contra = isset($_POST['contra']) ? $_POST['contra'] : '';
    $usua = $conectar->real_escape_string($usua);
    $usuario = strtolower($usua);
    $contra = $conectar->real_escape_string($contra);

    if (!empty($usuario) || !empty($contra)) {
        $tried_login = true;

        // Consulta (sin cambiar)
        $sql = "SELECT count(*) as cuenta FROM administradores WHERE user_name='$usuario' AND password='$contra'";
        $res = mysqli_query($conectar, $sql);

        if ($res && $reg = $res->fetch_array()) {
            if ($reg['cuenta'] > 0) {
                $login_ok = true;
            } else {
                // Credenciales incorrectas: asignar mensaje de error aquí
                $errorMessage = 'Los datos son incorrectos';
            }
        } else {
            $errorMessage = 'Error en la consulta de la base de datos';
        }
    }

    // Si login correcto, iniciar sesión y redirigir (igual que antes)
    if ($login_ok) {
        // NO volver a llamar session_start() aquí (ya se llamó arriba)
        session_regenerate_id(true); // evita session fixation
        $_SESSION['admin_logged'] = true;
        $_SESSION['admin_user'] = $usuario; // o id real si lo tienes

        header('Location: ../Admin/modulo1adminuni/inicioadmin.php');
        exit();
    }

    // Si llegamos aquí y hubo intento de login (POST) pero falló, guardar flash y PRG
    if ($tried_login && !$login_ok) {
        // Guardar mensaje en sesión (flash)
        $_SESSION['login_error'] = $errorMessage;
        // Redirigir a la misma página para convertir POST -> GET (PRG)
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit();
    }
}

// Cerrar conexión
if (isset($conectar) && $conectar) {
    mysqli_close($conectar);
}

// Recuperar mensaje flash (si existe) y eliminarlo para que solo se muestre una vez
$flashError = '';
if (isset($_SESSION['login_error'])) {
    $flashError = $_SESSION['login_error'];
    unset($_SESSION['login_error']);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Administrador</title>
    <link rel="stylesheet" href="Estilos/login.css">
    <style>
        .modal-backdrop { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); align-items: center; justify-content: center; z-index: 9999; }
        .modal { background: #fff; padding: 20px; max-width: 420px; width: 90%; border-radius: 8px; box-shadow: 0 6px 18px rgba(0,0,0,0.2); text-align: center; }
        .modal h2 { margin: 0 0 12px; font-size: 20px; }
        .modal p { margin: 0 0 18px; color: #333; }
        .modal .btn { padding: 8px 16px; background: #007bff; color: #fff; border: none; border-radius: 6px; cursor: pointer; }
    </style>
</head>
<body>
    <input type="image" class="icon" src="Imagenes/return-icon.png" onclick="location.href='../Admin/modulo3home/Paginaprincipal.php'">
    
    <main class="login-container">
        <section class="login-card">
            <div class="login-header">
                <h1>UNIVERSIDADES AGS</h1>
                <span>Administración</span>
            </div>
            <div class="login-content">
                <div class="login-image">
                    <img src="Imagenes/logo-2.jpeg" height="200px" width="200px" alt="Universidades">
                </div>
                <form method="post" id="loginForm" action="">
                    <h2>Administrador</h2>
                    <div class="input-group">
                        <label for="usuario">Usuario</label>
                        <input type="text" autocomplete="off" name="usuario" placeholder="Usuario" required>
                    </div>
                    <div class="input-group">
                        <label for="contrasena">Contraseña</label>
                        <input type="password" autocomplete="off" name="contra" placeholder="Contraseña" required>
                    </div>
                    <button type="submit">Iniciar sesión</button>
                </form>
            </div>
        </section>
    </main>

    <!-- Modal -->
    <div id="errorModalBackdrop" class="modal-backdrop" role="dialog" aria-modal="true" aria-hidden="true">
        <div class="modal" role="document">
            <h2 id="modalTitle">Error</h2>
            <p id="modalMessage">Mensaje de error</p>
            <button id="modalAccept" class="btn">Aceptar</button>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        function showModal(message, title) {
            var backdrop = document.getElementById('errorModalBackdrop');
            var msg = document.getElementById('modalMessage');
            var ttl = document.getElementById('modalTitle');
            msg.textContent = message || 'Ocurrió un error';
            ttl.textContent = title || 'Error';
            backdrop.style.display = 'flex';
            backdrop.setAttribute('aria-hidden', 'false');
        }
        function hideModal() {
            var backdrop = document.getElementById('errorModalBackdrop');
            backdrop.style.display = 'none';
            backdrop.setAttribute('aria-hidden', 'true');
        }
        var acceptBtn = document.getElementById('modalAccept');
        if (acceptBtn) {
            acceptBtn.addEventListener('click', function() {
                hideModal();
                // opcional: limpiar formulario
                // document.getElementById('loginForm').reset();
            });
        }

        // Mostrar modal si hay flashError (PRG)
        <?php if (!empty($flashError)): ?>
            showModal(<?php echo json_encode($flashError); ?>, 'Datos incorrectos');
        <?php endif; ?>
    });
    </script>
</body>
</html>
