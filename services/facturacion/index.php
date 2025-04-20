<?php
require_once '../../config.php';
verificarRol('admin,cajero');

// Obtener configuración de IVA
$config = $db->query("SELECT iva, tipo_cambio_dolar FROM configuracion LIMIT 1")->fetch_assoc();
if (!$config) {
    $config = [
        'iva' => 13,
        'tipo_cambio_dolar' => 500
    ];
}

// Procesar factura
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validar datos básicos
    $cliente_nombre = sanitizar($_POST['cliente_nombre'] ?? '');
    $cliente_identificacion = sanitizar($_POST['cliente_identificacion'] ?? '');
    $productos = $_POST['productos'] ?? [];
    $metodos_pago = $_POST['metodos_pago'] ?? [];

    // Validaciones
    if (empty($cliente_nombre)) {
        $error = "El nombre del cliente es requerido";
    } elseif (empty($productos) || !is_array($productos)) {
        $error = "Debe agregar al menos un producto o servicio";
    } elseif (empty($metodos_pago) || !is_array($metodos_pago)) {
        $error = "Debe agregar al menos un método de pago";
    } else {
        // Calcular totales
        $subtotal = 0;
        foreach ($productos as $producto) {
            $precio = floatval($producto['precio'] ?? 0);
            $cantidad = intval($producto['cantidad'] ?? 1);
            $subtotal += $precio * $cantidad;
        }

        $iva = $subtotal * (floatval($config['iva']) / 100);
        $total = $subtotal + $iva;

        // Registrar factura
        $db->begin_transaction();
        try {
            $usuario_id = $_SESSION['user']['id'] ?? null;
            if (!$usuario_id) throw new Exception("No se pudo identificar al usuario");

            // Insertar factura
            $stmt = $db->prepare("INSERT INTO facturas 
                                (codigo, cliente_nombre, cliente_identificacion, usuario_id, subtotal, iva, total) 
                                VALUES (?, ?, ?, ?, ?, ?, ?)");
            $codigo = 'FAC-' . date('Ymd') . '-' . str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
            $stmt->bind_param('sssiddd', $codigo, $cliente_nombre, $cliente_identificacion, $usuario_id, $subtotal, $iva, $total);
            $stmt->execute();
            $factura_id = $db->insert_id;

            // Insertar detalles
            $stmt = $db->prepare("INSERT INTO factura_detalles 
                                (factura_id, producto_id, descripcion, tipo, cantidad, precio_unitario, subtotal) 
                                VALUES (?, ?, ?, ?, ?, ?, ?)");
            foreach ($productos as $producto) {
                $producto_id = !empty($producto['id']) ? intval($producto['id']) : NULL;
                $descripcion = sanitizar($producto['descripcion'] ?? '');
                $tipo = $producto_id ? 'producto' : 'servicio';
                $cantidad = intval($producto['cantidad'] ?? 1);
                $precio = floatval($producto['precio'] ?? 0);
                $subtotal_item = $precio * $cantidad;

                $stmt->bind_param('iissidd', $factura_id, $producto_id, $descripcion, $tipo, $cantidad, $precio, $subtotal_item);
                $stmt->execute();

                // Actualizar inventario
                if ($producto_id) {
                    $db->query("UPDATE inventario SET stock = stock - $cantidad 
                               WHERE id = $producto_id AND stock >= $cantidad");
                    if ($db->affected_rows === 0) {
                        throw new Exception("No hay suficiente stock para el producto ID: $producto_id");
                    }
                }
            }

            // Registrar pagos
            $stmt = $db->prepare("INSERT INTO pagos 
                                (factura_id, metodo, monto, tipo_cambio, vuelto) 
                                VALUES (?, ?, ?, ?, ?)");
            foreach ($metodos_pago as $pago) {
                $metodo = sanitizar($pago['metodo'] ?? '');
                $monto = floatval($pago['monto'] ?? 0);
                $tipo_cambio = ($metodo === 'efectivo_dolares') ? floatval($config['tipo_cambio_dolar']) : NULL;
                $vuelto = floatval($pago['vuelto'] ?? 0);

                $stmt->bind_param('isddd', $factura_id, $metodo, $monto, $tipo_cambio, $vuelto);
                $stmt->execute();
            }

            $db->commit();
            bitacora('creacion', 'facturacion', "Factura $codigo creada");
            echo '<script>alert("Factura guardada correctamente");window.location.href="index.php";</script>';
            exit;
        } catch (Exception $e) {
            $db->rollback();
            $error = "Error al generar factura: " . $e->getMessage();
        }
    }
}

$productos_db = $db->query("SELECT id, nombre, precio_venta as precio, stock 
                          FROM inventario WHERE stock > 0 ORDER BY nombre");
$productos_data = [];
while ($row = $productos_db->fetch_assoc()) {
    $productos_data[] = $row;
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="../../public/css/styles.css" />
    <title>Nueva Factura - <?= SITE_NAME ?></title>
    <script>
        window.productosDisponibles = <?= json_encode($productos_data) ?>;
    </script>
</head>

<body>
    <div class="main-container">
        <?php include '../../components/header.php'; ?>
        <?php include '../../components/sidebar.php'; ?>

        <main class="main-content">
            <div class="card">
                <h2 class="page-title">Nueva Factura</h2>

                <?php if (isset($error)): ?>
                    <div class="alert alert-error"><?= $error ?></div>
                <?php endif; ?>

                <form id="form-factura" method="post" class="form-container">
                    <div class="form-row">
                        <div class="form-group">
                            <label>Nombre del Cliente:</label>
                            <input type="text" name="cliente_nombre" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Identificación:</label>
                            <input type="text" name="cliente_identificacion" class="form-control">
                        </div>
                    </div>

                    <div class="card">
                        <h3>Productos/Servicios</h3>
                        <div class="table-container">
                            <table id="tabla-productos" class="data-table">
                                <thead>
                                    <tr>
                                        <th>Descripción</th>
                                        <th>Cantidad</th>
                                        <th>Precio Unitario</th>
                                        <th>Subtotal</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="3" class="text-right"><strong>Subtotal:</strong></td>
                                        <td id="subtotal">₡0.00</td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" class="text-right"><strong>IVA (<?= $config['iva'] ?>%):</strong></td>
                                        <td id="iva">₡0.00</td>
                                        <td></td>
                                    </tr>
                                    <tr class="total-row">
                                        <td colspan="3" class="text-right"><strong>Total:</strong></td>
                                        <td id="total">₡0.00</td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>

                            <div class="form-actions">
                                <button type="button" id="agregar-producto" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> Agregar Producto
                                </button>
                                <button type="button" id="agregar-servicio" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> Agregar Servicio
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <h3>Métodos de Pago</h3>
                        <div id="metodos-pago" class="form-container">
                        </div>
                        <button type="button" id="agregar-pago" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Agregar Método de Pago
                        </button>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Guardar Factura
                        </button>
                        <a href="index.php" class="btn">
                            <i class="fas fa-times"></i> Cancelar
                        </a>
                    </div>
                </form>
            </div>

            <template id="template-producto">
                <tr class="producto">
                    <td>
                        <select name="productos[][id]" class="form-control select-producto" required>
                            <option value="">Seleccione un producto</option>
                        </select>
                    </td>
                    <td><input type="number" name="productos[][cantidad]" class="form-control cantidad" min="1" value="1" required></td>
                    <td><input type="number" name="productos[][precio]" class="form-control precio" step="0.01" min="0" required></td>
                    <td class="subtotal-item">₡0.00</td>
                    <td><button type="button" class="btn btn-small btn-danger eliminar-item">Eliminar</button></td>
                </tr>
            </template>

            <template id="template-servicio">
                <tr class="servicio">
                    <td>
                        <input type="text" name="productos[][descripcion]" class="form-control" placeholder="Descripción del servicio" required>
                    </td>
                    <td><input type="number" name="productos[][cantidad]" class="form-control cantidad" min="1" value="1" required></td>
                    <td><input type="number" name="productos[][precio]" class="form-control precio" step="0.01" min="0" required></td>
                    <td class="subtotal-item">₡0.00</td>
                    <td><button type="button" class="btn btn-small btn-danger eliminar-item">Eliminar</button></td>
                </tr>
            </template>

            <template id="template-pago">
                <div class="pago-form card">
                    <div class="form-row">
                        <div class="form-group">
                            <label>Método:</label>
                            <select name="metodos_pago[][metodo]" class="form-control" required>
                                <option value="efectivo_colones">Efectivo (₡)</option>
                                <option value="efectivo_dolares">Efectivo ($)</option>
                                <option value="tarjeta">Tarjeta</option>
                                <option value="transferencia">Transferencia</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Monto:</label>
                            <input type="number" name="metodos_pago[][monto]" class="form-control" step="0.01" min="0" required>
                        </div>
                        <div class="form-group pago-vuelto" style="display:none;">
                            <label>Vuelto (₡):</label>
                            <input type="number" name="metodos_pago[][vuelto]" class="form-control" step="0.01" min="0" value="0">
                        </div>
                        <div class="form-group">
                            <button type="button" class="btn btn-small btn-danger eliminar-pago">Eliminar</button>
                        </div>
                    </div>
                </div>
            </template>

            <div id="facturacion-config"
                data-iva="<?= $config['iva'] ?>"
                data-tipo-cambio="<?= $config['tipo_cambio_dolar'] ?>"></div>
        </main>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            try {
                // Configuración inicial
                const config = document.getElementById('facturacion-config');
                const iva = parseFloat(config.dataset.iva) || 13;
                const tipoCambio = parseFloat(config.dataset.tipoCambio) || 500;
                let productoCounter = 0;
                let servicioCounter = 0;

                // Función para actualizar totales
                function actualizarTotales() {
                    let subtotal = 0;
                    document.querySelectorAll('#tabla-productos tbody tr').forEach(row => {
                        const cantidad = parseFloat(row.querySelector('.cantidad').value) || 0;
                        const precio = parseFloat(row.querySelector('.precio').value) || 0;
                        subtotal += cantidad * precio;
                    });

                    const ivaTotal = subtotal * (iva / 100);
                    const total = subtotal + ivaTotal;

                    document.getElementById('subtotal').textContent = `₡${subtotal.toFixed(2)}`;
                    document.getElementById('iva').textContent = `₡${ivaTotal.toFixed(2)}`;
                    document.getElementById('total').textContent = `₡${total.toFixed(2)}`;
                }

                // Función para poblar productos en select
                function poblarProductos(selectElement) {
                    if (window.productosDisponibles && selectElement) {
                        selectElement.innerHTML = '<option value="">Seleccione un producto</option>';

                        window.productosDisponibles.forEach(producto => {
                            const option = document.createElement('option');
                            option.value = producto.id;
                            option.textContent = `${producto.nombre} - ₡${producto.precio} (Stock: ${producto.stock})`;
                            selectElement.appendChild(option);
                        });
                    }
                }

                // Función para agregar filas de producto
                function agregarProducto() {
                    const template = document.getElementById('template-producto');
                    const tbody = document.querySelector('#tabla-productos tbody');
                    const clone = document.importNode(template.content, true);

                    // Actualizar nombres de campos con contador único
                    const row = clone.querySelector('tr');
                    row.querySelectorAll('[name]').forEach(element => {
                        const name = element.getAttribute('name')
                            .replace('[]', `[${productoCounter}]`);
                        element.setAttribute('name', name);
                    });

                    // Poblar select de productos
                    const select = clone.querySelector('select.select-producto');
                    poblarProductos(select);

                    // Agregar evento para autocompletar precio
                    select.addEventListener('change', function() {
                        const precioInput = row.querySelector('.precio');
                        const producto = window.productosDisponibles.find(p => p.id == this.value);
                        if (producto) {
                            precioInput.value = producto.precio;
                            actualizarTotales();
                        }
                    });

                    // Agregar eventos para actualizar totales
                    row.querySelector('.cantidad').addEventListener('input', actualizarTotales);
                    row.querySelector('.precio').addEventListener('input', actualizarTotales);

                    // Botón eliminar
                    row.querySelector('.eliminar-item').addEventListener('click', function() {
                        row.remove();
                        actualizarTotales();
                    });

                    tbody.appendChild(clone);
                    productoCounter++;
                    actualizarTotales();
                }

                // Función para agregar filas de servicio
                function agregarServicio() {
                    const template = document.getElementById('template-servicio');
                    const tbody = document.querySelector('#tabla-productos tbody');
                    const clone = document.importNode(template.content, true);

                    // Actualizar nombres de campos con contador único
                    const row = clone.querySelector('tr');
                    row.querySelectorAll('[name]').forEach(element => {
                        const name = element.getAttribute('name')
                            .replace('[]', `[${servicioCounter}]`);
                        element.setAttribute('name', name);
                    });

                    // Agregar eventos para actualizar totales
                    row.querySelector('.cantidad').addEventListener('input', actualizarTotales);
                    row.querySelector('.precio').addEventListener('input', actualizarTotales);

                    // Botón eliminar
                    row.querySelector('.eliminar-item').addEventListener('click', function() {
                        row.remove();
                        actualizarTotales();
                    });

                    tbody.appendChild(clone);
                    servicioCounter++;
                    actualizarTotales();
                }

                // Función para agregar métodos de pago
                function agregarPago() {
                    const template = document.getElementById('template-pago');
                    const container = document.getElementById('metodos-pago');
                    const clone = document.importNode(template.content, true);

                    const pagoForm = clone.querySelector('.pago-form');

                    // Botón eliminar
                    pagoForm.querySelector('.eliminar-pago').addEventListener('click', function() {
                        pagoForm.remove();
                    });

                    // Mostrar campo de vuelto solo para efectivo
                    const metodoSelect = pagoForm.querySelector('select[name$="[metodo]"]');
                    metodoSelect.addEventListener('change', function() {
                        const vueltoGroup = pagoForm.querySelector('.pago-vuelto');
                        vueltoGroup.style.display = this.value === 'efectivo_colones' ? 'block' : 'none';
                    });

                    container.appendChild(clone);
                }

                // Eventos principales
                document.getElementById('agregar-producto').addEventListener('click', agregarProducto);
                document.getElementById('agregar-servicio').addEventListener('click', agregarServicio);
                document.getElementById('agregar-pago').addEventListener('click', agregarPago);

                // Inicialización
                agregarProducto();
                agregarPago();

            } catch (error) {
                console.error('Error:', error);
                alert('Error al inicializar la facturación');
            }
        });
    </script>
    <?php include '../../components/footer.php'; ?>
</body>

</html>