<?php
session_start();

// Guardar el nombre si no está en la sesión
if (!isset($_SESSION['nombre'])) {
    $_SESSION['nombre'] = isset($_GET['nombre']) ? $_GET['nombre'] : 'Jugador';
}

// Si se quiere reiniciar el juego, borrar solo las variables del juego
/*if (isset($_GET['reiniciar'])) {
    unset($_SESSION['combinacion'], $_SESSION['cartas_levantadas'], $_SESSION['carta_mostrada']); //para resetear el juego
    header("Location: mostrarcartas.php");
    exit;
}*/
if (isset($_GET['reiniciar'])) {
    $_SESSION['combinacion'] = [1, 1, 2, 2, 3, 3]; // Generar nueva combinación
    shuffle($_SESSION['combinacion']); // Mezclar las cartas
    $_SESSION['cartas_levantadas'] = 0;//reinicia el contador
    $_SESSION['carta_mostrada'] = -1;//reinicia la carta mostrada

    header("Location: mostrarcartas.php");
    exit;
}


// Si no hay combinación, generarla
if (!isset($_SESSION['combinacion'])) {
    $_SESSION['combinacion'] = [1, 1, 2, 2, 3, 3];
    shuffle($_SESSION['combinacion']);//combinaciones aleatorias
    $_SESSION['cartas_levantadas'] = 0;
    $_SESSION['carta_mostrada'] = -1;
}

// Levantar carta
if (isset($_POST['levantar'])) {
    $_SESSION['carta_mostrada'] = intval($_POST['levantar']);
    $_SESSION['cartas_levantadas']++;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Juego de Cartas</title>
</head>
<body>
    <h2>Bienvenido, <?php echo htmlspecialchars($_SESSION['nombre']); ?></h2>
    <p>Cartas levantadas: <?php echo $_SESSION['cartas_levantadas']; ?></p>

    <!-- Mostrar cartas -->
    <table border="1">
        <tr>
        <?php

for ($i = 0; $i < 6; $i++) {
    echo "<td>";
    if ($_SESSION['carta_mostrada'] == $i) {
        // Obtener el número de la carta
        $numCarta = $_SESSION['combinacion'][$i];

        // Mapear los números de las cartas con los nombres de las imágenes
        $imagenesCartas = [
            1 => "copas_02.jpg",
            2 => "copas_03.jpg",
            3 => "copas_05.jpg"
        ];

        // Verificar si la carta tiene una imagen asociada
        if (isset($imagenesCartas[$numCarta])) { //isset() verifica si $numCarta tiene una imagen en el array.
            //Ejemplo:
            //Si numCarta = 2, el array tiene copas_03.jpg, por lo que el if será verdadero ✅.
            // Si numCarta = 5, el array no tiene una imagen asociada, entonces el if será falso ❌ y no se ejecutará el echo.
            echo '<img src="imagenes/' . $imagenesCartas[$numCarta] . '" alt="Carta ' . $numCarta . '" width="100" height="150">';
            //ejemplo: echo '<img src="imagenes/copas_03.jpg" alt="Carta 2" width="100" height="150">';
        } else {
            echo "Imagen no encontrada"; // En caso de que haya un número que no tenga imagen
        }
    } else {
        // Mostrar la carta oculta
        echo '<img src="imagenes/boca_abajo.jpg" alt="Carta oculta" width="100" height="150">';
    }
    echo "</td>";
}
?>


        </tr>
    </table>

    <!-- Formulario para levantar cartas -->
    <form method="post">
    <?php 
    for ($i = 0; $i < 6; $i++) { 
        echo '<button type="submit" name="levantar" value="' . $i . '">Levantar ' . ($i + 1) . '</button>';
    }
    ?>
</form>


    <hr><!-- Línea horizontal aquí -->

    <!-- Formulario para comprobar parejas -->
    <form method="post" action="resultado.php">
        <label>Posición 1 (1-6):</label>
        <input type="number" name="pareja1" min="1" max="6" required>
        <label>Posición 2 (1-6):</label>
        <input type="number" name="pareja2" min="1" max="6" required>
        <button type="submit">Comprobar</button>
    </form>

    <hr>

    <!-- Botón para reiniciar -->
    <form method="get" action="mostrarcartas.php">
    <input type="hidden" name="reiniciar" value="1"> <!-- Se envía al hacer clic en el botón, pero el usuario no lo ve en la página. -->
    <button type="submit">Reiniciar Juego</button>
</form>
    <!-- <a href="mostrarcartas.php?reiniciar=1"><button>Reiniciar Juego</button></a> -->
    <!-- Esta línea crea un botón de reinicio dentro de un enlace <a>, lo cual hace que, 
        cuando el usuario haga clic en el botón, se recargue la página y se active la opción de reiniciar el juego. -->

</body>
</html>
