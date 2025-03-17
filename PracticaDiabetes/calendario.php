<?php
session_start();

if (!isset($_SESSION['id_usu'])) {
    exit("Acceso denegado.");
}

$usuarioID = intval($_SESSION['id_usu']);

include 'conexion.php';

$mesSeleccionado = isset($_GET['mes']) ? intval($_GET['mes']) : date('m');
$anioSeleccionado = isset($_GET['anio']) ? intval($_GET['anio']) : date('Y');


$mesPadded = str_pad($mesSeleccionado, 2, "0", STR_PAD_LEFT);

$primerDia = "$anioSeleccionado-$mesPadded-01";
$diasEnMes = date('t', strtotime($primerDia));

$queryEventos = "SELECT fecha, 'Glucosa' AS tipo
                 FROM CONTROL_GLUCOSA
                 WHERE id_usu = $usuarioID
                   AND MONTH(fecha) = $mesSeleccionado
                   AND YEAR(fecha) = $anioSeleccionado
                 UNION
                 SELECT fecha, 'Comida'
                 FROM COMIDA
                 WHERE id_usu = $usuarioID
                   AND MONTH(fecha) = $mesSeleccionado
                   AND YEAR(fecha) = $anioSeleccionado
                 UNION
                 SELECT fecha, 'Hiperglucemia'
                 FROM HIPERGLUCEMIA
                 WHERE id_usu = $usuarioID
                   AND MONTH(fecha) = $mesSeleccionado
                   AND YEAR(fecha) = $anioSeleccionado
                 UNION
                 SELECT fecha, 'Hipoglucemia'
                 FROM HIPOGLUCEMIA
                 WHERE id_usu = $usuarioID
                   AND MONTH(fecha) = $mesSeleccionado
                   AND YEAR(fecha) = $anioSeleccionado";

$resultado = $conn->query($queryEventos);
$eventosRegistrados = [];

if ($resultado && $resultado->num_rows > 0) {
    while ($fila = $resultado->fetch_assoc()) {
        $eventosRegistrados[$fila['fecha']][] = $fila['tipo'];
    }
}

$inicioSemana = date('N', strtotime($primerDia));

$conn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Calendario de Registro</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #FFDEE9, #B5FFFC);
            text-align: center;
            padding: 20px;
            margin: 0;
        }
        .calendario-container {
            background: white;
            border-radius: 12px;
            box-shadow: 0px 5px 15px rgba(0,0,0,0.2);
            padding: 20px;
            max-width: 600px;
            margin: auto;
        }
        .mes-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }
        .mes-header h2 {
            margin: 0;
            font-size: 22px;
            color: #333;
        }
        .btn-mes {
            background-color: #ff7eb3;
            color: white;
            padding: 8px 15px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 16px;
            transition: 0.3s;
        }
        .btn-mes:hover {
            background-color: #ff4d80;
        }
        .grid-calendario {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 8px;
        }
        .dia-nombre {
            font-weight: bold;
            color: #555;
            background: #f2f2f2;
            padding: 10px;
            border-radius: 8px;
        }
        .dia-celda {
            background: #ffffff;
            border-radius: 10px;
            padding: 15px;
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            color: #444;
            cursor: pointer;
            transition: 0.3s;
            position: relative;
            box-shadow: 2px 2px 8px rgba(0,0,0,0.1);
        }
        .dia-celda:hover {
            background: #ffeb3b;
        }
        .evento-icono {
            font-size: 14px;
            display: block;
            margin-top: 5px;
            color: #ff5722;
        }
        .boton-menu {
            background-color: #2196f3;
            color: white;
            padding: 12px;
            border-radius: 8px;
            font-size: 18px;
            text-decoration: none;
            display: inline-block;
            margin-top: 20px;
            transition: 0.3s;
        }
        .boton-menu:hover {
            background-color: #1976d2;
        }
    </style>
</head>
<body>
<div class="calendario-container">
    <div class="mes-header">
        <a href="?mes=<?= ($mesSeleccionado == 1) ? 12 : $mesSeleccionado - 1 ?>&anio=<?= ($mesSeleccionado == 1) ? $anioSeleccionado - 1 : $anioSeleccionado ?>" class="btn-mes">⬅</a>
        <h2><?= date("F Y", strtotime($primerDia)) ?></h2>
        <a href="?mes=<?= ($mesSeleccionado == 12) ? 1 : $mesSeleccionado + 1 ?>&anio=<?= ($mesSeleccionado == 12) ? $anioSeleccionado + 1 : $anioSeleccionado ?>" class="btn-mes">➡</a>
    </div>
    <div class="grid-calendario">
        <div class="dia-nombre">Lun</div>
        <div class="dia-nombre">Mar</div>
        <div class="dia-nombre">Mié</div>
        <div class="dia-nombre">Jue</div>
        <div class="dia-nombre">Vie</div>
        <div class="dia-nombre">Sáb</div>
        <div class="dia-nombre">Dom</div>
        <?php

        for ($i = 1; $i < $inicioSemana; $i++) {
            echo "<div></div>";
        }
        
        for ($dia = 1; $dia <= $diasEnMes; $dia++) {
            $fecha = "$anioSeleccionado-$mesPadded-" . str_pad($dia, 2, "0", STR_PAD_LEFT);
            $eventoIcono = "";
            if (isset($eventosRegistrados[$fecha])) {
                $eventoIcono = "<span class='evento-icono'>🔴 Evento</span>";
            }
            echo "<div class='dia-celda' onclick=\"window.location='datos.php?fecha=$fecha'\">";
            echo "$dia $eventoIcono";
            echo "</div>";
        }
        ?>
    </div>
    <a class="boton-menu" href="seleccionar.php">🏠 Menú</a>
    <a class="boton-menu" href="modificar.php">✏️ Modificar</a>
    <a class="boton-menu" href="borrar.php">🗑️ Borrar</a>
</div>
</body>
</html>
