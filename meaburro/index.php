<?php
session_start();
require_once 'login.php';

if (isset($_POST['usu'])) {
    $usu = $_POST['usu'];
    $psw = $_POST['psw'];

    $connection = new mysqli($hn, $un, $pw, $db);
    if ($connection->connect_error) die("Fatal Error: " . $connection->connect_error);

    // Consulta para verificar las credenciales del usuario
    $query = "SELECT nombre, login, clave FROM jugador WHERE login = '$usu' AND clave = '$psw'";
    $result = $connection->query($query);
    if (!$result) die("Fatal Error: " . $connection->error);

    if ($result->num_rows != 0) {
        $_SESSION['usu'] = $usu;
        // Recuperar el nombre del usuario y almacenarlo en la sesión
        $result->data_seek(0);
        $_SESSION['nombre'] = htmlspecialchars($result->fetch_assoc()['nombre']);
        echo "LOGUEADO CORRECTAMENTE";
        $connection->close();
        echo <<< _END
        <meta http-equiv="refresh" content="0;URL='mostrarcartas.php'" />
_END;
    } else {
        session_destroy();
        echo "<a href='index.php'>Credenciales incorrectas. Inténtalo de nuevo</a>";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar sesión</title>
</head>
<body>
    <h1>Iniciar sesión</h1>
    <form action="#" method="post">
        <label for="usu">Usuario:</label>
        <input type="text" id="usu" name="usu" required><br>
        <label for="psw">Contraseña:</label>
        <input type="password" id="psw" name="psw" required><br>
        <input type="submit" value="Entrar">
    </form>
</body>
</html>
