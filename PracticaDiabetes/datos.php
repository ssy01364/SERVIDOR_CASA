<?php
session_start();

if (!isset($_SESSION['id_usu'])) {
    die("No estás logueado.");
}

include 'conexion.php';

$id_usu = intval($_SESSION['id_usu']);
$fecha = isset($_GET['fecha']) ? $_GET['fecha'] : date('Y-m-d');

$sql = "SELECT 
            c.fecha, 
            c.deporte, 
            c.lenta, 
            cm.tipo_comida, 
            cm.gl_1h, 
            cm.gl_2h, 
            cm.raciones, 
            cm.insulina AS insulina_comida,
            h.glucosa AS glucosa_hipo, 
            h.hora AS hora_hipo, 
            h.tipo_comida AS tipo_comida_hipo, 
            g.glucosa AS glucosa_hiper, 
            g.hora AS hora_hiper, 
            g.correccion AS correccion_hiper 
        FROM CONTROL_GLUCOSA c
        LEFT JOIN COMIDA cm ON c.fecha = cm.fecha AND c.id_usu = cm.id_usu
        LEFT JOIN HIPOGLUCEMIA h ON cm.tipo_comida = h.tipo_comida AND cm.fecha = h.fecha AND cm.id_usu = h.id_usu
        LEFT JOIN HIPERGLUCEMIA g ON cm.tipo_comida = g.tipo_comida AND cm.fecha = g.fecha AND cm.id_usu = g.id_usu
        WHERE c.fecha = '$fecha' AND c.id_usu = $id_usu";

$resultado = $conn->query($sql);

if (!$resultado) {
    die("Error en la consulta: " . $conn->error);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Datos del Día</title>
  <link href="https://fonts.googleapis.com/css?family=Poppins:400,600,700|Roboto:400,500&display=swap" rel="stylesheet">
  <style>
    /* Fondo degradado animado */
    body {
      margin: 0;
      padding: 0;
      background: linear-gradient(135deg, #1d2671, #c33764);
      background-size: 400% 400%;
      animation: gradientBG 15s ease infinite;
      font-family: 'Poppins', sans-serif;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: flex-start;
      min-height: 100vh;
      color: #f0f0f0;
      padding: 40px 20px;
    }
    @keyframes gradientBG {
      0% { background-position: 0% 50%; }
      50% { background-position: 100% 50%; }
      100% { background-position: 0% 50%; }
    }
    h2 {
      font-size: 2.8rem;
      margin-bottom: 30px;
      text-transform: uppercase;
      letter-spacing: 2px;
      color: #ffffff;
      text-shadow: 2px 2px 8px rgba(0,0,0,0.5);
      position: relative;
    }
    h2::after {
      content: "";
      position: absolute;
      left: 50%;
      transform: translateX(-50%);
      bottom: -10px;
      width: 150px;
      height: 4px;
      background: linear-gradient(90deg, #ff6a00, #ee0979);
      border-radius: 2px;
    }
    /* Contenedor de tarjetas */
    .cards-container {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
      gap: 25px;
      width: 100%;
      max-width: 1200px;
    }
    /* Tarjeta con efecto glassmorphism */
    .card {
      background: rgba(255, 255, 255, 0.1);
      backdrop-filter: blur(10px);
      border: 1px solid rgba(255, 255, 255, 0.2);
      border-radius: 15px;
      padding: 20px 25px;
      box-shadow: 0 8px 20px rgba(0,0,0,0.4);
      transition: transform 0.3s ease, box-shadow 0.3s ease;
      opacity: 0;
      transform: translateY(20px);
      animation: cardAppear 0.8s forwards;
    }
    @keyframes cardAppear {
      to { opacity: 1; transform: translateY(0); }
    }
    .card:hover {
      transform: translateY(-5px) scale(1.02);
      box-shadow: 0 12px 30px rgba(0,0,0,0.5);
    }
    .card h3 {
      margin: 0 0 15px 0;
      font-size: 1.8rem;
      color: #ffcc00;
      text-transform: capitalize;
    }
    .card p {
      margin: 8px 0;
      font-size: 1rem;
      line-height: 1.5;
    }
    .card span.label {
      font-weight: 600;
      color: #ff6a00;
    }
    /* Botón de Calendario */
    .calendar-btn {
      margin-top: 40px;
      background: linear-gradient(90deg, #ff6a00, #ee0979);
      color: #fff;
      font-weight: 700;
      padding: 12px 30px;
      border: none;
      border-radius: 50px;
      cursor: pointer;
      transition: transform 0.3s ease, box-shadow 0.3s ease;
      font-size: 1.1rem;
      text-decoration: none;
      box-shadow: 0 8px 20px rgba(0,0,0,0.4);
    }
    .calendar-btn:hover {
      transform: scale(1.05);
      box-shadow: 0 12px 30px rgba(0,0,0,0.6);
    }
    .calendar-btn:active {
      transform: scale(0.98);
    }
    /* Sección sin registros */
    .no-records {
      background: rgba(255, 255, 255, 0.1);
      backdrop-filter: blur(8px);
      padding: 30px;
      border-radius: 15px;
      box-shadow: 0 8px 20px rgba(0,0,0,0.4);
      text-align: center;
    }
    .no-records h3 {
      color: #ff5252;
      margin-bottom: 10px;
      font-size: 1.8rem;
    }
  </style>
</head>
<body>
<?php
if ($resultado->num_rows > 0) {
    echo "<h2>Datos del $fecha</h2>";
    echo "<div class='cards-container'>";
    while ($fila = $resultado->fetch_assoc()) {
        echo "<div class='card'>";
        echo "<h3>{$fila['fecha']}</h3>";
        echo "<p><span class='label'>Deporte:</span> {$fila['deporte']} min</p>";
        echo "<p><span class='label'>Insulina Lenta:</span> {$fila['lenta']} U</p>";
        echo "<p><span class='label'>Tipo de Comida:</span> {$fila['tipo_comida']}</p>";
        echo "<p><span class='label'>Glucosa 1h:</span> {$fila['gl_1h']} mg/dL</p>";
        echo "<p><span class='label'>Glucosa 2h:</span> {$fila['gl_2h']} mg/dL</p>";
        echo "<p><span class='label'>Raciones:</span> {$fila['raciones']}</p>";
        echo "<p><span class='label'>Insulina Comida:</span> {$fila['insulina_comida']} U</p>";
        echo "<p><span class='label'>Glucosa Hipo:</span> {$fila['glucosa_hipo']} mg/dL</p>";
        echo "<p><span class='label'>Hora Hipo:</span> {$fila['hora_hipo']}</p>";
        echo "<p><span class='label'>Glucosa Hiper:</span> {$fila['glucosa_hiper']} mg/dL</p>";
        echo "<p><span class='label'>Hora Hiper:</span> {$fila['hora_hiper']}</p>";
        echo "<p><span class='label'>Corrección Hiper:</span> {$fila['correccion_hiper']} U</p>";
        echo "</div>";
    }
    echo "</div>";
    echo '<a class="calendar-btn" href="calendario.php">📅 Calendario</a>';
} else {
    echo "<div class='no-records'>";
    echo "<h3>😕 No se encontraron registros.</h3>";
    echo '<a class="calendar-btn" href="calendario.php">📅 Calendario</a>';
    echo "</div>";
}

$conn->close();
?>
</body>
</html>
