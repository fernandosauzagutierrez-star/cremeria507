<?php
// Configuración y conexión a XAMPP
$servidor = "localhost";
$usuario = "root";
$password = "";
$base_datos = "cremeria507";

$mensaje_exito = "";
$mensaje_error = "";
$mensaje_producto = "";
$mensaje_eliminar_prod = "";

// Determinar la pestaña activa
$tab = $_GET['tab'] ?? 'catalogo';

// 1. REGISTRAR PEDIDO DESDE EL CLIENTE
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['btn_hacer_pedido'])) {
    $tab = 'catalogo';
    $nombre = $_POST['nombre'] ?? '';
    $correo = $_POST['correo'] ?? '';
    $producto_interes = $_POST['producto_interes'] ?? 'Ninguno';
    $mensaje = $_POST['mensaje'] ?? '';

    if (!empty($nombre) && !empty($correo) && !empty($producto_interes) && !empty($mensaje)) {
        $conexion = new mysqli($servidor, $usuario, $password, $base_datos);
        if (!$conexion->connect_error) {
            $stmt = $conexion->prepare("INSERT INTO contacto (nombre, correo, producto_interes, mensaje) VALUES (?, ?, ?, ?)");
            if ($stmt) {
                $stmt->bind_param("ssss", $nombre, $correo, $producto_interes, $mensaje);
                if ($stmt->execute()) {
                    $mensaje_exito = "¡Pedido registrado con éxito! Te esperamos en mostrador.";
                }
                $stmt->close();
            }
            $conexion->close();
        }
    } else {
        $mensaje_error = "Por favor completa todos los campos (incluyendo los productos seleccionados).";
    }
}

// 2. AGREGAR PRODUCTO AL MENÚ (ADMIN)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['btn_guardar_producto'])) {
    $tab = 'admin';
    $prod_nombre = $_POST['prod_nombre'] ?? '';
    $prod_precio = $_POST['prod_precio'] ?? '';
    $prod_categoria = $_POST['prod_categoria'] ?? '';

    if (!empty($prod_nombre) && !empty($prod_precio)) {
        $conexion = new mysqli($servidor, $usuario, $password, $base_datos);
        if (!$conexion->connect_error) {
            $stmt = $conexion->prepare("INSERT INTO productos (nombre, precio, categoria) VALUES (?, ?, ?)");
            if ($stmt) {
                $stmt->bind_param("sss", $prod_nombre, $prod_precio, $prod_categoria);
                if ($stmt->execute()) {
                    $mensaje_producto = "¡Producto agregado al aparador con éxito!";
                }
                $stmt->close();
            }
            $conexion->close();
        }
    }
}

