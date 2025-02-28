<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Diabetes</title>
    <link rel="stylesheet" href="../css/login.css">
    <style>
        body {
            background: linear-gradient(to right, #6a11cb, #2575fc);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 119vh;
            margin: 0;
            font-family: 'Arial', sans-serif;
        }

        .contenedor {
            width: 90%;
            max-width: 900px;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.2);
            color: white;
        }

        .titulo {
            text-align: center;
            font-size: 1.8rem;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .seccion {
            background: rgba(255, 255, 255, 0.2);
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 15px;
        }

        .seccion h2 {
            font-size: 1.4rem;
            margin-bottom: 10px;
            color: #ffd700;
        }

        .grupo {
            margin-bottom: 12px;
        }

        .grupo label {
            display: block;
            font-size: 0.9rem;
            margin-bottom: 5px;
        }

        .grupo input, .grupo select {
            width: 99%;
            padding: 10px;
            border-radius: 8px;
            border: none;
            background: rgba(255, 255, 255, 0.2);
            color: black;
            font-size: 1rem;
            outline: none;
        }

        .grupo input::placeholder {
            color: rgba(255, 255, 255, 0.6);
        }

        .opciones-comida {
            display: flex;
            justify-content: space-around;
            margin-top: 10px;
        }

        .btn-comida {
            padding: 10px 15px;
            background-color: #ffcc00;
            border: none;
            border-radius: 8px;
            color: black;
            font-weight: bold;
            cursor: pointer;
            transition: 0.3s ease;
        }

        .btn-comida:hover {
            background-color: #ff9900;
            transform: scale(1.05);
        }

        .botones {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }

        .btn {
            padding: 12px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1rem;
            transition: 0.3s ease;
            color: white;
        }

        .btn-enviar {
            background-color: #28a745;
        }

        .btn-enviar:hover {
            background-color: #218838;
            transform: scale(1.05);
        }

        .btn-menu {
            background-color: #007bff;
        }

        .btn-menu:hover {
            background-color: #0056b3;
            transform: scale(1.05);
        }
    </style>
</head>
<body>

<div class="contenedor">
    <div class="titulo">📋 Registro de Diabetes</div>
    <form action="submit.php" method="POST">
        
        <!-- Sección Control de Glucosa -->
        <div class="seccion">
            <h2>🩸 Control de Glucosa</h2>
            <div class="grupo">
                <label>Fecha:</label>
                <input type="date" name="fecha" required>
            </div>
            <div class="grupo">
                <label>Minutos de Deporte:</label>
                <input type="number" name="deporte" placeholder="Ej. 30">
            </div>
            <div class="grupo">
                <label>Insulina Lenta:</label>
                <input type="number" name="lenta" placeholder="Ej. 10">
            </div>
        </div>

        <!-- Sección Registro de Comida -->
        <div class="seccion">
            <h2>🍽️ Registro de Comida</h2>
            <div class="grupo">
                <label>Tipo de Comida:</label>
                <div class="opciones-comida">
                    <button type="button" class="btn-comida" data-value="Desayuno">Desayuno</button>
                    <button type="button" class="btn-comida" data-value="Comida">Comida</button>
                    <button type="button" class="btn-comida" data-value="Cena">Cena</button>
                </div>
                <input type="hidden" name="tipo_comida" id="tipo_comida">
            </div>
            <div class="grupo">
                <label>Glucosa 1h después:</label>
                <input type="number" name="glucosa_1h">
            </div>
            <div class="grupo">
                <label>Glucosa 2h después:</label>
                <input type="number" name="glucosa_2h">
            </div>
            <div class="grupo">
                <label>Raciones:</label>
                <input type="number" name="raciones">
            </div>
            <div class="grupo">
                <label>Insulina:</label>
                <input type="number" name="insulina">
            </div>
        </div>

        <!-- Sección Tipo de Evento -->
        <div class="seccion">
            <h2>⚠️ Tipo de Evento</h2>
            <div class="grupo">
                <label>Seleccionar Tipo:</label>
                <select name="evento">
                    <option value="">Seleccione...</option>
                    <option value="hipoglucemia">Hipoglucemia</option>
                    <option value="hiperglucemia">Hiperglucemia</option>
                </select>
            </div>
        </div>

        <!-- Botones de acción -->
        <div class="botones">
            <button type="submit" class="btn btn-enviar">✅ Enviar Datos</button>
            <button type="button" class="btn btn-menu" onclick="window.location.href='seleccionar.php'">📂 Menú Principal</button>
        </div>

    </form>
</div>

<script>
    // Selección de tipo de comida
    document.querySelectorAll('.btn-comida').forEach(boton => {
        boton.addEventListener('click', function() {
            document.querySelectorAll('.btn-comida').forEach(btn => btn.style.backgroundColor = '#ffcc00');
            this.style.backgroundColor = '#ff9900';
            document.getElementById('tipo_comida').value = this.dataset.value;
        });
    });
</script>

</body>
</html>
