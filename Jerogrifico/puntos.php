<?php
session_start();
require 'conexion.php'; 

// Verificar conexión
if ($conexion->connect_error) die("Fatal Error");

// Consulta para obtener los jugadores y sus puntos
$query = "SELECT login, Nombre, puntos FROM jugador ORDER BY puntos DESC";
$result = $conexion->query($query);
if (!$result) die("Fatal Error");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estadística</title>
    <style>
        table {
            border: 1px solid black;
            border-collapse: collapse;
            text-align: left;
        }
        th, td {
            border: 1px solid black;
            padding: 8px;
        }
        .bar {
            background-color: blue;
            height: 20px;
        }
    </style>
</head>
<body>
    <h1>Puntos por jugador</h1>
    <table>
        <tr>
            <th>Usuario</th>
            <th>Puntos</th>
            <th></th>
        </tr>
        <?php
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['Nombre']) . "</td>";
                echo "<td>" . htmlspecialchars($row['puntos']) . "</td>";
                echo "<td><div class='bar' style='width: " . $row['puntos'] . "px;'></div></td>";
                echo "</tr>";
            }
        ?>
    </table>
    <br>
    <div>
        <a href="index.php">VOLVER AL INICIO</a>
    </div>
</body>
</html>

<?php
    $conexion->close();
?>
