<?php
session_start();
include("conexion.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $login = $_POST['login'];
    $clave = $_POST['clave'];

    $sql = "SELECT * FROM jugador WHERE login = ? AND clave = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("ss", $login, $clave);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows == 1) {
        $_SESSION['login'] = $login;
        header("Location: inicio.php");
        exit();
    } else {
        $error = "Usuario o clave incorrectos.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
</head>
<body>
    <h2>Iniciar Sesión</h2>
    <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
    <form method="post" action="">
        <label>Usuario:</label>
        <input type="text" name="login" required>
        <br>
        <label>Clave:</label>
        <input type="password" name="clave" required>
        <br>
        <input type="submit" value="Ingresar">
    </form>
</body>
</html>
