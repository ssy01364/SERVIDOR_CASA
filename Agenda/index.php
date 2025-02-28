<?php
session_start();           // 1ª línea, sin espacios antes
require_once 'login.php';  // carga las variables de conexión

$error = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $mysqli = new mysqli($hn, $un, $pw, $db);
    if ($mysqli->connect_error) {
        die("Error en la conexión: " . $mysqli->connect_error);
    }
    
    $usuario = $mysqli->real_escape_string($_POST['usuario']);
    $clave   = $mysqli->real_escape_string($_POST['clave']);
    
    $query = "SELECT * FROM usuarios WHERE usuario='$usuario' AND clave='$clave' LIMIT 1";
    $result = $mysqli->query($query);
    
    if ($result && $result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $_SESSION['usuario']    = $row['usuario'];
        $_SESSION['usuario_id'] = $row['id'];
        $_SESSION['emojis']     = array();
        header("Location: inicio.php");
        exit();
    } else {
        $error = "Usuario o clave incorrectos.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Login - Agenda</title>
</head>
<body>
    <h2>Login</h2>
    <?php if ($error !== ""): ?>
        <p style="color:red;"><?php echo $error; ?></p>
    <?php endif; ?>
    <form method="POST" action="">
        <label>Usuario:</label>
        <input type="text" name="usuario" required><br><br>
        <label>Clave:</label>
        <input type="password" name="clave" required><br><br>
        <input type="submit" value="Entrar">
    </form>
</body>
</html>
