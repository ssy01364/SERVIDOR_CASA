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

$query = "SELECT u.usuario, u.id, COUNT(c.id) as total_contactos
          FROM usuarios u
          LEFT JOIN contactos c ON u.id = c.usuario_id
          GROUP BY u.id, u.usuario";
$result = $mysqli->query($query);

$users = array();
while ($row = $result->fetch_assoc()) {
    $users[] = $row;
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Totales - Agenda</title>
    <style>
        .bar {
            height: 20px;
            background-color: #4CAF50;
            text-align: right;
            padding-right: 5px;
            color: white;
        }
    </style>
</head>
<body>
    <h2>Totales de Contactos por Usuario</h2>
    <table border="1" cellpadding="5">
        <tr>
            <th>Usuario</th>
            <th>Total Contactos</th>
        </tr>
        <?php foreach ($users as $user): ?>
        <tr>
            <td><?php echo $user['usuario']; ?></td>
            <td><?php echo $user['total_contactos']; ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
    
    <h3>Gráfica</h3>
    <?php
    $max = 0;
    foreach ($users as $user) {
        if ($user['total_contactos'] > $max) {
            $max = $user['total_contactos'];
        }
    }
    if ($max == 0) $max = 1;
    
    foreach ($users as $user) {
        $width = ($user['total_contactos'] / $max) * 300;
        echo "<p>" . $user['usuario'] . ": <div class='bar' style='width: {$width}px;'>" . $user['total_contactos'] . "</div></p>";
    }
    ?>
    
    <hr>
    <nav>
        <ul>
            <li><a href="grabado.php">Volver a Grabado</a></li>
            <li><a href="index.php">Cerrar sesión</a></li>
        </ul>
    </nav>
</body>
</html>
