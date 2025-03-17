<?php
session_start();

if (!isset($_SESSION['id_usu'])) {
    die("Error: Usuario no autenticado.");
}

include 'conexion.php';

$fecha         = $_POST['fecha'];
$deporte       = $_POST['deporte'];
$lenta         = $_POST['lenta'];
$tipo_comida   = $_POST['tipo_comida'];
$gl_1h         = $_POST['gl_1h'];
$gl_2h         = $_POST['gl_2h'];
$raciones      = $_POST['raciones'];
$insulina      = $_POST['insulina'];
$glucosa_hiper = $_POST['glucosa_hiper'] ?? null;
$hora_hiper    = $_POST['hora_hiper'] ?? null;
$correccion    = $_POST['correccion'] ?? null;
$glucosa_hipo  = $_POST['glucosa_hipo'] ?? null;
$hora_hipo     = $_POST['hora_hipo'] ?? null;

if (!isset($fecha, $tipo_comida, $gl_1h, $gl_2h, $raciones, $insulina)) {
    header("Location: formulario.php?error=1");
    exit();
}

$id_usu = $_SESSION['id_usu'];
$sql_check_control = "SELECT * FROM CONTROL_GLUCOSA WHERE fecha = '$fecha' AND id_usu = $id_usu";
$result_check_control = $conn->query($sql_check_control);

if ($result_check_control->num_rows == 0) {
    $sql_control = "INSERT INTO CONTROL_GLUCOSA (fecha, deporte, lenta, id_usu) 
                    VALUES ('$fecha', $deporte, $lenta, $id_usu)";
    if (!$conn->query($sql_control)) {
        die("Error en CONTROL_GLUCOSA: " . $conn->error);
    }
}

$sql_check_comida = "SELECT * FROM COMIDA WHERE fecha = '$fecha' AND tipo_comida = '$tipo_comida' AND id_usu = $id_usu";
$result_check_comida = $conn->query($sql_check_comida);

if ($result_check_comida->num_rows == 0) {
    $sql_comida = "INSERT INTO COMIDA (tipo_comida, gl_1h, gl_2h, raciones, insulina, fecha, id_usu) 
                   VALUES ('$tipo_comida', $gl_1h, $gl_2h, $raciones, $insulina, '$fecha', $id_usu)";
    if (!$conn->query($sql_comida)) {
        die("Error en COMIDA: " . $conn->error);
    }
    $mensaje = "✅ Comida añadida correctamente.";
    $color_mensaje = "#2ecc71"; // Verde
} else {
    $mensaje = "⚠️ Ya existe un registro para el tipo de comida '$tipo_comida' en la fecha $fecha.";
    $color_mensaje = "#e74c3c"; // Rojo
}

if (!empty($glucosa_hiper) && !empty($hora_hiper) && !empty($correccion)) {
    $sql_check_hiper = "SELECT * FROM HIPERGLUCEMIA WHERE fecha = '$fecha' AND tipo_comida = '$tipo_comida' AND id_usu = $id_usu";
    $result_check_hiper = $conn->query($sql_check_hiper);
    if ($result_check_hiper->num_rows == 0) {
        $sql_hiper = "INSERT INTO HIPERGLUCEMIA (glucosa, hora, correccion, tipo_comida, fecha, id_usu) 
                      VALUES ($glucosa_hiper, '$hora_hiper', $correccion, '$tipo_comida', '$fecha', $id_usu)";
        if (!$conn->query($sql_hiper)) {
            die("Error en HIPERGLUCEMIA: " . $conn->error);
        }
    } else {
        header("Location: formulario.php?error=hiper_existente");
        exit();
    }
}

if (!empty($glucosa_hipo) && !empty($hora_hipo)) {
    $sql_check_hipo = "SELECT * FROM HIPOGLUCEMIA WHERE fecha = '$fecha' AND tipo_comida = '$tipo_comida' AND id_usu = $id_usu";
    $result_check_hipo = $conn->query($sql_check_hipo);
    if ($result_check_hipo->num_rows == 0) {
        $sql_hipo = "INSERT INTO HIPOGLUCEMIA (glucosa, hora, tipo_comida, fecha, id_usu) 
                     VALUES ($glucosa_hipo, '$hora_hipo', '$tipo_comida', '$fecha', $id_usu)";
        if (!$conn->query($sql_hipo)) {
            die("Error en HIPOGLUCEMIA: " . $conn->error);
        }
    } else {
        header("Location: formulario.php?error=hipo_existente");
        exit();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultado del Envío</title>
    <link rel="stylesheet" href="login.css">
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background: linear-gradient(135deg, #4a00e0, #8e2de2);
            font-family: 'Roboto', sans-serif;
        }

        .container-result {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
            width: 400px;
            text-align: center;
            animation: fadeIn 0.8s ease-in-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: scale(0.9); }
            to { opacity: 1; transform: scale(1); }
        }

        .container-result h2 {
            font-size: 24px;
            color: #fff;
            margin-bottom: 15px;
        }

        .container-result p {
            font-size: 18px;
            font-weight: bold;
            color: <?php echo $color_mensaje; ?>;
            margin-bottom: 20px;
        }

        .btn-return {
            display: inline-block;
            padding: 10px 20px;
            background: linear-gradient(45deg, #ff512f, #dd2476);
            color: white;
            font-weight: bold;
            border-radius: 8px;
            text-decoration: none;
            transition: transform 0.3s, box-shadow 0.3s;
            box-shadow: 0 4px 10px rgba(255, 81, 47, 0.5);
        }

        .btn-return:hover {
            transform: scale(1.1);
            box-shadow: 0 8px 20px rgba(255, 81, 47, 0.7);
        }
    </style>
</head>
<body>
    <div class="container-result">
        <h2>📌 Resultado del Envío</h2>
        <p><?php echo $mensaje; ?></p>
        <a href="formulario.php" class="btn-return">⬅ Volver al formulario</a>
    </div>
</body>
</html>
