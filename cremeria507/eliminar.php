<?php
// Corregido al nombre de tu base de datos: cremeria507
$conexion = new mysqli("localhost", "root", "", "cremeria507");
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}
$conexion->set_charset("utf8mb4");

$exito = false;
$mensaje_estado = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $correo = trim($_POST["correo"] ?? "");
    
    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $mensaje_estado = "El correo electrónico no es válido.";
    } else {
        // Se cambia "cliente" por "contacto", que es el nombre real de tu tabla
        $sql = "DELETE FROM contacto WHERE correo = ? LIMIT 1";
        $stmt = $conexion->prepare($sql);
        
        if ($stmt) {
            $stmt->bind_param("s", $correo);
            if ($stmt->execute()) {
                if ($stmt->affected_rows > 0) {
                    $exito = true;
                    $mensaje_estado = "¡Registro eliminado correctamente!";
                } else {
                    $mensaje_estado = "No se encontró ningún registro registrado con ese correo.";
                }
            } else {
                $mensaje_estado = "Error al eliminar: " . $stmt->error;
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
    <title>Resultado de la Eliminación</title>
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
            color: <?php echo $exito ? '#d9534f' : '#c62828'; ?>;
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
    <div class="icono"><?php echo $exito ? '🗑️' : '❌'; ?></div>
    <h2><?php echo $mensaje_estado; ?></h2>
    <p><?php echo $exito ? 'Los datos han sido removidos de la base de datos.' : 'Por favor verifica la información ingresada.'; ?></p>
    <a href="index.php">← Volver al formulario</a>
</div>

</body>
</html>