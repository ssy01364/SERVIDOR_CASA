<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    die("Usuario no autenticado.");
}
$usuario = htmlspecialchars($_SESSION['usuario']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Opciones de Usuario</title>
  <!-- Vincular el CSS principal (login.css) -->
  <link rel="stylesheet" href="../css/login.css">
  <style>
    /* Fondo animado con un degradado de varios colores */
    body {
      margin: 0;
      padding: 0;
      background: linear-gradient(-45deg, #74ebd5, #ACB6E5, #FBC2EB, #FEE140);
      background-size: 400% 400%;
      animation: gradientBG 15s ease infinite;
      font-family: 'Poppins', sans-serif;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      min-height: 100vh;
      color: #fff;
      text-align: center;
      position: relative; /* Para ubicar el botón de logout */
    }

    @keyframes gradientBG {
      0% { background-position: 0% 50%; }
      50% { background-position: 100% 50%; }
      100% { background-position: 0% 50%; }
    }

    /* Botón de cerrar sesión */
    .logout-btn {
      position: absolute;
      top: 20px;
      right: 20px;
      background: #e74c3c;
      color: #fff;
      font-weight: bold;
      padding: 10px 20px;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      transition: background 0.3s, transform 0.2s;
      text-decoration: none;
      font-size: 0.95rem;
    }
    .logout-btn:hover {
      background: #c0392b;
      transform: scale(1.05);
    }
    .logout-btn:active {
      transform: scale(0.95);
    }

    /* Mensaje de bienvenida */
    .welcome-message {
      margin-top: 60px; /* Espacio para el botón logout */
      margin-bottom: 20px;
      font-size: 2rem;
      font-weight: 600;
      text-shadow: 2px 2px 6px rgba(0, 0, 0, 0.3);
      animation: fadeInDown 1s ease forwards;
    }
    @keyframes fadeInDown {
      0% {
        transform: translateY(-30px);
        opacity: 0;
      }
      100% {
        transform: translateY(0);
        opacity: 1;
      }
    }

    /* Contenedor semitransparente para las opciones */
    .options-container {
      background: rgba(255, 255, 255, 0.15);
      backdrop-filter: blur(10px);
      border-radius: 15px;
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
      width: 90%;
      max-width: 600px;
      padding: 2rem;
      animation: scaleUp 0.7s ease forwards;
      opacity: 0;
      transform: scale(0.8);
      margin-top: 20px;
    }
    @keyframes scaleUp {
      to {
        transform: scale(1);
        opacity: 1;
      }
    }

    .options-container h1 {
      font-size: 1.8rem;
      margin-bottom: 1.5rem;
      color: #fff;
      text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.3);
    }

    /* Formulario de opciones */
    .options-form {
      display: flex;
      flex-direction: column;
      gap: 1rem;
      align-items: center;
    }

    /* Botones de opciones */
    .option-btn {
      width: 80%;
      max-width: 300px;
      padding: 0.75rem;
      border: none;
      border-radius: 5px;
      font-size: 1rem;
      cursor: pointer;
      font-weight: bold;
      transition: background 0.3s, transform 0.2s;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      text-decoration: none;
      color: #fff;
    }
    .option-btn:hover {
      transform: scale(1.03);
    }

    /* Colores para cada botón */
    .add-food-btn {
      background: #f39c12;
    }
    .add-food-btn:hover {
      background: #e67e22;
    }

    .calendar-btn {
      background: #3498db;
    }
    .calendar-btn:hover {
      background: #2980b9;
    }

    .stats-btn {
      background: #9b59b6;
    }
    .stats-btn:hover {
      background: #8e44ad;
    }

    /* Adaptación a pantallas pequeñas */
    @media (max-width: 480px) {
      .welcome-message {
        font-size: 1.6rem;
      }
      .options-container h1 {
        font-size: 1.4rem;
      }
      .option-btn {
        font-size: 0.9rem;
      }
    }
  </style>
</head>
<body>
  <!-- Botón de Cerrar Sesión -->
  <button class="logout-btn" onclick="window.location.href='../index.php';">🔒 Cerrar sesión</button>

  <!-- Mensaje de bienvenida -->
  <div class="welcome-message">¡Bienvenido, <?php echo $usuario; ?>!</div>

  <!-- Contenedor con las opciones -->
  <div class="options-container">
    <h1>Selecciona una opción</h1>
    <form class="options-form" action="procesar.php" method="POST">
      <button type="submit" name="opcion" value="formulario.php" class="option-btn add-food-btn">🍽️ Añadir comida</button>
      <button type="submit" name="opcion" value="calendario.php" class="option-btn calendar-btn">📅 Ir al calendario</button>
      <button type="submit" name="opcion" value="estadisticas.php" class="option-btn stats-btn">📊 Ir a las estadísticas</button>
    </form>
  </div>
</body>
</html>
