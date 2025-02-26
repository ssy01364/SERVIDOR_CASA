<?php
session_start(); 

if (!isset($_SESSION['id_usu'])) {
    die("Error: Usuario no autenticado.");
}

include '../conexion.php';

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

if (empty($fecha) || empty($tipo_comida) || empty($gl_1h) || empty($gl_2h) || empty($raciones) || empty($insulina)) {
    header("Location: formulario.php?error=1");
    exit();
}

$id_usu = $_SESSION['id_usu'];

// Inserta en CONTROL_GLUCOSA si no existe registro para la fecha
$sql_check_control = "SELECT * FROM CONTROL_GLUCOSA WHERE fecha = '$fecha' AND id_usu = $id_usu";
$result_check_control = $conn->query($sql_check_control);
if ($result_check_control->num_rows == 0) {
    $sql_control = "INSERT INTO CONTROL_GLUCOSA (fecha, deporte, lenta, id_usu) 
                    VALUES ('$fecha', $deporte, $lenta, $id_usu)";
    if (!$conn->query($sql_control)) {
        die("Error en CONTROL_GLUCOSA: " . $conn->error);
    }
}

// Inserta en COMIDA si no existe registro para ese tipo de comida en la fecha
$sql_check_comida = "SELECT * FROM COMIDA 
                     WHERE fecha = '$fecha' AND tipo_comida = '$tipo_comida' AND id_usu = $id_usu";
$result_check_comida = $conn->query($sql_check_comida);

if ($result_check_comida->num_rows == 0) {
    $sql_comida = "INSERT INTO COMIDA (tipo_comida, gl_1h, gl_2h, raciones, insulina, fecha, id_usu) 
                   VALUES ('$tipo_comida', $gl_1h, $gl_2h, $raciones, $insulina, '$fecha', $id_usu)";
    if (!$conn->query($sql_comida)) {
        die("Error en COMIDA: " . $conn->error);
    }
    $mensaje = "Comida añadida correctamente.";
    $color_mensaje = "green";
} else {
    $mensaje = "Ya existe un registro para el tipo de comida '$tipo_comida' en la fecha $fecha.";
    $color_mensaje = "red";
}

// Inserta en HIPERGLUCEMIA si se proporcionan datos y no existe registro
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

// Inserta en HIPOGLUCEMIA si se proporcionan datos y no existe registro
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
    <link rel="stylesheet" href="../css/login.css">
    <style>
        /* Cambiamos el nombre de la clase principal para resultados */
        .container-result {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            width: 350px;
            text-align: center;
            margin: 0 auto;
        }
        .container-result h2 {
            margin-bottom: 20px;
            font-size: 24px;
        }
        .container-result p {
            margin-bottom: 20px;
            font-size: 16px;
            color: <?php echo $color_mensaje; ?>;
        }
        .container-result a {
            color: #f39c12;
            text-decoration: none;
            font-weight: bold;
            transition: color 0.3s;
        }
        .container-result a:hover {
            color: #e67e22;
        }
    </style>
</head>
<body>
    <div class="container-result">
        <h2>Resultado del Envío</h2>
        <p><?php echo $mensaje; ?></p>
        <a href="formulario.php">Volver al formulario</a>
    </div>
</body>
</html>
