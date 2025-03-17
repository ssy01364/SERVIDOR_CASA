<?php
include 'conexion.php';

$mensaje = "";
$mostrarBusqueda = true; // Controla si se muestra el formulario de búsqueda
$mostrarFormulario = false; // Controla si se muestra el formulario de modificación/eliminación

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fecha = $_POST['fecha'] ?? '';
    $tipo_comida = $_POST['tipo_comida'] ?? '';

    if (isset($_POST['buscar'])) {
        $stmt = $conn->prepare("SELECT * FROM COMIDA WHERE fecha = ? AND tipo_comida = ?");
        $stmt->bind_param("ss", $fecha, $tipo_comida);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $comida = $resultado->fetch_assoc();

        if ($comida) {
            $mostrarBusqueda = false; // Oculta el formulario de búsqueda
            $mostrarFormulario = true; // Muestra el formulario de modificación/eliminación
        } else {
            $mensaje = "<p class='message error-message'>No se encontraron datos para la fecha y tipo de comida ingresados.</p>";
        }
    } elseif (isset($_POST['modificar'])) {
        $gl_1h = $_POST['gl_1h'];
        $gl_2h = $_POST['gl_2h'];
        $raciones = $_POST['raciones'];
        $insulina = $_POST['insulina'];
        $id_usu = $_POST['id_usu'];

        $stmt = $conn->prepare("UPDATE COMIDA SET gl_1h=?, gl_2h=?, raciones=?, insulina=? WHERE fecha=? AND tipo_comida=? AND id_usu=?");
        $stmt->bind_param("iiiissi", $gl_1h, $gl_2h, $raciones, $insulina, $fecha, $tipo_comida, $id_usu);

        if ($stmt->execute()) {
            $mensaje = "<p class='message success-message'>Registro actualizado con éxito.</p>";
            $mostrarFormulario = false; // Ocultar el formulario de modificación tras actualizar
        } else {
            $mensaje = "<p class='message error-message'>Error al actualizar: " . $conn->error . "</p>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Modificar Datos</title>
  <style>
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    body {
      background: linear-gradient(135deg, #ff7e5f, #feb47b);
      min-height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      padding: 20px;
    }

    .container-form {
      background-color: rgba(255, 255, 255, 0.3);
      backdrop-filter: blur(12px);
      border-radius: 12px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
      width: 100%;
      max-width: 550px;
      color: #333;
      padding: 25px;
      animation: fadeIn 0.8s ease;
      text-align: center;
    }

    h1 {
      color: #fff;
      margin-bottom: 15px;
    }

    .message {
      font-size: 16px;
      padding: 10px;
      border-radius: 5px;
      text-align: center;
      margin-bottom: 15px;
    }

    .success-message {
      background-color: #28a745;
      color: white;
    }

    .error-message {
      background-color: #dc3545;
      color: white;
    }

    form {
      display: flex;
      flex-direction: column;
      gap: 10px;
    }

    label {
      font-weight: bold;
      color: #fff;
      text-align: left;
    }

    input {
      padding: 10px;
      border: 1px solid #ddd;
      border-radius: 6px;
      font-size: 16px;
    }

    button {
      padding: 10px;
      border: none;
      border-radius: 6px;
      font-size: 16px;
      font-weight: bold;
      cursor: pointer;
      transition: 0.3s ease;
    }

    .btn-buscar {
      background-color: #007bff;
      color: white;
    }

    .btn-buscar:hover {
      background-color: #0056b3;
    }

    .btn-modificar {
      background-color: #ffc107;
      color: black;
    }

    .btn-modificar:hover {
      background-color: #e0a800;
    }

    .btn-regresar, .btn-menu {
      display: block;
      width: 100%;
      text-align: center;
      padding: 10px;
      margin-top: 15px;
      background-color: #007bff;
      color: white;
      font-size: 16px;
      font-weight: bold;
      text-decoration: none;
      border-radius: 6px;
      transition: 0.3s ease;
    }

    .btn-regresar:hover, .btn-menu:hover {
      background-color: #0056b3;
    }

    @keyframes fadeIn {
      from {
        opacity: 0;
        transform: scale(0.95);
      }
      to {
        opacity: 1;
        transform: scale(1);
      }
    }
  </style>
</head>
<body>
  <div class="container-form">
    <h1>Modificar o Eliminar Registro de Comida</h1>
    
    <?= $mensaje ?>

    <?php if ($mostrarBusqueda): ?>
      <form method="post">
        <label>Fecha:</label>
        <input type="date" name="fecha" required>
        <label>Tipo de Comida:</label>
        <input type="text" name="tipo_comida" required>
        <button type="submit" name="buscar" class="btn-buscar">Buscar</button>
      </form>
    <?php endif; ?>

    <?php if ($mostrarFormulario): ?>
      <form method="post">
        <input type="hidden" name="fecha" value="<?= $comida['fecha'] ?>">
        <input type="hidden" name="tipo_comida" value="<?= $comida['tipo_comida'] ?>">
        <input type="hidden" name="id_usu" value="<?= $comida['id_usu'] ?>">

        <label>Glucosa 1h:</label>
        <input type="number" name="gl_1h" value="<?= $comida['gl_1h'] ?>" required>

        <label>Glucosa 2h:</label>
        <input type="number" name="gl_2h" value="<?= $comida['gl_2h'] ?>" required>

        <label>Raciones:</label>
        <input type="number" name="raciones" value="<?= $comida['raciones'] ?>" required>

        <label>Insulina:</label>
        <input type="number" name="insulina" value="<?= $comida['insulina'] ?>" required>

        <button type="submit" name="modificar" class="btn-modificar">Modificar</button>
      </form>

      <a href="modificar.php" class="btn-regresar">Buscar otra comida</a>
    <?php endif; ?>
    <a href="seleccionar.php" class="btn-menu">🏠 Menú</a>

  </div>
</body>
</html>
