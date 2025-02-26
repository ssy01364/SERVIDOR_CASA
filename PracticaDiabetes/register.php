<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Usuario</title>
    <link rel="stylesheet" href="css/login.css">
</head>
<body>
    <div class="container-register">
        <h2>Registro de Usuario</h2>
        <?php if (isset($_GET['error'])): ?>
            <p style="color: red;"><?php echo htmlspecialchars($_GET['error']); ?></p>
        <?php endif; ?>

        <form action="paginas/register_process.php" method="POST">
            <div class="form-group">
                <label for="nombre">Nombre:</label>
                <input type="text" id="nombre" name="nombre" required placeholder="Escribe tu nombre">
            </div>

            <div class="form-group">
                <label for="apellidos">Apellidos:</label>
                <input type="text" id="apellidos" name="apellidos" required placeholder="Escribe tus apellidos">
            </div>

            <div class="form-group">
                <label for="usuario">Usuario:</label>
                <input type="text" id="usuario" name="usuario" required placeholder="Escribe un nombre de usuario">
            </div>

            <div class="form-group">
                <label for="contra">Contraseña:</label>
                <input type="password" id="contra" name="contra" required placeholder="Crea una contraseña">
            </div>

            <div class="form-group">
                <label for="fecha_nacimiento">Fecha de Nacimiento:</label>
                <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" required>
            </div>

            <button class="login-btn" type="submit">Registrarse</button>
        </form>

        <form action="index.php" method="GET">
            <button class="register-btn" type="submit">Iniciar Sesión</button>
        </form>
    </div>
</body>
</html>
