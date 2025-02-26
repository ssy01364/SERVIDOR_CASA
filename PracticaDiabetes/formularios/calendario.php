<?php 
session_start();

if (!isset($_SESSION['id_usu'])) {
    die("Usuario no autenticado.");
}
$id_usu = intval($_SESSION['id_usu']);

include '../conexion.php';  

$mes = isset($_GET['mes']) ? $_GET['mes'] : date('m');
$anio = isset($_GET['anio']) ? $_GET['anio'] : date('Y');

$primerDia = date('Y-m-01', strtotime("$anio-$mes-01"));
$ultimoDia = date('Y-m-t', strtotime("$anio-$mes-01"));

// Consulta unificada de fechas y tipos
$sql = "SELECT fecha, 'Glucosa' AS tipo FROM CONTROL_GLUCOSA WHERE id_usu = $id_usu
        UNION 
        SELECT fecha, 'Comida' FROM COMIDA WHERE id_usu = $id_usu
        UNION 
        SELECT fecha, 'Hiperglucemia' FROM HIPERGLUCEMIA WHERE id_usu = $id_usu
        UNION 
        SELECT fecha, 'Hipoglucemia' FROM HIPOGLUCEMIA WHERE id_usu = $id_usu";

$resultado = $conn->query($sql);
$eventos = [];

if ($resultado && $resultado->num_rows > 0) {
    while ($row = $resultado->fetch_assoc()) {
        $eventos[$row['fecha']][] = $row['tipo'];
    }
}

// Calcular el día de la semana del primer día y la cantidad de días del mes
$diaSemana = date('N', strtotime($primerDia));
$diasMes = date('t', strtotime($primerDia));

$conn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Calendario Diabetes</title>
  <!-- Se vincula el mismo archivo CSS que en login.css para mantener la paleta y estilos -->
  <link rel="stylesheet" href="../css/login.css">
  <!-- Estilos adicionales para el calendario -->
  <style>
    .container-calendar {
      background: rgba(255, 255, 255, 0.1);
      backdrop-filter: blur(10px);
      padding: 2rem;
      border-radius: 10px;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
      width: 90%;
      max-width: 800px;
      text-align: center;
      color: white;
      margin: 20px auto;
    }
    .container-calendar h1 {
      margin-bottom: 20px;
      font-size: 24px;
    }
    .nav-calendar {
      display: flex;
      justify-content: space-between;
      margin-bottom: 15px;
    }
    .nav-calendar a {
      text-decoration: none;
      color: white;
      background: #e67e22;
      padding: 10px 15px;
      border-radius: 5px;
      font-size: 1.2rem;
      transition: 0.3s;
    }
    .nav-calendar a:hover {
      background: #d35400;
    }
    .nav-calendar a:active {
      transform: scale(0.98);
    }
    table {
      width: 100%;
      border-collapse: collapse;
    }
    th, td {
      padding: 15px;
      text-align: center;
      border: 1px solid rgba(255, 255, 255, 0.3);
    }
    th {
      background: rgba(255, 255, 255, 0.2);
      color: #fff;
    }
    td {
      cursor: pointer;
    }
    td:hover {
      background-color: #3f7cac;
      transition: 0.3s ease;
    }
    td a {
      color: #f39c12; 
      font-size: 1.5rem;
      text-decoration: none;
      display: block;
      padding: 10px;
      transition: transform 0.1s ease, color 0.3s ease;
    }
    td a:hover {
      background-color: #f39c12;
      color: #fff;
    }
    td a:active {
      transform: scale(0.95);
      color: #fff;
    }
    .button-container {
      margin-top: 20px;
      text-align: center;
    }
    .choose-btn {
      background-color: #3498db;
      color: white;
      border: none;
      padding: 12px 24px;
      font-size: 16px;
      font-weight: bold;
      border-radius: 5px;
      cursor: pointer;
      transition: background-color 0.3s, transform 0.2s;
    }
    .choose-btn:hover {
      background-color: #2980b9;
      transform: scale(1.05);
    }
    .choose-btn:active {
      background-color: #1f618d;
      transform: scale(0.98);
    }
  </style>
</head>
<body>
  <div class="container-calendar">
    <div class="nav-calendar">
      <a href="?mes=<?= ($mes == 1) ? 12 : $mes - 1 ?>&anio=<?= ($mes == 1) ? $anio - 1 : $anio ?>">◀ Mes Anterior</a>
      <h1><?= date("F Y", strtotime($primerDia)) ?></h1>
      <a href="?mes=<?= ($mes == 12) ? 1 : $mes + 1 ?>&anio=<?= ($mes == 12) ? $anio + 1 : $anio ?>">Mes Siguiente ▶</a>
    </div>
    <table>
      <tr>
        <th>Lun</th><th>Mar</th><th>Mié</th><th>Jue</th><th>Vie</th><th>Sáb</th><th>Dom</th>
      </tr>
      <tr>
      <?php
        // Rellenar los días en blanco antes del primer día
        for ($i = 1; $i < $diaSemana; $i++) {
            echo "<td></td>";
        }
        // Mostrar los días del mes
        for ($dia = 1; $dia <= $diasMes; $dia++) {
            $fecha_actual = "$anio-$mes-" . str_pad($dia, 2, "0", STR_PAD_LEFT);
            echo "<td>";
            echo "<a href='datos.php?fecha=$fecha_actual'><strong>$dia</strong></a>";
            echo "</td>";
            // Si es el final de la semana, nueva fila
            if ((($dia + $diaSemana - 1) % 7) == 0) {
                echo "</tr><tr>";
            }
        }
        // Rellenar los días en blanco hasta completar la última fila
        while ((($dia + $diaSemana - 1) % 7) != 1) {
            echo "<td></td>";
            $dia++;
        }
      ?>
      </tr>
    </table>
    <div class="button-container">
      <button type="button" class="choose-btn" onclick="window.location.href='seleccionar.php'">📋 Menú Principal</button>
    </div>
  </div>
</body>
</html>
