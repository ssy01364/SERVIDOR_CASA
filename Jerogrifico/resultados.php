<?php
session_start();
include("conexion.php");

$fecha_actual = "2024-12-12";

echo "<h2>Resultados del día: $fecha_actual</h2>";

// Obtener la solución correcta del día
$query_solucion = "SELECT solucion FROM solucion WHERE fecha = ?";
$stmt_solucion = $conexion->prepare($query_solucion);
$stmt_solucion->bind_param("s", $fecha_actual);
$stmt_solucion->execute();
$result_solucion = $stmt_solucion->get_result();
$solucion_data = $result_solucion->fetch_assoc();
$solucion = $solucion_data ? trim($solucion_data['solucion']) : '';

$stmt_solucion->close();

// Obtener respuestas de los jugadores
$query_respuestas = "SELECT jugador.login, respuestas.hora, respuestas.respuesta 
                     FROM respuestas 
                     JOIN jugador ON respuestas.login = jugador.login 
                     WHERE respuestas.fecha = ?";
$stmt_respuestas = $conexion->prepare($query_respuestas);
$stmt_respuestas->bind_param("s", $fecha_actual);
$stmt_respuestas->execute();
$result_respuestas = $stmt_respuestas->get_result();

$aciertos = [];
$fallos = [];
while ($row = $result_respuestas->fetch_assoc()) {
    if (strcasecmp(trim($row['respuesta']), $solucion) == 0) {
        $aciertos[] = $row;
    } else {
        $fallos[] = $row;
    }
}
$stmt_respuestas->close();

// Sumar puntos a jugadores que acertaron
$query_actualizar_puntos = "UPDATE jugador SET puntos = puntos + 1 WHERE login IN 
                           (SELECT login FROM respuestas WHERE fecha = ? AND respuesta = ?)";
$stmt_actualizar_puntos = $conexion->prepare($query_actualizar_puntos);
$stmt_actualizar_puntos->bind_param("ss", $fecha_actual, $solucion);
$stmt_actualizar_puntos->execute();
$stmt_actualizar_puntos->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Resultados del Día</title>
    <style>
        table {
            width: 50%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
            text-align: center;
            padding: 8px;
        }
        th {
            background-color: lightgray;
        }
    </style>
</head>
<body>
    <h2>Fecha: <?php echo $fecha_actual; ?></h2>

    <h3>Jugadores acertantes: <?php echo count($aciertos); ?></h3>
    <table>
        <tr>
            <th>Login</th>
            <th>Hora</th>
        </tr>
        <?php foreach ($aciertos as $row): ?>
        <tr>
            <td><?php echo htmlspecialchars($row['login']); ?></td>
            <td><?php echo $row['hora']; ?></td>
        </tr>
        <?php endforeach; ?>
    </table>

    <h3>Jugadores que han fallado</h3>
    <table>
        <tr>
            <th>Login</th>
            <th>Hora</th>
        </tr>
        <?php foreach ($fallos as $row): ?>
        <tr>
            <td><?php echo htmlspecialchars($row['login']); ?></td>
            <td><?php echo $row['hora']; ?></td>
        </tr>
        <?php endforeach; ?>
    </table>

    <br>
    <a href="inicio.php">Volver</a>
</body>
</html>
