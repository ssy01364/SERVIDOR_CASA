<?php
session_start();

if (!isset($_SESSION['id_usu'])) {
    die("No estás logueado.");
}

include '../conexion.php';

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
  <link rel="stylesheet" href="../css/login.css"> 
  <style>
    /* Fondo animado con un degradado */
    body {
      margin: 0;
      padding: 0;
      background: linear-gradient(-45deg, #74ebd5, #ACB6E5, #FBC2EB, #FEE140);
      background-size: 400% 400%;
      animation: gradientBG 15s ease infinite;
      font-family: 'Poppins', sans-serif;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      min-height: 100vh;
      color: #fff;
      text-align: center;
      padding: 20px;
    }
    @keyframes gradientBG {
      0% { background-position: 0% 50%; }
      50% { background-position: 100% 50%; }
      100% { background-position: 0% 50%; }
    }

    /* Contenedor principal con efecto glassmorphism */
    .container-result {
      background: rgba(255, 255, 255, 0.1);
      backdrop-filter: blur(10px);
      padding: 40px;
      border-radius: 15px;
      width: 95%;  
      max-width: 1500px; 
      box-shadow: 0 6px 15px rgba(0, 0, 0, 0.3);
      text-align: center;
      overflow: hidden;
      max-height: 1200px;
      overflow-y: auto;
      animation: fadeIn 0.7s ease forwards;
      transform: scale(0.9);
      opacity: 0;
    }
    @keyframes fadeIn {
      to {
        transform: scale(1);
        opacity: 1;
      }
    }
    .container-result h2 {
      font-size: 2.5rem;
      margin-bottom: 30px;
      text-shadow: 1px 1px 3px rgba(0,0,0,0.3);
    }

    .table-container {
      display: flex;
      justify-content: space-between;
      gap: 20px;
      margin-top: 30px;
    }
    .table-container table {
      width: 48%; 
      border-collapse: collapse;
      background: rgba(255,255,255,0.1);
      border-radius: 10px;
      overflow: hidden;
    }
    table th, table td {
      padding: 18px;
      border: 1px solid rgba(255, 255, 255, 0.3);
      text-align: center;
      font-size: 1.1rem;
      color: gray;
      word-wrap: break-word;
    }
    table th {
      background: rgba(255, 255, 255, 0.2);
      color: #fff;
      font-weight: 600;
    }

    /* Botón de Calendario */
    .calendar-btn {
      margin-top: 20px;
      background: #3498db;
      color: white;
      font-weight: bold;
      padding: 10px 20px;
      border-radius: 5px;
      cursor: pointer;
      transition: background 0.3s, transform 0.2s;
      font-size: 1rem;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      gap: 8px;
    }
    .calendar-btn:hover {
      background: #2980b9;
      transform: scale(1.05);
    }
    .calendar-btn:active {
      transform: scale(0.95);
    }

    /* Sección de "No se encontraron registros" más vistosa */
    .no-records-container {
      background: rgba(255, 255, 255, 0.15);
      backdrop-filter: blur(10px);
      padding: 2rem;
      border-radius: 10px;
      box-shadow: 0 6px 15px rgba(0, 0, 0, 0.3);
      animation: fadeIn 0.7s ease forwards;
      transform: scale(0.9);
      opacity: 0;
      width: 90%;
      max-width: 600px;
      text-align: center;
      margin: 0 auto;
    }
    .no-records-icon {
      font-size: 3rem;
      margin-bottom: 0.5rem;
    }
    .no-records-message {
      font-size: 1.4rem;
      margin-bottom: 1rem;
      text-shadow: 1px 1px 3px rgba(0,0,0,0.3);
    }
  </style>
</head>
<body>
<?php
if ($resultado->num_rows > 0) {
    echo "<div class='container-result'>";
    echo "<h2>Datos del $fecha</h2>";
    echo "<div class='table-container'>";

    // Primera tabla: Fecha, Deporte, Insulina Lenta
    echo "<table>";
    echo "<tr><th>Fecha</th><th>Deporte</th><th>Insulina Lenta</th></tr>";
    while ($fila = $resultado->fetch_assoc()) {
        echo "<tr>
                <td>{$fila['fecha']}</td>
                <td>{$fila['deporte']} min</td>
                <td>{$fila['lenta']} U</td>
              </tr>";
    }
    echo "</table>";

    // Reiniciar puntero para segunda tabla
    $resultado->data_seek(0);

    // Segunda tabla: Tipo de comida, Glucosas, Raciones, Insulina, Hipo e Hiper
    echo "<table>";
    echo "<tr>
            <th>Tipo de Comida</th>
            <th>Glucosa 1h</th>
            <th>Glucosa 2h</th>
            <th>Raciones</th>
            <th>Insulina Comida</th>
            <th>Glucosa Hipo</th>
            <th>Hora Hipo</th>
            <th>Glucosa Hiper</th>
            <th>Hora Hiper</th>
            <th>Corrección Hiper</th>
          </tr>";

    while ($fila = $resultado->fetch_assoc()) {
        // Definir color según tipo de comida
        $colorTipoComida = match (strtolower($fila['tipo_comida'])) {
            'desayuno' => 'gold',
            'comida'   => 'orange',
            'cena'     => 'lightcoral',
            default    => 'white',
        };
        echo "<tr>
                <td style='color: {$colorTipoComida}; font-weight: bold;'>{$fila['tipo_comida']}</td>
                <td>{$fila['gl_1h']} mg/dL</td>
                <td>{$fila['gl_2h']} mg/dL</td>
                <td>{$fila['raciones']}</td>
                <td>{$fila['insulina_comida']} U</td>
                <td>{$fila['glucosa_hipo']} mg/dL</td>
                <td>{$fila['hora_hipo']}</td>
                <td>{$fila['glucosa_hiper']} mg/dL</td>
                <td>{$fila['hora_hiper']}</td>
                <td>{$fila['correccion_hiper']} U</td>
              </tr>";
    }
    echo "</table>";

    echo "</div>"; // .table-container
    echo "</div>"; // .container-result
    echo '<a class="calendar-btn" href="calendario.php">📅 Calendario</a>';
} else {
    // Sección vistosa cuando no hay registros
    echo '<div class="no-records-container">';
    echo '<div class="no-records-icon">😕</div>';
    echo '<h2 class="no-records-message">No se encontraron registros.</h2>';
    echo '<a class="calendar-btn" href="calendario.php">📅 Calendario</a>';
    echo '</div>';
}

$conn->close();
?>
</body>
</html>