<?php
session_start();
include 'conexion.php';

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

$response_message = ""; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = htmlspecialchars(trim($_POST['nombre'] ?? ''));
    $apellidos = htmlspecialchars(trim($_POST['apellidos'] ?? ''));
    $usuario = htmlspecialchars(trim($_POST['usuario'] ?? ''));
    $contra = trim($_POST['contra'] ?? '');
    $fecha_nacimiento = $_POST['fecha_nacimiento'] ?? null;

    if ($nombre && $apellidos && $usuario && $contra && $fecha_nacimiento) {

        $sql_check = "SELECT id_usu FROM USUARIO WHERE usuario = ?";
        $stmt_check = $conn->prepare($sql_check);
        $stmt_check->bind_param("s", $usuario);
        $stmt_check->execute();
        $stmt_check->store_result();

        if ($stmt_check->num_rows > 0) {
            $response_message = "El usuario ya está registrado. <a href='register.php'>Intenta con otro nombre de usuario</a>";
        } else {
            $hashed_password = password_hash($contra, PASSWORD_DEFAULT);

            $sql_insert = "INSERT INTO USUARIO (fecha_nacimiento, nombre, apellidos, usuario, contra)
                           VALUES (?, ?, ?, ?, ?)";
            $stmt_insert = $conn->prepare($sql_insert);
            $stmt_insert->bind_param("sssss", $fecha_nacimiento, $nombre, $apellidos, $usuario, $hashed_password);

            if ($stmt_insert->execute()) {
                $response_message = "Registro exitoso. <a href='index.php'>Inicia sesión aquí</a>";
            } else {
                $response_message = "Error: " . $stmt_insert->error;
            }

            $stmt_insert->close();
        }

        $stmt_check->close();
    } else {
        $response_message = "Todos los campos son obligatorios.";
    }
} else {
    $response_message = "Método de solicitud no válido.";
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Registro de Usuario</title>
  <link rel="stylesheet" href="login.css">
</head>
<body>
  <div class="container-register">
    <?php if (!empty($response_message)): ?>
      <?php echo $response_message; ?>
    <?php else: ?>
      <h2>Registro de Usuario</h2>
      <form method="POST" action="">
        <input type="text" name="nombre" placeholder="Nombre" required>
        <input type="text" name="apellidos" placeholder="Apellidos" required>
        <input type="text" name="usuario" placeholder="Usuario" required>
        <input type="password" name="contra" placeholder="Contraseña" required>
        <input type="date" name="fecha_nacimiento" required>
        <button type="submit">Registrarse</button>
      </form>
    <?php endif; ?>
  </div>
</body>
</html>
