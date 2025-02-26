<?php
session_start();

include 'conexion.php';

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

$usuario = trim($_POST['usuario']);
$contra = trim($_POST['contra']);

$sql = "SELECT id_usu, contra FROM USUARIO WHERE usuario = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $usuario);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $usuario_data = $result->fetch_assoc();
    $hash_password = $usuario_data['contra'];

    if (password_verify($contra, $hash_password)) {
        $_SESSION['usuario'] = $usuario;
        $_SESSION['id_usu'] = $usuario_data['id_usu'];

        header("Location: formularios/seleccionar.php");
        exit();
    } else {
        $error_message = "Contraseña incorrecta.";
    }
} else {
    $error_message = "Usuario no encontrado.";
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Autenticación</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background: linear-gradient(135deg, #1e3c72, #2a5298);
            font-family: 'Poppins', sans-serif;
            color: white;
        }
        .container-login {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            width: 350px;
            text-align: center;
        }
        a {
            color: #f39c12;
            text-decoration: none;
            font-weight: bold;
        }
        a:hover {
            color: #e67e22;
        }
    </style>
</head>
<body>
    <div class="container-login">
        <?php if (isset($error_message)): ?>
            <p><?php echo $error_message; ?></p>
            <a href="index.php">Volver a intentarlo</a>
        <?php endif; ?>
    </div>
</body>
</html>
