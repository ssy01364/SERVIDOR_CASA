<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Regulación de Diabetes</title>
  <style>
    /* -------------------
       ESTILOS GENERALES
    ------------------- */
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    body {
      /* Fondo con degradado en tonos morados */
      background: linear-gradient(135deg,rgba(255, 189, 102, 0.84),rgba(209, 83, 10, 0.76));
      min-height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      padding: 20px;
    }

    /* Contenedor principal que agrupa todo el formulario */
    .container-form {
      background-color: rgba(255, 255, 255, 0.2);
      backdrop-filter: blur(10px);
      border-radius: 15px;
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
      width: 100%;
      max-width: 650px;
      color: #fff;
      padding: 20px;
      animation: fadeIn 0.8s ease;
    }

    @keyframes fadeIn {
      from {
        opacity: 0;
        transform: translateY(10px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    /* Título principal */
    .container-form h1 {
      text-align: center;
      margin-bottom: 15px;
      font-size: 24px;
      color: #fff;
      text-shadow: 1px 1px 2px rgba(0,0,0,0.3);
    }

    /* Mensaje de error */
    .error-message {
      color: #ff4f4f;
      text-align: center;
      margin-bottom: 15px;
      font-weight: bold;
    }

    /* ------------------------
       SECCIONES DEL FORMULARIO
    ------------------------ */
    .form-section {
      background-color: rgba(255, 255, 255, 0.15);
      border-radius: 10px;
      padding: 15px;
      margin-bottom: 20px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.2);
    }

    .form-section h2 {
      display: flex;
      align-items: center;
      font-size: 18px;
      margin-bottom: 15px;
      color: #ffeb3b; /* Amarillo para resaltar */
      text-shadow: 1px 1px 2px rgba(0,0,0,0.3);
    }

    .form-section h2 span {
      margin-right: 8px; /* espacio para el ícono */
    }

    /* Grupos de inputs */
    .input-group {
      margin-bottom: 15px;
    }

    .input-group label {
      display: block;
      margin-bottom: 5px;
      font-weight: bold;
      color: #fff;
      font-size: 14px;
    }

    .input-group input,
    .input-group select {
      width: 100%;
      padding: 10px;
      border: none;
      border-radius: 50px;
      font-size: 15px;
      color: #333;
      background-color: #fff;
      outline: none;
      transition: box-shadow 0.2s, transform 0.2s;
    }

    .input-group input:focus,
    .input-group select:focus {
      box-shadow: 0 0 8px rgba(221, 134, 2, 0.9);
      transform: scale(1.02);
    }

    /* ------------------------
       OPCIONES DE COMIDA
    ------------------------ */
    .food-options {
      display: flex;
      justify-content: space-around;
      margin-top: 10px;
    }

    .btn-food {
      padding: 10px 20px;
      background-color: #ffeb3b; /* Amarillo de base */
      border: none;
      border-radius: 50px;
      color: #333;
      cursor: pointer;
      transition: background-color 0.3s, transform 0.2s, box-shadow 0.2s;
      font-weight: bold;
    }

    .btn-food:hover {
      background-color: #ffc107; /* Amarillo más oscuro */
      transform: scale(1.05);
      box-shadow: 0 4px 10px rgba(255, 96, 4, 0.88);
    }

    /* ------------------------
       SECCIONES HIPO/HIPER
    ------------------------ */
    .event-section {
      transition: max-height 0.5s ease-in-out, opacity 0.5s ease;
      overflow: hidden;
      opacity: 0;
      max-height: 0;
      background-color: rgba(255, 255, 255, 0.3);
      border-radius: 10px;
      padding: 15px;
      margin-top: 15px;
      color: #fff;
    }

    .event-section.active {
      opacity: 1;
      max-height: 500px;
    }

    /* ------------------------
       BOTONES FINALES
    ------------------------ */
    .button-container {
      display: flex;
      gap: 10px;
      margin-top: 20px;
      justify-content: space-between;
    }

    .btn-submit,
    .btn-choose {
      width: 48%;
      padding: 12px;
      border: none;
      border-radius: 5px;
      font-size: 15px;
      font-weight: bold;
      color: #fff;
      cursor: pointer;
      transition: background-color 0.3s, transform 0.2s, box-shadow 0.2s;
    }

    /* Botón Enviar (verde) */
    .btn-submit {
      background-color: #40b324;
    }

    .btn-submit:hover {
      background-color: #34a119;
      transform: scale(1.05);
      box-shadow: 0 4px 10px rgba(0,0,0,0.2);
    }

    /* Botón Menú (azul) */
    .btn-choose {
      background-color: #0076ff;
    }

    .btn-choose:hover {
      background-color: #0056cc;
      transform: scale(1.05);
      box-shadow: 0 4px 10px rgba(0,0,0,0.2);
    }
  </style>
</head>
<body>
  <div class="container-form">
    <?php
      // Mensajes de error si existen
      if (isset($_GET['error'])) {
        echo "<p class='error-message'>";
        switch ($_GET['error']) {
          case '1':
            echo "Error: Todos los campos obligatorios deben ser completados.";
            break;
          case 'hiper_existente':
            echo "Ya existe un registro de hiperglucemia para esa fecha y tipo de comida.";
            break;
          case 'hipo_existente':
            echo "Ya existe un registro de hipoglucemia para esa fecha y tipo de comida.";
            break;
          case 'comida_existente':
            echo "Ya existe un registro para esa comida en esa fecha.";
            break;
          default:
            echo "Error desconocido. Intente nuevamente.";
        }
        echo "</p>";
      }
    ?>
    <h1>📋Registro Datos</h1>
    <form action="submit.php" method="POST">
      <!-- Sección de Control de Glucosa -->
      <div class="form-section">
        <h2><span>🩸</span>Control de Glucosa</h2>
        <div class="input-group">
          <label for="fecha">Fecha:</label>
          <input type="date" id="fecha" name="fecha" required>
        </div>
        <div class="input-group">
          <label for="deporte">Minutos de Deporte:</label>
          <input type="number" id="deporte" name="deporte" required>
        </div>
        <div class="input-group">
          <label for="lenta">Insulina Lenta:</label>
          <input type="number" id="lenta" name="lenta" required>
        </div>
      </div>

      <!-- Sección de Comida -->
      <div class="form-section">
        <h2><span>🍽️</span>Registro de Comida</h2>
        <div class="input-group">
          <label for="tipo_comida">Tipo de Comida:</label>
          <div class="food-options">
            <button type="button" class="btn-food" data-value="Desayuno">Desayuno</button>
            <button type="button" class="btn-food" data-value="Comida">Comida</button>
            <button type="button" class="btn-food" data-value="Cena">Cena</button>
          </div>
          <!-- Campo hidden para almacenar el valor del tipo de comida -->
          <input type="hidden" id="tipo_comida" name="tipo_comida" required>
        </div>
        <div class="input-group">
          <label for="gl_1h">Glucosa 1h después:</label>
          <input type="number" id="gl_1h" name="gl_1h" required>
        </div>
        <div class="input-group">
          <label for="gl_2h">Glucosa 2h después:</label>
          <input type="number" id="gl_2h" name="gl_2h" required>
        </div>
        <div class="input-group">
          <label for="raciones">Raciones:</label>
          <input type="number" id="raciones" name="raciones" required>
        </div>
        <div class="input-group">
          <label for="insulina">Insulina:</label>
          <input type="number" id="insulina" name="insulina" required>
        </div>
      </div>

      <!-- Sección de Tipo de Evento (Hipo / Hiper) -->
      <div class="form-section">
        <h2><span>⚠️</span>Tipo de Evento</h2>
        <div class="input-group">
          <label for="evento">Seleccionar Tipo:</label>
          <select id="evento" name="evento">
            <option value="">Seleccione...</option>
            <option value="hipoglucemia">Hipoglucemia (Hipo)</option>
            <option value="hiperglucemia">Hiperglucemia (Hiper)</option>
          </select>
        </div>
      </div>

      <!-- Sección Hiperglucemia -->
      <div class="event-section" id="hiperglucemia">
        <h2>Hiperglucemia</h2>
        <div class="input-group">
          <label for="glucosa_hiper">Glucosa:</label>
          <input type="number" id="glucosa_hiper" name="glucosa_hiper">
        </div>
        <div class="input-group">
          <label for="hora_hiper">Hora:</label>
          <input type="time" id="hora_hiper" name="hora_hiper">
        </div>
        <div class="input-group">
          <label for="correccion">Corrección:</label>
          <input type="number" id="correccion" name="correccion">
        </div>
      </div>

      <!-- Sección Hipoglucemia -->
      <div class="event-section" id="hipoglucemia">
        <h2>Hipoglucemia</h2>
        <div class="input-group">
          <label for="glucosa_hipo">Glucosa:</label>
          <input type="number" id="glucosa_hipo" name="glucosa_hipo">
        </div>
        <div class="input-group">
          <label for="hora_hipo">Hora:</label>
          <input type="time" id="hora_hipo" name="hora_hipo">
        </div>
      </div>

      <!-- Botones de Enviar y Menú Principal -->
      <div class="button-container">
        <button type="submit" class="btn-submit">✅Enviar Datos</button>
        <button type="button" class="btn-choose" onclick="window.location.href='seleccionar.php'">🏠Menú Principal</button>
      </div>
    </form>
  </div>

  <script>
    // Botones de tipo de comida
    document.querySelectorAll('.btn-food').forEach(button => {
      button.addEventListener('click', () => {
        // Reestablecer color de todos los botones
        document.querySelectorAll('.btn-food').forEach(btn => btn.style.backgroundColor = '#ffeb3b');
        // Marcar el botón actual
        button.style.backgroundColor = '#ffc107';
        // Asignar valor al input hidden
        document.getElementById('tipo_comida').value = button.dataset.value;
      });
    });

    // Mostrar/ocultar secciones de eventos (Hipo/Hiper)
    document.getElementById('evento').addEventListener('change', function() {
      const selectedEvent = this.value;
      document.getElementById('hiperglucemia').classList.toggle('active', selectedEvent === 'hiperglucemia');
      document.getElementById('hipoglucemia').classList.toggle('active', selectedEvent === 'hipoglucemia');
    });
  </script>
</body>
</html>
