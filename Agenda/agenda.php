<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit();
}

$numContactos = isset($_SESSION['emojis']) ? count($_SESSION['emojis']) : 0;
if ($numContactos < 1) {
    header("Location: inicio.php");
    exit();
}

$error = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $contacts = array();
    for ($i = 0; $i < $numContactos; $i++) {
        $nombre   = trim($_POST["nombre_$i"]);
        $telefono = trim($_POST["telefono_$i"]);
        $email    = trim($_POST["email_$i"]);
        
        if ($nombre == "" || $telefono == "" || $email == "") {
            $error = "Todos los campos son obligatorios para cada contacto.";
            break;
        }
        $contacts[] = array("nombre" => $nombre, "telefono" => $telefono, "email" => $email);
    }
    
    if ($error == "") {
        require_once 'login.php';
        $mysqli = new mysqli($hn, $un, $pw, $db);
        if ($mysqli->connect_error) {
            die("Error en la conexión: " . $mysqli->connect_error);
        }
        
        foreach ($contacts as $contact) {
            $nombre   = $mysqli->real_escape_string($contact["nombre"]);
            $telefono = $mysqli->real_escape_string($contact["telefono"]);
            $email    = $mysqli->real_escape_string($contact["email"]);
            $usuario_id = $_SESSION['usuario_id'];
            
            $query = "INSERT INTO contactos (usuario_id, nombre, telefono, email)
                      VALUES ('$usuario_id', '$nombre', '$telefono', '$email')";
            $mysqli->query($query);
        }
        header("Location: grabado.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Agenda - Introducir Contactos</title>
</head>
<body>
    <h2>Agenda - <?php echo $_SESSION['usuario']; ?></h2>
    <h3>Introduce los datos de <?php echo $numContactos; ?> contacto(s)</h3>
    <?php if ($error !== "") { echo "<p style='color:red;'>$error</p>"; } ?>
    <form method="POST" action="">
        <?php for ($i = 0; $i < $numContactos; $i++): ?>
            <fieldset>
                <legend>Contacto <?php echo ($i + 1); ?></legend>
                <label>Nombre:</label>
                <input type="text" name="nombre_<?php echo $i; ?>" required><br>
                <label>Teléfono:</label>
                <input type="text" name="telefono_<?php echo $i; ?>" required><br>
                <label>Email:</label>
                <input type="email" name="email_<?php echo $i; ?>" required><br>
            </fieldset>
            <br>
        <?php endfor; ?>
        <input type="submit" value="GRABAR">
    </form>
</body>
</html>
