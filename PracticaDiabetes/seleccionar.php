<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    exit("Acceso denegado.");
}
$usuario = htmlspecialchars($_SESSION['usuario']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menú Principal</title>
    <link rel="stylesheet" href="login.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(to right, #74ebd5, #acb6e5);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
            color: #fff;
            text-align: center;
        }

        .contenedor {
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(12px);
            padding: 2.5rem;
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
            max-width: 700px;
            width: 90%;
        }

        .bienvenida {
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 1rem;
            text-shadow: 2px 2px 8px rgba(0, 0, 0, 0.2);
        }

        .selecciona {
            font-size: 1.3rem;
            margin-bottom: 1.5rem;
            color: #eee;
        }

        .opciones-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            justify-content: center;
            margin-top: 15px;
        }

        .tarjeta-opcion {
            background: white;
            padding: 20px;
            border-radius: 12px;
            text-decoration: none;
            color: #333;
            font-weight: bold;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
            transition: transform 0.3s, box-shadow 0.3s;
            text-align: center;
            font-size: 1.1rem;
        }

        .tarjeta-opcion:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.25);
        }

        .icono {
            font-size: 2rem;
            display: block;
            margin-bottom: 10px;
        }

        .cerrar-sesion {
            margin-top: 20px;
            background: #ff6b6b;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1rem;
            transition: background 0.3s;
        }

        .cerrar-sesion:hover {
            background: #e74c3c;
        }

        @media (max-width: 600px) {
            .opciones-grid {
                grid-template-columns: repeat(1, 1fr);
            }
        }
    </style>
</head>
<body>

<div class="contenedor">
    <div class="bienvenida">¡Hola, <?php echo $usuario; ?>!</div>
    <div class="selecciona">Selecciona una opción:</div>

    <div class="opciones-grid">
        <a href="formulario.php" class="tarjeta-opcion">
            <span class="icono">🍽️</span> Añadir Comida
        </a>
        <a href="calendario.php" class="tarjeta-opcion">
            <span class="icono">📅</span> Ver Calendario
        </a>
        <a href="estadisticas.php" class="tarjeta-opcion">
            <span class="icono">📊</span> Estadísticas
        </a>
    </div>

    <button class="cerrar-sesion" onclick="window.location.href='index.php';">🔒 Cerrar Sesión</button>
</div>

</body>
</html>
