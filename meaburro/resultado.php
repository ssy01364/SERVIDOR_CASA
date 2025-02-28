<?php
session_start();

// Verificamos que las posiciones han sido enviadas
if (!isset($_POST['pareja1']) || !isset($_POST['pareja2'])) {
    die("Falta indicar las posiciones de pareja.");
}

// Convertimos a enteros y verificamos que estén en rango
$pos1 = intval($_POST['pareja1']);
$pos2 = intval($_POST['pareja2']);

if ($pos1 < 1 || $pos1 > 6 || $pos2 < 1 || $pos2 > 6 || $pos1 == $pos2) {
    die("Las posiciones deben estar entre 1 y 6 y no pueden ser iguales.");
}

// Convertimos las posiciones a índices del array
$indice1 = $pos1 - 1;
$indice2 = $pos2 - 1;

// Verificamos que la combinación de cartas existe en la sesión
if (!isset($_SESSION['combinacion'])) {
    die("No se encontró la combinación de cartas en la sesión.");
}
$combinacion = $_SESSION['combinacion'];

// Verificamos si las cartas son iguales (pareja válida)
if (isset($combinacion[$indice1]) && isset($combinacion[$indice2]) && $combinacion[$indice1] == $combinacion[$indice2]) {
    $resultado = "✅ Acierto";
    $puntosCambio = 1;
} else {
    $resultado = "❌ Fallo";
    $puntosCambio = -1;
}

// Conexión a la base de datos
$conexion = mysqli_connect("localhost", "root", "", "cartas") or die("Error de conexión a la base de datos.");

// Verificamos que el nombre del jugador existe en la sesión
if (!isset($_SESSION['nombre'])) {
    die("Error: El usuario no está identificado.");
}
$nombre = $_SESSION['nombre'];

// Obtener los puntos y los intentos actuales del jugador
$query = "SELECT puntos, extra FROM jugador WHERE nombre = '$nombre'";
$res = mysqli_query($conexion, $query);

// Verificamos si la consulta devuelve resultados
if (!$res || mysqli_num_rows($res) == 0) {
    die("Error: El jugador '$nombre' no existe en la base de datos.");
}
$fila = mysqli_fetch_assoc($res);

// Asignamos valores por defecto si los datos están vacíos
$intentos = isset($fila['extra']) ? $fila['extra'] + 1 : 1;
$nuevosPuntos = isset($fila['puntos']) ? $fila['puntos'] + $puntosCambio : $puntosCambio;

// Actualizamos los datos del jugador
$update = "UPDATE jugador SET puntos = $nuevosPuntos, extra = $intentos WHERE nombre = '$nombre'";
mysqli_query($conexion, $update) or die("Error al actualizar los datos del jugador.");

// Mostramos el resultado de la jugada
echo "<h2>Bienvenido, $nombre</h2>";
echo "<p>$resultado: posiciones $pos1 y $pos2 después de $intentos intentos.</p>";

// Mostramos la tabla de puntuaciones
$query = "SELECT nombre, puntos, extra FROM jugador";
$res = mysqli_query($conexion, $query);
echo "<h3>Tabla de puntos por jugador:</h3>";
echo "<table border='1' cellpadding='5'>";
echo "<tr><th>Nombre</th><th>Puntos</th><th>Intentos</th></tr>";
while ($row = mysqli_fetch_assoc($res)) {
    echo "<tr>";
    echo "<td>" . htmlspecialchars($row['nombre']) . "</td>";
    echo "<td>" . htmlspecialchars($row['puntos']) . "</td>";
    echo "<td>" . htmlspecialchars($row['extra']) . "</td>";
    echo "</tr>";
}
echo "</table>";

mysqli_close($conexion);
?>
