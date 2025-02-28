<?php 
session_start();
require_once 'login.php';

$connection = new mysqli($hn, $un, $pw, $db);
if ($connection->connect_error) die("Fatal Error");

$cod = $_SESSION['cod'];

// Obtener el último codcontacto y sumarle 1
$result_max = $connection->query("SELECT MAX(codcontacto) FROM contactos");
$row_max = $result_max->fetch_array();
$codcontacto = $row_max[0] + 1;

for ($i = 1; $i <= $_SESSION['contador'] + 1; $i++) {
    $nombre = $_POST['nombre' . $i];
    $email = $_POST['email' . $i];
    $telf = $_POST['telefono' . $i];

    $connection->query("INSERT INTO contactos (codcontacto, nombre, email, telefono, codusuario) 
                        VALUES ($codcontacto, '$nombre', '$email', '$telf', $cod)");

    $codcontacto++; // Incrementar para el siguiente contacto
}

$connection->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Agenda</title>
</head>
<body>
    <h1>AGENDA</h1>
    <h2>Hola <?php echo $_SESSION['usu']; ?></h2>
    <p>Se han grabado los <?php echo $_SESSION['contador'] + 1; ?> contactos de <?php echo $_SESSION['usu']; ?></p>
    <a href="index.php">Volver a loguearse</a><br>
    <a href="inicio.php">Introducir más contactos para <?php echo $_SESSION['usu']; ?></a><br>
    <a href="totales.php">Total de contactos guardados</a><br>
</body>
</html>
