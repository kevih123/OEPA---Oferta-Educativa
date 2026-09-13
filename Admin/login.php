<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="Estilos/login.css">

    <title>Administrador</title>
</head>

<body>

    <main class="login-container">

        <section class="login-card">

            <div class="login-header">
                <h1>UNIVERSIDADES AGS</h1>
                <span>Administración</span>
            </div>

            <div class="login-content">

                <div class="login-image">
                    <img src="Imagenes/logo.jpeg" alt="Universidades">
                </div>

                <form id="loginForm">

                    <h2>Administrador</h2>

                    <div class="input-group">
                        <label for="usuario">Usuario</label>
                        <input
                            type="text"
                            id="usuario"
                            placeholder="Usuario"
                            required
                        >
                    </div>

                    <div class="input-group">
                        <label for="contrasena">Contraseña</label>
                        <input
                            type="password"
                            id="contrasena"
                            placeholder="Contraseña"
                            required
                        >
                    </div>

                    <button type="submit" >
                        Iniciar sesión
                    </button>

                </form>

            </div>

        </section>

    </main>

    <script src="login.js"></script>

</body>
</html>