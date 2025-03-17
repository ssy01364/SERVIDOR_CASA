<?php

$servername = "localhost:3307";
$username = "root";
$password = "";
$dbname = "diabetesdb";

/*
$servername = "fdb1028.awardspace.net";
$username = "4597274_diabetesdb";
$password = "{YUJFb%s71%77/R2";
$dbname = "4597274_diabetesdb";
*/
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
?>
