<?php
session_start();

if (!isset($_SESSION['id_usu'])) {
    die("Acceso no autorizado.");
}

$idUsuario = intval($_SESSION['id_usu']);

require_once 'conexion.php';

// Validar mes y año seleccionados
$mesSeleccionado = isset($_GET['mes']) ? filter_var($_GET['mes'], FILTER_VALIDATE_INT, ["options" => ["min_range" => 1, "max_range" => 12]]) : date('m');
$anioSeleccionado = isset($_GET['anio']) ? filter_var($_GET['anio'], FILTER_VALIDATE_INT, ["options" => ["min_range" => 2000, "max_range" => 3000]]) : date('Y');

$datosInsulina = [];
$sumaInsulina = 0;
$totalRegistros = 0;

// Consulta para sumar insulina por día (incluye insulina de CONTROL_GLUCOSA y COMIDA)
$query = "SELECT DAY(cg.fecha) AS dia, 
                 SUM(cg.lenta + IFNULL(c.insulina, 0)) AS total_insulina
          FROM CONTROL_GLUCOSA cg
          LEFT JOIN COMIDA c ON cg.fecha = c.fecha AND cg.id_usu = c.id_usu
          WHERE MONTH(cg.fecha) = ? 
            AND YEAR(cg.fecha) = ? 
            AND cg.id_usu = ?
          GROUP BY DAY(cg.fecha)
          ORDER BY dia ASC";

$stmt = $conn->prepare($query);
$stmt->bind_param("iii", $mesSeleccionado, $anioSeleccionado, $idUsuario);
$stmt->execute();
$resultado = $stmt->get_result();
$registros = $resultado->fetch_all(MYSQLI_ASSOC);

foreach ($registros as $fila) {
    $datosInsulina[] = ["x" => $fila['dia'], "y" => $fila['total_insulina']];
    $sumaInsulina += $fila['total_insulina'];
    $totalRegistros++;
}

$promedioInsulina = $totalRegistros > 0 ? round($sumaInsulina / $totalRegistros, 2) : null;

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Estadísticas de Insulina</title>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    * { box-sizing: border-box; font-family: 'Roboto', sans-serif; }
    body {
      background: linear-gradient(135deg, #2C3E50, #4CA1AF);
      color: #f9f9f9;
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
      padding: 20px;
      margin: 0;
    }
    .container {
      background: rgba(0, 0, 0, 0.3);
      padding: 30px;
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
      margin-bottom: 20px;
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
    .btn {
      padding: 12px 20px;
      font-size: 1.1rem;
      font-weight: bold;
      border-radius: 8px;
      cursor: pointer;
      transition: 0.3s ease-in-out;
      border: none;
    }
    .btn-estadisticas {
      background-color: #27ae60;
      color: #fff;
      margin-top: 10px;
    }
    .btn-estadisticas:hover {
      background-color: #1e8449;
      transform: scale(1.05);
    }
    .btn-volver {
      background-color: #2980b9;
      color: #fff;
      text-decoration: none;
      display: inline-block;
      margin-top: 25px;
    }
    .btn-volver:hover {
      background-color: #21618c;
      transform: scale(1.05);
    }
    .resultado-insulina {
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
  </style>
</head>
<body>
  <div class="container">
    <h2>📊 Estadísticas de Insulina</h2>
    <form method="GET" action="estadisticas.php">
      <div class="form-container">
        <div class="form-group">
          <label for="mes">Mes:</label>
          <input type="number" name="mes" id="mes" value="<?= $mesSeleccionado ?>" min="1" max="12" required>
        </div>
        <div class="form-group">
          <label for="anio">Año:</label>
          <input type="number" name="anio" id="anio" value="<?= $anioSeleccionado ?>" min="2000" max="3000" required>
        </div>
      </div>
      <button type="submit" class="btn btn-estadisticas">📊 Consultar</button>
    </form>

    <?php if ($promedioInsulina !== null): ?>
      <div class="resultado-insulina">
        Promedio diario de Insulina: <?= number_format($promedioInsulina, 2) ?> UI
      </div>
    <?php endif; ?>

    <canvas id="graficaInsulina"></canvas>
    <a href="seleccionar.php" class="btn btn-volver">🏠 Menú Principal</a>
  </div>

  <script>
    const ctx = document.getElementById('graficaInsulina').getContext('2d');
    const datosInsulina = <?= json_encode($datosInsulina) ?>;
    
    new Chart(ctx, {
      type: 'line',
      data: {
        labels: datosInsulina.map(d => d.x),
        datasets: [{
          label: 'Insulina Administrada por Día (UI)',
          data: datosInsulina.map(d => d.y),
          backgroundColor: 'rgba(52, 152, 219, 0.6)',
          borderColor: '#3498db',
          borderWidth: 2,
          fill: true
        }]
      },
      options: {
        scales: {
          x: { title: { display: true, text: 'Días del Mes' } },
          y: { beginAtZero: true, title: { display: true, text: 'Unidades de Insulina (UI)' } }
        }
      }
    });
  </script>
</body>
</html>
