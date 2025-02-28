<?php
    session_start();
    require_once 'login.php';
    $connection = new mysqli($hn, $un, $pw, $db);
    if ($connection->connect_error) die("Fatal Error");
    $query = "
    SELECT u.Codigo, u.Nombre, c.contactos AS contactos FROM usuarios u
    LEFT JOIN 
        (
            SELECT codusuario, COUNT(*) AS contactos FROM contactos 
            GROUP BY 
                codusuario
        ) c
    ON 
        u.Codigo = c.codusuario
    ";

    $result = $connection->query($query);
    if (!$result) die("Fatal Error");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        table {
            border: 1px solid black;
            
            
            text-align: left;
        }
        th, td {
            border: 1px solid black;
            padding: 8px;
        }
        /* .bar {
            background-color: blue;
            height: 20px;
        } */
        .circulo {
            background-color: red;
            height: 20px;
            width: 20px;
            border-radius: 50px;
            display: inline-block;
        }
    </style>
</head>
<body>
    <h1>AGENDA</h1>
    <h2>Hola <?php echo  $_SESSION['usu'];?></h2>
    <table>
        <tr>
            <th>Código usuario</th>
            <th>Nombre</th>
            <th>Número de contactos</th>
            <th>Gráfica</th>
        </tr>
        <?php
            $rows = $result->num_rows;
            for ($j = 0 ; $j < $rows ; ++$j)
                {
                    echo "<tr>";
                    $result->data_seek($j);
                    echo '<td>' .htmlspecialchars($result->fetch_assoc()['Codigo']) .'</td>'; 
                    $result->data_seek($j);
                    echo '<td>' .htmlspecialchars($result->fetch_assoc()['Nombre']) .'</td>';
                    $result->data_seek($j);
                    $numcontactos = htmlspecialchars($result->fetch_assoc()['contactos']);
                    if ($numcontactos == "") {
                        $numcontactos = 0;
                    }
                    echo '<td>' .$numcontactos .'</td><td>';
                    
                    for ($i = 0 ; $i < $numcontactos ; ++$i) {
                        echo "<div class='circulo'></div>";
                    }
                    echo '</td></tr>';


                
                }
        ?>
        
    </table>
    <a href="index.php">Volver a loguearse</a><br>
    <a href="inicio.php">Introducir más contactos para <?php echo  $_SESSION['usu'];?></a><br>
</body>
</html>