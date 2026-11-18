<?php
// Configuración y conexión rápida a XAMPP
$servidor = "localhost";
$usuario = "root";
$password = "";
$base_datos = "cremeria507";

$mensaje_exito = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['nombre'] ?? '';
    $correo = $_POST['correo'] ?? '';
    $mensaje = $_POST['mensaje'] ?? '';

    // Solo intentamos guardar si los campos no están vacíos
    if (!empty($nombre) && !empty($correo) && !empty($mensaje)) {
        $conexion = new mysqli($servidor, $usuario, $password, $base_datos);

        if ($conexion->connect_error) {
            $mensaje_exito = "Error de conexión: " . $conexion->connect_error;
        } else {
            // Usamos consultas preparadas para evitar errores de comillas o sintaxis en SQL
            $stmt = $conexion->prepare("INSERT INTO contacto (nombre, correo, mensaje) VALUES (?, ?, ?)");
            
            if ($stmt) {
                $stmt->bind_param("sss", $nombre, $correo, $mensaje);
                if ($stmt->execute()) {
                    $mensaje_exito = "¡Mensaje registrado en la base de datos con éxito!";
                } else {
                    $mensaje_exito = "Error al ejecutar: " . $stmt->error;
                }
                $stmt->close();
            } else {
                $mensaje_exito = "Error en la consulta: " . $conexion->error;
            }
            $conexion->close();
        }
    } else {
        $mensaje_exito = "Por favor, llena todos los campos.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cafetería - Inicio</title>

    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            background-color: #fdfbf7;
            color: #333;
        }

        header {
            background: linear-gradient(135deg, #4a2e18, #6f4e37);
            color: white;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        .encabezado {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 20px;
            max-width: 700px;
            margin: 0 auto;
            text-align: left;
        }

        .encabezado img {
            width: 75px;
            height: 75px;
            object-fit: cover;
            border-radius: 50%;
            border: 3px solid #d4a373;
        }

        .encabezado h1 {
            margin: 0;
            font-size: 30px;
            letter-spacing: 1px;
        }

        .encabezado p {
            margin: 5px 0 0;
            font-size: 14px;
            color: #f4d35e;
        }

        nav {
            background-color: #3e2723;
            padding: 12px;
            text-align: center;
        }

        nav a {
            margin: 0 15px;
            text-decoration: none;
            color: #f5efe6;
            font-weight: 600;
            transition: color 0.3s;
        }

        nav a:hover {
            color: #d4a373;
        }

        section {
            background-color: white;
            max-width: 650px;
            margin: 30px auto;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        }

        h2 {
            color: #4a2e18;
            border-bottom: 2px solid #f0ead6;
            padding-bottom: 8px;
            margin-top: 0;
        }

        table {
            width: 100%;
            margin: 15px 0;
            border-collapse: collapse;
        }

        th {
            background-color: #6f4e37;
            color: white;
            padding: 12px;
            text-align: left;
        }

        td {
            border-bottom: 1px solid #eee;
            padding: 12px;
            color: #555;
        }

        tr:hover {
            background-color: #faf9f6;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            display: block;
            margin-bottom: 6px;
            font-weight: 600;
            color: #4a2e18;
        }

        input, textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 6px;
            box-sizing: border-box;
            font-size: 14px;
            transition: border-color 0.3s;
        }

        input:focus, textarea:focus {
            outline: none;
            border-color: #6f4e37;
            box-shadow: 0 0 5px rgba(111, 78, 55, 0.2);
        }

        textarea {
            height: 100px;
            resize: vertical;
        }

        button {
            background-color: #6f4e37;
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            width: 100%;
            transition: background 0.3s;
        }

        button:hover {
            background-color: #4a2e18;
        }

        footer {
            background-color: #2c1e12;
            color: #ccc;
            text-align: center;
            padding: 20px;
            margin-top: 40px;
            font-size: 14px;
        }

        .aviso {
            background-color: #e8f5e9;
            color: #2e7d32;
            padding: 12px;
            border-radius: 6px;
            text-align: center;
            font-weight: bold;
            margin-bottom: 15px;
            border: 1px solid #c8e6c9;
        }
    </style>
</head>

<body>

    <header>
        <div class="encabezado">
            <img src="cremeria.jpg" alt="Logo de la Cremeria">
            <div>
                <h1>Cafetería Artesanal</h1>
                <p>El mejor sabor y energía para tu día</p>
            </div>
        </div>
    </header>

    <nav>
        <a href="#">Inicio</a>
        <a href="#">Menú</a>
        <a href="#">Contacto</a>
    </nav>

    <section>
        <h2>Nuestro Menú Destacado</h2>
        <table>
            <tr>
                <th>Producto</th>
                <th>Precio</th>
            </tr>
            <tr>
                <td>Hamburguesa Especial</td>
                <td>$45</td>
            </tr>
            <tr>
                <td>Jugo de huevo</td>
                <td>$20</td>
            </tr>
            <tr>
                <td>Sándwich Gourmet</td>
                <td>$35</td>
            </tr>
        </table>
    </section>

    <section>
        <h2>Ponte en Contacto</h2>

        <!-- Mensaje de confirmación dinámico -->
        <?php if(!empty($mensaje_exito)) { echo "<div class='aviso'>$mensaje_exito</div>"; } ?>

        <form id="formulario" action="index.php" method="POST">
            <div class="form-group">
                <label>Nombre:</label>
                <input type="text" id="nombre" name="nombre" placeholder="Escribe tu nombre completo">
            </div>

            <div class="form-group">
                <label>Correo Electrónico:</label>
                <input type="email" id="correo" name="correo" placeholder="ejemplo@correo.com">
            </div>

            <div class="form-group">
                <label>Mensaje:</label>
                <textarea id="mensajeTexto" name="mensaje" placeholder="Escribe tus comentarios o dudas..."></textarea>
            </div>

            <button type="submit">Enviar Mensaje</button>
        </form>
    </section>

    <footer>
        Cafetería &copy; 2026 - 026-Sauza Gutierrez Fernando - alias CHAMOY      
    </footer>

    <script>
        document.getElementById("formulario").addEventListener("submit", function(event) {
            let nombre = document.getElementById("nombre").value.trim();
            let correo = document.getElementById("correo").value.trim();
            let mensaje = document.getElementById("mensajeTexto").value.trim();

            if (nombre === "" || correo === "" || mensaje === "") {
                event.preventDefault();
                alert("Por favor, completa todos los campos antes de enviar.");
            }
        });
    </script>

</body>
</html>