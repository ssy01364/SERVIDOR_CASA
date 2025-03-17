<?php 
session_start();

if (!isset($_SESSION['id_usu'])) {
    die("Acceso no autorizado.");
}

$idUsuario = intval($_SESSION['id_usu']);

require_once 'conexion.php';

$mesSeleccionado = isset($_GET['mes']) ? intval($_GET['mes']) : intval(date('m'));
$anioSeleccionado = isset($_GET['anio']) ? intval($_GET['anio']) : intval(date('Y'));

$datosGlucosa = [];
$sumaGlucosa = 0;
$totalRegistros = 0;

$query = "SELECT DAY(fecha) AS dia, lenta 
          FROM CONTROL_GLUCOSA 
          WHERE MONTH(fecha) = ? 
            AND YEAR(fecha) = ? 
            AND id_usu = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("iii", $mesSeleccionado, $anioSeleccionado, $idUsuario);
$stmt->execute();
$resultado = $stmt->get_result();

while ($fila = $resultado->fetch_assoc()) {
    $datosGlucosa[] = ["x" => $fila['dia'], "y" => $fila['lenta']];
    $sumaGlucosa += $fila['lenta'];
    $totalRegistros++;
}

$promedioGlucosa = $totalRegistros > 0 ? $sumaGlucosa / $totalRegistros : null;

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Estadísticas de Glucosa</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="login.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    * {
      box-sizing: border-box;
    }
    body {
      font-family: 'Roboto', sans-serif;
      background: linear-gradient(135deg, #2C3E50, #4CA1AF);
      margin: 0;
      padding: 20px;
      color: #f9f9f9;
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
    }
    .container {
      background: rgba(0, 0, 0, 0.3);
      padding: 30px 40px;
      border-radius: 15px;
      box-shadow: 0 10px 20px rgba(0, 0, 0, 0.4);
      max-width: 900px;
      width: 100%;
      text-align: center;
      animation: fadeIn 1s ease-in-out;
    }
    @keyframes fadeIn {
      from { opacity: 0; transform: scale(0.95); }
      to { opacity: 1; transform: scale(1); }
    }
    h2 {
      margin-bottom: 25px;
      font-size: 2.4rem;
      text-shadow: 1px 1px 2px rgba(0,0,0,0.3);
    }
    .form-container {
      display: flex;
      justify-content: center;
      gap: 20px;
      flex-wrap: wrap;
      margin-bottom: 25px;
    }
    .form-group {
      display: flex;
      flex-direction: column;
      align-items: center;
    }
    .form-group label {
      font-size: 1rem;
      margin-bottom: 5px;
    }
    .form-group input {
      padding: 10px;
      border-radius: 8px;
      border: none;
      text-align: center;
      width: 100px;
      font-size: 1rem;
    }
    .btn-estadisticas {
      background-color: #27ae60;
      color: #fff;
      border: none;
      padding: 12px 20px;
      font-size: 1.1rem;
      font-weight: bold;
      border-radius: 8px;
      cursor: pointer;
      transition: background-color 0.3s, transform 0.2s;
      margin-top: 10px;
    }
    .btn-estadisticas:hover {
      background-color: #1e8449;
      transform: scale(1.05);
    }
    .resultado-glucosa {
      margin: 20px auto;
      font-size: 1.2rem;
      font-weight: bold;
      background: rgba(255, 255, 255, 0.2);
      padding: 15px;
      border-radius: 10px;
      width: fit-content;
    }
    canvas {
      margin-top: 30px;
      max-width: 100%;
    }
    .btn-volver {
      background-color: #2980b9;
      color: #fff;
      border: none;
      padding: 14px 26px;
      font-size: 1rem;
      font-weight: bold;
      border-radius: 8px;
      cursor: pointer;
      text-decoration: none;
      display: inline-block;
      margin-top: 25px;
      transition: background-color 0.3s, transform 0.2s;
    }
    .btn-volver:hover {
      background-color: #21618c;
      transform: scale(1.05);
    }
  </style>
</head>
<body>
  <div class="container">
    <h2>Reporte de Niveles de Glucosa</h2>
    <form method="GET" action="estadisticas.php">
      <div class="form-container">
        <div class="form-group">
          <label for="mes">Mes:</label>
          <input type="number" name="mes" id="mes" value="<?= htmlspecialchars($mesSeleccionado) ?>" min="1" max="12" required>
        </div>
        <div class="form-group">
          <label for="anio">Año:</label>
          <input type="number" name="anio" id="anio" value="<?= htmlspecialchars($anioSeleccionado) ?>" min="2000" max="3000" required>
        </div>
      </div>
      <button type="submit" class="btn-estadisticas">📊 Consultar</button>
    </form>

    <?php if ($promedioGlucosa !== null): ?>
      <div class="resultado-glucosa">
        Nivel Promedio de Glucosa: <?= number_format($promedioGlucosa, 2) ?> mg/dL
      </div>
    <?php endif; ?>

    <canvas id="graficaGlucosa"></canvas>
    <a href="seleccionar.php" class="btn-volver">🏠 Menú Principal</a>
  </div>

  <script>
    const ctx = document.getElementById('graficaGlucosa').getContext('2d');
    const datosGlucosa = <?= json_encode($datosGlucosa) ?>;
    new Chart(ctx, {
      type: 'scatter',
      data: {
        datasets: [{
          label: 'Nivel de Glucosa por Día',
          data: datosGlucosa,
          backgroundColor: 'rgba(231, 76, 60, 0.7)',
          borderColor: '#e74c3c',
          pointRadius: 6,
          pointHoverRadius: 8,
        }]
      },
      options: {
        scales: {
          x: {
            type: 'linear',
            position: 'bottom',
            title: {
              display: true,
              text: 'Días del Mes',
              color: '#fff',
              font: { size: 14 }
            },
            ticks: {
              color: '#f1c40f',
              maxRotation: 0,
              minRotation: 0,
              callback: function(value) {
                return Math.round(value);
              }
            },
            grid: { display: false }
          },
          y: {
            beginAtZero: true,
            title: {
              display: true,
              text: 'Glucosa (mg/dL)',
              color: '#fff',
              font: { size: 14 }
            },
            ticks: { color: '#f1c40f' }
          }
        },
        plugins: {
          legend: {
            labels: { color: '#f1c40f' }
          }
        }
      }
    });
  </script>
</body>
</html>
