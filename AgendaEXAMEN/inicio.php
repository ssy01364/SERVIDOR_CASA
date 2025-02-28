<?php
    session_start();
    if (isset($_POST['input'])) {
        if ($_POST['input'] == "INCREMENTAR" && $_SESSION['contador']<4) {
            $_SESSION['contador']++;
        } else if ($_SESSION['contador']>=4) {
            echo<<<_END
                <meta http-equiv="refresh" content="0;URL='agenda.php'" />
            _END;
        } else {
            echo<<<_END
                <meta http-equiv="refresh" content="0;URL='agenda.php'" />
            _END;
        }
    } else {
        $_SESSION['contador'] = 0;
    }
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <h1>AGENDA</h1>
    <div style="border: 4px double; width:40%;">
        <p>Hola <?php echo  $_SESSION['usu'];?>, cuantos contactos deseas grabar?</p>
        <p>Puedes grabar entre 1 y 5. Por cada pulsación en INCREMENTAR grabarás un usuario más.</p>
        <p>Cuando el número sea el deseado pulsa GRABAR</p>
    </div>
    <div>
        <?php 
            for ($i=0;$i<=$_SESSION['contador']; $i++) {
                echo "<img src='img/OIP$i.jfif' style='border: 4px double; width:5%;'>";
            }
        ?>
    </div>
    <form method="post" action="#">
    <input type="submit" value="INCREMENTAR" name="input">
    <input type="submit" value="GRABAR" name="input">
    </form>
</body>
</html>