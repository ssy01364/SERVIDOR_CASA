<?php 
session_start();

if (!isset($_SESSION['id_usu'])) {
    die("Acceso no autorizado.");
}

$idUsuario = intval($_SESSION['id_usu']);

include '../conexion.php';

$mesSeleccionado = isset($_GET['mes']) ? $_GET['mes'] : date('m');
$anioSeleccionado = isset($_GET['anio']) ? $_GET['anio'] : date('Y');

$datosGlucosa = [];
$mensaje = "";

// Consultar los niveles de glucosa
$query = "SELECT DAY(fecha) AS dia, lenta 
          FROM CONTROL_GLUCOSA 
          WHERE MONTH(fecha) = ? 
            AND YEAR(fecha) = ? 
            AND id_usu = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("iii", $mesSeleccionado, $anioSeleccionado, $idUsuario);
$stmt->execute();
$resultado = $stmt->get_result();

$sumaGlucosa = 0;
$totalRegistros = 0;

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
    <link rel="stylesheet" href="../css/login.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(45deg, #2C3E50, #4CA1AF);
            color: white;
            text-align: center;
            padding: 20px;
        }

        .estadisticas-box {
            background: rgba(0, 0, 0, 0.2);
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            max-width: 850px;
            margin: auto;
        }

        .estadisticas-box h2 {
            margin-bottom: 15px;
            font-size: 2.2rem;
        }

        .form-container {
            display: flex;
            justify-content: space-around;
            margin-bottom: 20px;
        }

        .form-container label {
            display: block;
            font-size: 14px;
            margin-bottom: 5px;
        }

        .form-container input {
            padding: 10px;
            border-radius: 5px;
            border: none;
            text-align: center;
            width: 80px;
        }

        .btn-estadisticas {
            background-color: #27ae60;
            color: white;
            border: none;
            padding: 12px 18px;
            font-size: 16px;
            font-weight: bold;
            border-radius: 6px;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.2s;
        }

        .btn-estadisticas:hover {
            background-color: #1e8449;
            transform: scale(1.05);
        }

        .resultado-glucosa {
            margin-top: 20px;
            font-size: 18px;
            font-weight: bold;
            background: rgba(255, 255, 255, 0.2);
            padding: 12px;
            border-radius: 8px;
        }

        canvas {
            margin-top: 30px;
            max-width: 100%;
        }

        .btn-volver {
            background-color: #2980b9;
            color: white;
            border: none;
            padding: 14px 22px;
            font-size: 16px;
            font-weight: bold;
            border-radius: 6px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            margin-top: 20px;
        }

        .btn-volver:hover {
            background-color: #21618c;
            transform: scale(1.05);
        }
    </style>
</head>
<body>

<div class="estadisticas-box">
    <h2>Reporte de Niveles de Glucosa</h2>

    <form method="GET" action="estadisticas.php">
        <div class="form-container">
            <div>
                <label for="mes">Mes:</label>
                <input type="number" name="mes" id="mes" value="<?= $mesSeleccionado ?>" min="1" max="12" required>
            </div>
            <div>
                <label for="anio">Año:</label>
                <input type="number" name="anio" id="anio" value="<?= $anioSeleccionado ?>" min="2000" max="3000" required>
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
  const glucosaChart = new Chart(ctx, {
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
            maxRotation: 0,  // Asegura que los números estén en una sola fila
            minRotation: 0,
            callback: function(value) {
              return Math.round(value); // Solo muestra números enteros
            }
          },
          grid: { display: false } // Oculta líneas verticales del grid
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
          labels: {
            color: '#f1c40f'
          }
        }
      }
    }
});

</script>

</body>
</html>
