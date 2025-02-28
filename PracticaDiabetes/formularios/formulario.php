<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Regulación de Diabetes</title>
  <link rel="stylesheet" href="../css/login.css">
  <style>
    /* Fondo degradado y animación de entrada */
    body {
      margin: 0;
      padding: 0;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: linear-gradient(135deg, #2980b9, #8e44ad);
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
      animation: fadeIn 1s ease-in-out;
    }
    @keyframes fadeIn {
      from { opacity: 0; }
      to { opacity: 1; }
    }
    /* Contenedor principal */
    .container-form {
      background: rgba(255, 255, 255, 0.15);
      backdrop-filter: blur(10px);
      padding: 2rem;
      border-radius: 15px;
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
      width: 100%;
      max-width: 600px;
      color: #fff;
      margin: 20px;
      animation: slideUp 0.8s ease-out;
    }
    @keyframes slideUp {
      from { transform: translateY(50px); opacity: 0; }
      to { transform: translateY(0); opacity: 1; }
    }
    /* Título principal */
    .container-form h1 {
      text-align: center;
      margin-bottom: 20px;
      font-size: 28px;
      color: #f1c40f;
      text-shadow: 1px 1px 2px rgba(0,0,0,0.3);
    }
    /* Mensaje de error */
    .error-message {
      color: #e74c3c;
      text-align: center;
      margin-bottom: 10px;
      font-weight: bold;
    }
    /* Secciones del formulario */
    .form-section {
      margin-bottom: 30px;
      border-bottom: 1px solid rgba(255, 255, 255, 0.2);
      padding-bottom: 20px;
    }
    .form-section h2 {
      font-size: 20px;
      margin-bottom: 15px;
      color: #f1c40f;
      border-left: 4px solid #f1c40f;
      padding-left: 10px;
    }
    /* Grupos de inputs */
    .input-group {
      margin-bottom: 15px;
    }
    .input-group label {
      display: block;
      font-size: 15px;
      margin-bottom: 5px;
    }
    .input-group input,
    .input-group select {
      width: 100%;
      padding: 10px;
      border: none;
      border-radius: 5px;
      font-size: 16px;
      background: rgba(255, 255, 255, 0.9);
      color: #333;
      outline: none;
      transition: transform 0.2s, box-shadow 0.2s;
    }
    .input-group input:focus,
    .input-group select:focus {
      box-shadow: 0 0 8px rgba(241, 196, 15, 0.8);
      transform: scale(1.02);
    }
    /* Opciones de comida (Desayuno, Comida, Cena) */
    .food-options {
      display: flex;
      justify-content: space-around;
      margin-top: 10px;
    }
    .btn-food {
      padding: 10px 20px;
      background-color: #f1c40f;
      border: none;
      border-radius: 5px;
      color: #333;
      cursor: pointer;
      transition: background-color 0.3s, transform 0.2s, box-shadow 0.2s;
    }
    .btn-food:hover {
      background-color: #e67e22;
      transform: scale(1.05);
      box-shadow: 0 4px 10px rgba(0,0,0,0.2);
    }
    /* Sección de eventos (Hipoglucemia / Hiperglucemia) */
    .event-section {
      transition: max-height 0.5s ease-in-out, opacity 0.5s ease;
      overflow: hidden;
      opacity: 0;
      max-height: 0;
      background: rgba(255, 255, 255, 0.85);
      border-radius: 5px;
      padding: 15px;
      margin-top: 15px;
      color: #333;
    }
    .event-section.active {
      opacity: 1;
      max-height: 500px;
    }
    /* Contenedor de botones */
    .button-container {
      display: flex;
      gap: 10px;
      margin-top: 20px;
    }
    .btn-submit,
    .btn-choose {
      width: 48%;
      padding: 10px;
      border: none;
      border-radius: 5px;
      font-size: 16px;
      font-weight: bold;
      color: #fff;
      cursor: pointer;
      transition: background 0.3s, transform 0.2s, box-shadow 0.2s;
    }
    .btn-submit {
      background: #f1c40f;
    }
    .btn-submit:hover {
      background: #e67e22;
      transform: scale(1.05);
      box-shadow: 0 4px 10px rgba(0,0,0,0.2);
    }
    .btn-choose {
      background: #3498db;
    }
    .btn-choose:hover {
      background: #2980b9;
      transform: scale(1.05);
      box-shadow: 0 4px 10px rgba(0,0,0,0.2);
    }
  </style>
</head>
<body>
  <div class="container-form">
    <?php
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
    <h1>Registro de Datos para la Diabetes</h1>
    <form action="submit.php" method="POST">
      <!-- Sección de Control de Glucosa -->
      <div class="form-section">
        <h2>Control de Glucosa</h2>
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
        <h2>Registro de Comida</h2>
        <div class="input-group">
          <label for="tipo_comida">Tipo de Comida:</label>
          <div class="food-options">
            <button type="button" class="btn-food" data-value="Desayuno">Desayuno</button>
            <button type="button" class="btn-food" data-value="Comida">Comida</button>
            <button type="button" class="btn-food" data-value="Cena">Cena</button>
          </div>
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
        <h2>Tipo de Evento</h2>
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
        <button type="submit" class="btn-submit">📤 Enviar Datos</button>
        <button type="button" class="btn-choose" onclick="window.location.href='seleccionar.php'">📋 Menú Principal</button>
      </div>
    </form>
  </div>

  <script>
    // Selección de tipo de comida
    document.querySelectorAll('.btn-food').forEach(button => {
      button.addEventListener('click', () => {
        document.querySelectorAll('.btn-food').forEach(btn => btn.style.backgroundColor = '#f1c40f');
        button.style.backgroundColor = '#e67e22';
        document.getElementById('tipo_comida').value = button.dataset.value;
      });
    });

    // Mostrar/ocultar secciones de eventos según selección
    document.getElementById('evento').addEventListener('change', function() {
      const selectedEvent = this.value;
      document.getElementById('hiperglucemia').classList.toggle('active', selectedEvent === 'hiperglucemia');
      document.getElementById('hipoglucemia').classList.toggle('active', selectedEvent === 'hipoglucemia');
    });
  </script>
</body>
</html>
