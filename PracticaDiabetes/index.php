<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
    <link rel="stylesheet" href="css/login.css">
</head>
<body>
    <div class="container-login">
        <h2>Iniciar Sesión</h2>
        <form action="autentificacion.php" method="POST">
            <div class="form-group">
                <label for="usuario">Usuario:</label>
                <input type="text" id="usuario" name="usuario" required>
            </div>

            <div class="form-group">
                <label for="contra">Contraseña:</label>
                <input type="password" id="contra" name="contra" required>
            </div>

            <button class="login-btn" type="submit">Iniciar Sesión</button>
        </form>

        <form action="register.php" method="GET">
            <button class="register-btn" type="submit">Regístrate aquí</button>
        </form>
    </div>
</body>
</html>
