<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit();
}
require_once 'login.php';
$mysqli = new mysqli($hn, $un, $pw, $db);
if ($mysqli->connect_error) {
    die("Error en la conexión: " . $mysqli->connect_error);
}

$usuario_id = $_SESSION['usuario_id'];
$query = "SELECT COUNT(*) as total FROM contactos WHERE usuario_id='$usuario_id'";
$result = $mysqli->query($query);
$row = $result->fetch_assoc();
$totalContactos = $row['total'];
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Grabado - Agenda</title>
</head>
<body>
    <h2>Agenda - <?php echo $_SESSION['usuario']; ?></h2>
    <p>Número de contactos grabados: <?php echo $totalContactos; ?></p>
    <hr>
    <nav>
        <ul>
            <li><a href="inicio.php">Nueva Entrada</a></li>
            <li><a href="agenda.php">Introducir Contactos</a></li>
            <li><a href="totales.php">Totales</a></li>
            <li><a href="index.php">Cerrar sesión</a></li>
        </ul>
    </nav>
</body>
</html>
