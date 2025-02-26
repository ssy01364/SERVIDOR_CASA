<?php 
session_start();

if (!isset($_SESSION['id_usu'])) {
    die("Por favor, inicia sesión para ver esta página.");
}

$id_usu = intval($_SESSION['id_usu']);

include '../conexion.php';  

$mes = isset($_GET['mes']) ? $_GET['mes'] : date('m');
$anio = isset($_GET['anio']) ? $_GET['anio'] : date('Y');

$promedio_glucosa_lenta = null;

$sql = "SELECT DAY(fecha) AS dia, lenta 
        FROM CONTROL_GLUCOSA 
        WHERE MONTH(fecha) = ? 
          AND YEAR(fecha) = ? 
          AND id_usu = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iii", $mes, $anio, $id_usu);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows === 0) {
    $mensaje = "No hay datos disponibles para el mes y año seleccionados.";
} else {
    // Creamos un array con los días del mes (1 a 31)
    $dias = range(1, 31);
    $niveles_glucosa = array_fill(0, 31, null);

    $total_lenta = 0;
    $dias_con_datos = 0;

    while ($row = $resultado->fetch_assoc()) {
        $dia_index = $row['dia'] - 1;
        $niveles_glucosa[$dia_index] = $row['lenta'];

        if ($row['lenta'] !== null) {
            $total_lenta += $row['lenta'];
            $dias_con_datos++;
        }
    }

    $promedio_glucosa_lenta = ($dias_con_datos > 0) ? ($total_lenta / $dias_con_datos) : null;
}

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
    /* Estilos específicos para la página de estadísticas */
    .container-statistics {
      background: rgba(255, 255, 255, 0.1);
      backdrop-filter: blur(10px);
      padding: 2rem;
      border-radius: 10px;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
      width: 95%;
      max-width: 800px;
      text-align: center;
      color: white;
      margin: 20px auto;
      overflow: auto;
      max-height: 1200px;
    }
    .container-statistics h2 {
      margin-bottom: 20px;
      font-size: 2.5rem;
    }
    .input-group {
      margin: 15px 0;
      text-align: left;
    }
    .input-group label {
      display: block;
      font-size: 14px;
      margin-bottom: 5px;
    }
    .input-group input {
      width: 100%;
      padding: 10px;
      border: none;
      border-radius: 5px;
      background: rgba(255,255,255,0.2);
      color: white;
      outline: none;
    }
    .input-group input::placeholder {
      color: rgba(255,255,255,0.7);
    }
    /* Navegación para cambiar mes */
    .nav-statistics {
      display: flex;
      justify-content: space-between;
      margin-bottom: 15px;
    }
    .nav-statistics a {
      text-decoration: none;
      color: white;
      background: #e67e22;
      padding: 10px 15px;
      border-radius: 5px;
      font-size: 1.2rem;
      transition: 0.3s;
    }
    .nav-statistics a:hover {
      background: #d35400;
    }
    .nav-statistics a:active {
      transform: scale(0.98);
    }
    /* Botón para menú principal */
    .btn-statistics {
      background-color: #3498db;
      color: white;
      border: none;
      padding: 12px 24px;
      font-size: 16px;
      font-weight: bold;
      border-radius: 5px;
      cursor: pointer;
      transition: background-color 0.3s, transform 0.2s;
      text-decoration: none;
      display: inline-block;
      margin-top: 20px;
    }
    .btn-statistics:hover {
      background-color: #2980b9;
      transform: scale(1.05);
    }
    .btn-statistics:active {
      background-color: #1f618d;
      transform: scale(0.98);
    }
    /* Estilos para el canvas de la gráfica */
    canvas {
      margin-top: 30px;
      max-width: 100%;
    }
    /* Promedio de glucosa */
    .promedio-glucosa {
      margin-top: 20px;
      font-size: 18px;
      font-weight: bold;
      background: rgba(0, 0, 0, 0.5);
      padding: 10px;
      border-radius: 5px;
    }
  </style>
</head>
<body>
  <div class="container-statistics">
    <h2>Gráfica de Niveles de Glucosa</h2>
    <form method="GET" action="estadisticas.php">
      <div class="input-group">
        <label for="mes">Mes:</label>
        <input type="number" name="mes" id="mes" value="<?php echo $mes; ?>" min="1" max="12" required>
      </div>
      <div class="input-group">
        <label for="anio">Año:</label>
        <input type="number" name="anio" id="anio" value="<?php echo $anio; ?>" min="2000" max="3000" required>
      </div>
      <button type="submit" class="login-btn">📊 Ver Estadísticas</button>
    </form>

    <?php if (isset($promedio_glucosa_lenta) && $promedio_glucosa_lenta !== null): ?>
      <div class="promedio-glucosa">
        Promedio de Glucosa Lenta: <?php echo number_format($promedio_glucosa_lenta, 2); ?> mg/dL
      </div>
    <?php endif; ?>

    <?php if (!empty($resultado) && $resultado->num_rows > 0): ?>
      <canvas id="glucosaChart"></canvas>
      <script>
        const ctx = document.getElementById('glucosaChart').getContext('2d');
        const dias = <?php echo json_encode($dias); ?>;
        const nivelesGlucosa = <?php echo json_encode($niveles_glucosa); ?>;
        const promedio = <?php echo ($promedio_glucosa_lenta !== null) ? $promedio_glucosa_lenta : 'null'; ?>;

        const glucosaChart = new Chart(ctx, {
          type: 'bar',
          data: {
            labels: dias,
            datasets: [{
              label: 'Nivel de Glucosa Lenta',
              data: nivelesGlucosa,
              backgroundColor: '#f39c12',
              borderColor: '#e67e22',
              borderWidth: 1
            }]
          },
          options: {
            scales: {
              y: {
                beginAtZero: true,
                title: {
                  display: true,
                  text: 'Nivel de Glucosa (mg/dL)',
                  color: '#fff',
                  font: {
                    size: 14,
                  }
                },
                ticks: {
                  color: '#f39c12'
                }
              },
              x: {
                title: {
                  display: true,
                  text: 'Días del Mes',
                  color: '#fff',
                  font: {
                    size: 14,
                  }
                },
                ticks: {
                  color: '#f39c12'
                }
              }
            },
            plugins: {
              annotation: {
                annotations: {
                  line1: {
                    type: 'line',
                    yMin: promedio,
                    yMax: promedio,
                    borderColor: 'red',
                    borderWidth: 2,
                    label: {
                      content: 'Promedio: ' + promedio.toFixed(2),
                      enabled: true,
                      position: 'center',
                      backgroundColor: 'rgba(255, 255, 255, 0.5)',
                      font: {
                        size: 12
                      }
                    }
                  }
                }
              }
            }
          }
        });
      </script>
    <?php else: ?>
      <div class="promedio-glucosa">
        <?php echo $mensaje ?? "No hay datos disponibles."; ?>
      </div>
    <?php endif; ?>

    <div class="button-container">
      <a href="seleccionar.php" class="btn-statistics">📋 Menú Principal</a>
    </div>
  </div>
</body>
</html>
