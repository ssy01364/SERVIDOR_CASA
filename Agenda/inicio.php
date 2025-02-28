<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit();
}

// Lista de archivos de imagen (emojis)
$emojiList = array("OIP0.jfif", "OIP1.jfif", "OIP2.jfif", "OIP3.jfif", "OIP4.jfif");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['incrementar'])) {
        $randomEmoji = $emojiList[array_rand($emojiList)];
        $_SESSION['emojis'][] = $randomEmoji;
        if (count($_SESSION['emojis']) >= 5) {
            header("Location: agenda.php");
            exit();
        }
    } elseif (isset($_POST['grabar'])) {
        if (count($_SESSION['emojis']) < 1) {
            $randomEmoji = $emojiList[array_rand($emojiList)];
            $_SESSION['emojis'][] = $randomEmoji;
        }
        header("Location: agenda.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Inicio - Agenda</title>
</head>
<body>
    <h2>Bienvenido, <?php echo $_SESSION['usuario']; ?></h2>
    <h3>Selecciona el número de contactos a grabar</h3>
    <div>
        <?php
        if (isset($_SESSION['emojis']) && count($_SESSION['emojis']) > 0) {
            foreach ($_SESSION['emojis'] as $emoji) {
                echo "<img src='$emoji' alt='emoji' style='width:50px;height:50px; margin:5px;'>";
            }
        }
        ?>
    </div>
    <form method="POST" action="">
        <input type="submit" name="incrementar" value="INCREMENTAR">
        <input type="submit" name="grabar" value="GRABAR">
    </form>
    <p>Total de contactos a grabar: <?php echo count($_SESSION['emojis']); ?></p>
</body>
</html>
