<?php
include 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fecha = $_POST['fecha'];
    $tipo_comida = $_POST['tipo_comida'];
    $id_usu = $_POST['id_usu'];
    
    if (isset($_POST['modificar'])) {
        // Datos a actualizar en CONTROL_GLUCOSA
        $deporte = $_POST['deporte'];
        $insulina_lenta = $_POST['insulina_lenta'];
        
        $query = "UPDATE CONTROL_GLUCOSA SET deporte=?, lenta=? WHERE fecha=? AND id_usu=?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ddsi", $deporte, $insulina_lenta, $fecha, $id_usu);
        $stmt->execute();
        
        // Datos a actualizar en COMIDA
        $glucosa_1h = $_POST['glucosa_1h'];
        $glucosa_2h = $_POST['glucosa_2h'];
        $raciones = $_POST['raciones'];
        $insulina_comida = $_POST['insulina_comida'];
        
        $query = "UPDATE COMIDA SET gl_1h=?, gl_2h=?, raciones=?, insulina=? WHERE fecha=? AND tipo_comida=? AND id_usu=?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("dddsssi", $glucosa_1h, $glucosa_2h, $raciones, $insulina_comida, $fecha, $tipo_comida, $id_usu);
        $stmt->execute();
    } elseif (isset($_POST['borrar'])) {
        // Borrar la comida y las relaciones dependientes
        $query = "DELETE FROM COMIDA WHERE fecha=? AND tipo_comida=? AND id_usu=?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssi", $fecha, $tipo_comida, $id_usu);
        $stmt->execute();
    }
    header("Location: index.php");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Modificar/Borrar Insulina</title>
</head>
<body>
    <form method="post">
        ID Usuario: <input type="number" name="id_usu" required><br>
        Fecha: <input type="date" name="fecha" required><br>
        Tipo de Comida: <select name="tipo_comida">
            <option value="Desayuno">Desayuno</option>
            <option value="Comida">Comida</option>
            <option value="Cena">Cena</option>
        </select><br>
        <h3>Modificar</h3>
        Deporte: <input type="number" name="deporte"><br>
        Insulina Lenta: <input type="number" step="0.1" name="insulina_lenta"><br>
        Glucosa 1h: <input type="number" step="0.1" name="glucosa_1h"><br>
        Glucosa 2h: <input type="number" step="0.1" name="glucosa_2h"><br>
        Raciones: <input type="number" step="0.1" name="raciones"><br>
        Insulina Comida: <input type="number" step="0.1" name="insulina_comida"><br>
        <input type="submit" name="modificar" value="Modificar">
        <br><br>
        <h3>🗑️ Borrar</h3>
        <input type="submit" name="borrar" value="Borrar">
    </form>
</body>
</html>
