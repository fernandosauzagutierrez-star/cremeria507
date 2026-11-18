<?php
// Conexión corregida al nombre correcto de tu base de datos
$conexion = new mysqli("localhost", "root", "", "cremeria507");
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}
$conexion->set_charset("utf8mb4");

$exito = false;
$mensaje_estado = "";
$nombre_cliente = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = trim($_POST["nombre"] ?? "");
    $correo = trim($_POST["correo"] ?? "");
    $mensaje = trim($_POST["mensaje"] ?? "");

    if ($nombre == "" || $correo == "" || $mensaje == "") {
        $mensaje_estado = "Todos los campos son obligatorios.";
    } elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $mensaje_estado = "El correo electrónico no es válido.";
    } else {
        // Se corrige "cliente" por "contacto", que es el nombre real de tu tabla
        $sql = "INSERT INTO contacto (nombre, correo, mensaje) VALUES (?, ?, ?)";
        $stmt = $conexion->prepare($sql);
        
        if ($stmt) {
            $stmt->bind_param("sss", $nombre, $correo, $mensaje);
            if ($stmt->execute()) {
                $exito = true;
                $nombre_cliente = $nombre;
                $mensaje_estado = "¡Registro guardado correctamente!";
            } else {
                $mensaje_estado = "Error al guardar: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $mensaje_estado = "Error SQL: " . $conexion->error;
        }
    }
}
$conexion->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultado del Registro</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f5f0eb 0%, #e0d5c1 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .card {
            background: white;
            padding: 35px;
            border-radius: 16px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            text-align: center;
            max-width: 450px;
            width: 90%;
        }
        .icono {
            font-size: 48px;
            margin-bottom: 15px;
        }
        h2 {
            color: <?php echo $exito ? '#2e7d32' : '#c62828'; ?>;
            margin-top: 0;
        }
        p { color: #555; font-size: 15px; margin-bottom: 25px; }
        a {
            display: inline-block;
            background-color: #6f4e37;
            color: white;
            padding: 12px 25px;
            text-decoration: none;
            border-radius: 8px;
            font-weight: bold;
            transition: background 0.3s;
        }
        a:hover { background-color: #4a2e18; }
    </style>
</head>
<body>

<div class="card">
    <div class="icono"><?php echo $exito ? '🎉' : '⚠️'; ?></div>
    <h2><?php echo $mensaje_estado; ?></h2>
    <?php if ($exito): ?>
        <p>Bienvenido(a), <strong><?php echo htmlspecialchars($nombre_cliente, ENT_QUOTES, "UTF-8"); ?></strong>. Tu registro ha sido procesado exitosamente.</p>
    <?php endif; ?>
    <a href="index.html">← Volver al formulario</a>
</div>

</body>
</html>