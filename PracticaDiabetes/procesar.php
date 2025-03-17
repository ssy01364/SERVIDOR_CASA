<?php
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["opcion"])) {
    $destino = $_POST["opcion"];
    header("Location: $destino");
    exit();
}
?>