// 3. ELIMINAR PRODUCTO DEL MENÚ (ADMIN)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['btn_eliminar_producto'])) {
    $tab = 'admin';
    $prod_id = $_POST['prod_id'] ?? '';

    if (!empty($prod_id)) {
        $conexion = new mysqli($servidor, $usuario, $password, $base_datos);
        if (!$conexion->connect_error) {
            $stmt = $conexion->prepare("DELETE FROM productos WHERE id = ?");
            if ($stmt) {
                $stmt->bind_param("i", $prod_id);
                $stmt->execute();
                $stmt->close();
            }
            $conexion->close();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cremería Chapala 🐷 | Calidad y Tradición</title>
    <style>
        :root {
            --primary: #FF6600;         
            --primary-dark: #D95300;    
            --secondary: #0A192F;       
            --accent: #FFD700;          
            --bg-main: #F4F7FB;         
            --card-bg: #FFFFFF;
            --text-dark: #1E293B;
            --text-muted: #64748B;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; font-family: Arial, Helvetica, sans-serif; }
        body { background-color: var(--bg-main); color: var(--text-dark); display: flex; flex-direction: column; min-height: 100vh; }

        h1, h2, .section-title, .brand-box h1 {
            font-family: 'Arial Rounded MT Bold', Arial, sans-serif;
        }

        .prod-price {
            font-family: 'Arial Black', Arial, sans-serif;
            letter-spacing: -0.5px;
        }

        /* HEADER COMERCIAL MODERNO */
        header {
            background: linear-gradient(135deg, var(--secondary), #172A45);
            color: white;
            padding: 22px 20px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.12);
            border-bottom: 4px solid var(--primary);
        }
        .header-content {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 20px;
        }
        .brand-box {
            display: flex;
            align-items: center;
            gap: 16px;
        }
        .brand-box img {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid var(--primary);
            box-shadow: 0 4px 12px rgba(255,102,0,0.3);
        }
        .brand-box h1 { font-size: 26px; font-weight: 800; letter-spacing: 0.5px; color: #FFFFFF; }
        .brand-box p { font-size: 13px; color: #94A3B8; margin-top: 3px; max-width: 450px; line-height: 1.3; }

        /* NAVEGACIÓN TIPO PESTAÑAS */
        .nav-tabs {
            display: flex;
            gap: 8px;
            background: rgba(255,255,255,0.08);
            padding: 6px;
            border-radius: 12px;
            border: 1px solid rgba(255,255,255,0.1);
        }
        .tab-link {
            color: #E2E8F0;
            background: transparent;
            padding: 10px 18px;
            border-radius: 8px;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.25s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .tab-link:hover { background: rgba(255,255,255,0.15); color: #FFF; }
        .tab-link.active {
            background: var(--primary);
            color: white;
            box-shadow: 0 4px 12px rgba(255,102,0,0.4);
        }

        /* CONTENEDOR GENERAL MÁS AMPLIO */
        .main-wrapper {
            max-width: 1200px;
            margin: 35px auto;
            padding: 0 20px;
            width: 100%;
            flex: 1;
        }

        .tab-view { display: none; }
        .tab-view.active { display: block; }

        /* GRID DE APARADOR (CATÁLOGO Y FORMULARIO MÁS ANCHO) */
        .catalog-layout {
            display: grid;
            grid-template-columns: 1.3fr 1.1fr;
            gap: 30px;
            align-items: start;
        }
        @media(max-width: 900px) {
            .catalog-layout { grid-template-columns: 1fr; }
        }

        .section-title {
            font-size: 22px;
            font-weight: 700;
            color: var(--secondary);
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            border-bottom: 2px solid #E2E8F0;
            padding-bottom: 8px;
        }

        .grid-cards {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(210px, 1fr));
            gap: 16px;
        }
        .product-card {
            background: var(--card-bg);
            border-radius: 12px;
            padding: 16px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            border: 1px solid #E2E8F0;
            border-top: 5px solid var(--primary);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            cursor: pointer;
        }
        .product-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
            border-top-color: var(--secondary);
            border-color: #CBD5E1;
        }
        .badge-cat {
            background: #FFF7ED;
            color: var(--primary);
            font-size: 11px;
            font-weight: 700;
            padding: 4px 10px;
            border-radius: 6px;
            align-self: flex-start;
            margin-bottom: 8px;
            text-transform: uppercase;
            border: 1px solid #FFEDD5;
        }
        .prod-name { font-size: 16px; font-weight: 700; color: var(--secondary); margin-bottom: 4px; }
        .prod-price { font-size: 18px; color: #16A34A; margin-bottom: 10px; }
        .click-hint { font-size: 11px; color: var(--text-muted); font-weight: bold; background: #F8FAFC; padding: 6px; border-radius: 6px; text-align: center; border: 1px dashed #CBD5E1; }

        /* TARJETA DE FORMULARIO MÁS GRANDE Y CÓMODA */
        .card-form {
            background: var(--card-bg);
            border-radius: 14px;
            padding: 30px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.07);
            border: 1px solid #E2E8F0;
        }
        .form-group { margin-bottom: 18px; }
        label { display: block; font-weight: 700; font-size: 13px; color: var(--text-muted); margin-bottom: 6px; text-transform: uppercase; letter-spacing: 0.3px; }
        input, select, textarea {
            width: 100%;
            padding: 14px;
            border: 1px solid #CBD5E1;
            border-radius: 8px;
            font-size: 15px;
            background: #F8FAFC;
            color: var(--text-dark);
        }
        input:focus, select:focus, textarea:focus {
            outline: none;
            border-color: var(--primary);
            background: #FFF;
            box-shadow: 0 0 0 3px rgba(255,102,0,0.15);
        }
        textarea { height: 100px; resize: vertical; }

        /* CONTENEDOR DE LA LISTA DE PRODUCTOS SELECCIONADOS (CARRITO VISUAL) */
        .cart-box {
            background: #FFF7ED;
            border: 2px dashed #FDBA74;
            border-radius: 10px;
            padding: 12px;
            min-height: 120px;
            max-height: 180px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        .cart-item {
            background: #FFFFFF;
            padding: 10px 12px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border: 1px solid #FFEDD5;
            box-shadow: 0 2px 5px rgba(0,0,0,0.03);
            font-size: 14px;
            font-weight: bold;
            color: var(--secondary);
        }
        .btn-remove-item {
            background: #EF4444;
            color: white;
            border: none;
            width: 26px;
            height: 26px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: bold;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.2s;
        }
        .btn-remove-item:hover { background: #DC2626; }
        .empty-cart-text {
            color: #9A3412;
            font-size: 13px;
            text-align: center;
            margin: auto;
            font-style: italic;
        }

        .cart-actions-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 8px;
        }
        .btn-clear-all {
            background: transparent;
            color: #EF4444;
            border: none;
            font-size: 12px;
            font-weight: bold;
            cursor: pointer;
            text-decoration: underline;
        }
        .btn-clear-all:hover { color: #DC2626; }

        .btn-main {
            background: var(--primary);
            color: white;
            border: none;
            padding: 14px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            width: 100%;
            transition: background 0.2s, transform 0.1s;
            box-shadow: 0 4px 12px rgba(255,102,0,0.3);
            margin-top: 5px;
        }
        .btn-main:hover { background: var(--primary-dark); }
        .btn-main:active { transform: scale(0.98); }

        /* TABLAS Y BUZÓN */
        .table-box {
            background: var(--card-bg);
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            border: 1px solid #E2E8F0;
        }
        table { width: 100%; border-collapse: collapse; text-align: left; }
        th { background: var(--secondary); color: white; padding: 14px; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px; }
        td { padding: 14px; border-bottom: 1px solid #F1F5F9; font-size: 14px; color: var(--text-dark); }
        tr:hover { background: #F8FAFC; }

        /* ALERTAS */
        .alert-ok { background: #DCFCE7; color: #15803D; padding: 12px; border-radius: 8px; font-weight: 600; margin-bottom: 15px; text-align: center; border: 1px solid #BBF7D0; }
        .alert-err { background: #FEE2E2; color: #B91C1C; padding: 12px; border-radius: 8px; font-weight: 600; margin-bottom: 15px; text-align: center; border: 1px solid #FECACA; }

        /* FOOTER */
        footer {
            background: var(--secondary);
            color: #94A3B8;
            text-align: center;
            padding: 20px;
            font-size: 13px;
            margin-top: auto;
            letter-spacing: 0.5px;
            border-top: 3px solid var(--primary);
        }
    </style>
</head>
<body>

    <header>
        <div class="header-content">
            <div class="brand-box">
                <img src="cremeria.jpg" alt="Logo Cremería">
                <div>
                    <h1>Cremería Chapala 🐷</h1>
                    <p>Donde la crema y los quesos son los más ricos de las 3B: Bueno, Barato y Bonito, como todos sus productos.</p>
                </div>
            </div>
            <nav class="nav-tabs">
                <a href="index.php?tab=catalogo" class="tab-link <?php echo ($tab == 'catalogo') ? 'active' : ''; ?>">🛒 Aparador y Pedidos</a>
                <a href="index.php?tab=buzon" class="tab-link <?php echo ($tab == 'buzon') ? 'active' : ''; ?>">📋 Buzón</a>
                <a href="index.php?tab=admin" class="tab-link <?php echo ($tab == 'admin') ? 'active' : ''; ?>">⚙️ Administrar</a>
            </nav>
        </div>
    </header>

    <div class="main-wrapper">

        <!-- 1. VISTA DE CATÁLOGO Y PEDIDOS -->
        <div id="tab-catalogo" class="tab-view <?php echo ($tab == 'catalogo') ? 'active' : ''; ?>">
            <div class="catalog-layout">
                <div>
                    <h2 class="section-title">🧀 Nuestros Productos Frescos</h2>
                    <p style="font-size: 13px; color: var(--text-muted); margin-bottom: 15px;">Haz clic en los productos para agregarlos a tu lista de pedido.</p>
                    <div class="grid-cards">
                        <?php
                        $conexion_cat = new mysqli($servidor, $usuario, $password, $base_datos);
                        if (!$conexion_cat->connect_error) {
                            $res = $conexion_cat->query("SELECT nombre, precio, categoria FROM productos");
                            if ($res && $res->num_rows > 0) {
                                while($p = $res->fetch_assoc()) {
                                    $nombre_prod = htmlspecialchars($p['nombre'], ENT_QUOTES);
                                    $precio_prod = number_format($p['precio'], 2);
                                    $texto_seleccion = $nombre_prod . " - $" . $precio_prod;
                                    
                                    echo '<div class="product-card" onclick="agregarProducto(\''.$texto_seleccion.'\')">';
                                    echo '<div>';
                                    echo '<span class="badge-cat">'.htmlspecialchars($p['categoria'] ?: 'Especialidad').'</span>';
                                    echo '<div class="prod-name">'.$nombre_prod.'</div>';
                                    echo '<div class="prod-price">$'.$precio_prod.'</div>';
                                    echo '</div>';
                                    echo '<div class="click-hint">➕ Agregar al pedido</div>';
                                    echo '</div>';
                                }
                            } else {
                                echo '<p style="color:#777; grid-column:1/-1;">No hay productos registrados todavía.</p>';
                            }
                            $conexion_cat->close();
                        }
                        ?>
                    </div>
                </div>

                <div>
                    <div class="card-form">
                        <h2 class="section-title" style="margin-top:0; font-size:18px;">🛒 Hacer un Pedido</h2>
                        <?php 
                        if(!empty($mensaje_exito)) echo "<div class='alert-ok'>$mensaje_exito</div>";
                        if(!empty($mensaje_error)) echo "<div class='alert-err'>$mensaje_error</div>";
                        ?>
                        <form action="index.php?tab=catalogo" method="POST" onsubmit="return prepararEnvio();">
                            <div class="form-group">
                                <label>Tu Nombre Completo</label>
                                <input type="text" name="nombre" placeholder="Ej. Carlos Mendoza" required>
                            </div>
                            <div class="form-group">
                                <label>Correo Electrónico</label>
                                <input type="email" name="correo" placeholder="correo@ejemplo.com" required>
                            </div>
                            <div class="form-group">
                                <label>Productos Seleccionados</label>
                                <!-- Contenedor visual dinámico con opción de borrar individualmente -->
                                <div id="cart-container" class="cart-box">
                                    <div class="empty-cart-text" id="empty-msg">Haz clic en los productos de la izquierda para agregarlos aquí...</div>
                                </div>
                                <div class="cart-actions-bar">
                                    <span style="font-size: 11px; color: var(--text-muted); font-weight: bold;" id="counter-label">0 productos seleccionados</span>
                                    <button type="button" class="btn-clear-all" onclick="vaciarCarrito()">Vaciar todo ✕</button>
                                </div>
                                <!-- Input oculto que se envía por POST a PHP con la lista completa -->
                                <input type="hidden" id="producto_interes" name="producto_interes" required>
                            </div>
                            <div class="form-group">
                                <label>Instrucciones (gramos, tipo de corte, etc.)</label>
                                <textarea name="mensaje" placeholder="Ej. Medio kilo fresco bien envuelto..." required></textarea>
                            </div>
                            <button type="submit" name="btn_hacer_pedido" class="btn-main">Enviar Pedido</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. VISTA DE BUZÓN -->
        <div id="tab-buzon" class="tab-view <?php echo ($tab == 'buzon') ? 'active' : ''; ?>">
            <h2 class="section-title">📋 Buzón de Pedidos Recibidos</h2>
            <div class="table-box">
                <table>
                    <tr>
                        <th>Cliente</th>
                        <th>Correo</th>
                        <th>Productos Elegidos</th>
                        <th>Instrucciones</th>
                    </tr>
                    <?php
                    $conexion_bz = new mysqli($servidor, $usuario, $password, $base_datos);
                    if (!$conexion_bz->connect_error) {
                        $res_bz = $conexion_bz->query("SELECT nombre, correo, producto_interes, mensaje FROM contacto");
                        if ($res_bz && $res_bz->num_rows > 0) {
                            while($row = $res_bz->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td><strong>".htmlspecialchars($row['nombre'])."</strong></td>";
                                echo "<td>".htmlspecialchars($row['correo'])."</td>";
                                echo "<td><span style='color:var(--primary); font-weight:700; white-space: pre-line;'>".htmlspecialchars($row['producto_interes'])."</span></td>";
                                echo "<td>".htmlspecialchars($row['mensaje'])."</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='4' style='text-align:center; color:#777;'>El buzón está vacío.</td></tr>";
                        }
                        $conexion_bz->close();
                    }
                    ?>
                </table>
            </div>
        </div>

        <!-- 3. VISTA DE ADMINISTRACIÓN -->
        <div id="tab-admin" class="tab-view <?php echo ($tab == 'admin') ? 'active' : ''; ?>">
            <div style="max-width: 650px; margin: 0 auto 40px auto;">
                <div class="card-form">
                    <h2 class="section-title" style="margin-top:0; font-size:18px;">➕ Agregar Producto al Inventario</h2>
                    <?php if(!empty($mensaje_producto)) echo "<div class='alert-ok'>$mensaje_producto</div>"; ?>
                    <form action="index.php?tab=admin" method="POST">
                        <div class="form-group">
                            <label>Nombre del Producto</label>
                            <input type="text" name="prod_nombre" placeholder="Ej. Queso Panela o Crema Ranchera" required>
                        </div>
                        <div class="form-group">
                            <label>Precio ($)</label>
                            <input type="number" step="0.01" name="prod_precio" placeholder="Ej. 68.50" required>
                        </div>
                        <div class="form-group">
                            <label>Categoría</label>
                            <input type="text" name="prod_categoria" placeholder="Ej. Quesos, Lácteos o Embutidos">
                        </div>
                        <button type="submit" name="btn_guardar_producto" class="btn-main">Guardar en Inventario</button>
                    </form>
                </div>
            </div>

            <h2 class="section-title">⚙️ Gestión y Control de Inventario</h2>
            <div class="table-box">
                <table>
                    <tr>
                        <th>ID</th>
                        <th>Producto</th>
                        <th>Precio</th>
                        <th>Categoría</th>
                        <th>Acción</th>
                    </tr>
                    <?php
                    $conexion_inv = new mysqli($servidor, $usuario, $password, $base_datos);
                    if (!$conexion_inv->connect_error) {
                        $res_inv = $conexion_inv->query("SELECT id, nombre, precio, categoria FROM productos");
                        if ($res_inv && $res_inv->num_rows > 0) {
                            while($inv = $res_inv->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>#".$inv['id']."</td>";
                                echo "<td><strong>".htmlspecialchars($inv['nombre'])."</strong></td>";
                                echo "<td class='prod-price' style='font-size: 15px; color:#16A34A;'>$".number_format($inv['precio'], 2)."</td>";
                                echo "<td>".htmlspecialchars($inv['categoria'])."</td>";
                                echo "<td>
                                        <form action='index.php?tab=admin' method='POST' style='margin:0;'>
                                            <input type='hidden' name='prod_id' value='".$inv['id']."'>
                                            <button type='submit' name='btn_eliminar_producto' style='background:#EF4444; color:white; border:none; padding:6px 12px; border-radius:6px; cursor:pointer; font-weight:bold;'>Eliminar</button>
                                        </form>
                                     </td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='5' style='text-align:center; color:#777;'>No hay productos registrados.</td></tr>";
                        }
                        $conexion_inv->close();
                    }
                    ?>
                </table>
            </div>
        </div>

    </div>

    <footer>
        Cremería Chapala &copy; 2026 — SAUZA GUTIERREZ FERNANDO😍🙉 - AGUILAR DOMINGUEZ DIEGO MAURICIO 🤑🤌🏾
    </footer>

    <!-- SCRIPT DE JAVASCRIPT PARA MANEJAR EL CARRITO DINÁMICO -->
    <script>
        // Array para almacenar los productos seleccionados en memoria
        let carritoProductos = [];

        function agregarProducto(texto) {
            carritoProductos.push(texto);
            actualizarVistaCarrito();
        }

        function eliminarProducto(index) {
            carritoProductos.splice(index, 1);
            actualizarVistaCarrito();
        }

        function vaciarCarrito() {
            carritoProductos = [];
            actualizarVistaCarrito();
        }

        function actualizarVistaCarrito() {
            const container = document.getElementById('cart-container');
            const counterLabel = document.getElementById('counter-label');
            
            container.innerHTML = '';

            if (carritoProductos.length === 0) {
                container.innerHTML = '<div class="empty-cart-text" id="empty-msg">Haz clic en los productos de la izquierda para agregarlos aquí...</div>';
                counterLabel.innerText = '0 productos seleccionados';
                return;
            }

            counterLabel.innerText = carritoProductos.length + (carritoProductos.length === 1 ? ' producto seleccionado' : ' productos seleccionados');

            carritoProductos.forEach((prod, index) => {
                const itemDiv = document.createElement('div');
                itemDiv.className = 'cart-item';
                itemDiv.innerHTML = `
                    <span>• ${prod}</span>
                    <button type="button" class="btn-remove-item" onclick="eliminarProducto(${index})" title="Quitar este producto">✕</button>
                `;
                container.appendChild(itemDiv);
            });
        }

        // Antes de enviar el formulario a PHP, unimos todos los productos con saltos de línea para guardarlos juntos en la base de datos
        function prepararEnvio() {
            if (carritoProductos.length === 0) {
                alert("Por favor selecciona al menos un producto para tu pedido.");
                return false;
            }
            
            let textoFinal = "";
            carritoProductos.forEach((prod, index) => {
                textoFinal += (index === 0 ? "" : "\n") + "• " + prod;
            });

            document.getElementById('producto_interes').value = textoFinal;
            return true;
        }
    </script>
</body>
</html>