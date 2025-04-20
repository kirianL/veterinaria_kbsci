<?php
require_once '../../config.php';
verificarRol('admin,veterinario');

if (isset($_GET['editar'])) {
    $id_editar = (int)$_GET['editar'];
    $producto = $db->query("SELECT * FROM inventario WHERE id = $id_editar")->fetch_assoc();

    if (!$producto) {
        header("Location: index.php?error=Producto no encontrado");
        exit;
    }
}


// Procesar formulario de agregar/editar producto
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $codigo = sanitizar($_POST['codigo']);
    $nombre = sanitizar($_POST['nombre']);
    $descripcion = sanitizar($_POST['descripcion']);
    $categoria = sanitizar($_POST['categoria']);
    $stock = (int)$_POST['stock'];
    $stock_minimo = (int)$_POST['stock_minimo'];
    $precio_compra = (float)$_POST['precio_compra'];
    $precio_venta = (float)$_POST['precio_venta'];
    $proveedor = sanitizar($_POST['proveedor']);

    if (empty($id)) {
        // Nuevo producto
        $stmt = $db->prepare("INSERT INTO inventario 
                            (codigo, nombre, descripcion, categoria, stock, stock_minimo, precio_compra, precio_venta, proveedor) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param('ssssiidds', $codigo, $nombre, $descripcion, $categoria, $stock, $stock_minimo, $precio_compra, $precio_venta, $proveedor);

        if ($stmt->execute()) {
            $producto_id = $db->insert_id;
            bitacora('creacion', 'inventario', "Nuevo producto ID: $producto_id");

            // Registrar movimiento de entrada inicial
            registrarMovimiento($producto_id, 'entrada', $stock, 'inicial', $_SESSION['user']['id'], "Entrada inicial de stock");

            header("Location: index.php?success=Producto agregado correctamente");
            exit;
        } else {
            $error = "Error al agregar producto: " . $db->error;
        }
    } else {
        // Editar producto existente
        $stmt = $db->prepare("UPDATE inventario SET 
                            codigo = ?, nombre = ?, descripcion = ?, categoria = ?, 
                            stock_minimo = ?, precio_compra = ?, precio_venta = ?, proveedor = ? 
                            WHERE id = ?");
        $stmt->bind_param('ssssiddsi', $codigo, $nombre, $descripcion, $categoria, $stock_minimo, $precio_compra, $precio_venta, $proveedor, $id);

        if ($stmt->execute()) {
            bitacora('actualizacion', 'inventario', "Actualizó producto ID: $id");
            header("Location: index.php?success=Producto actualizado correctamente");
            exit;
        } else {
            $error = "Error al actualizar producto: " . $db->error;
        }
    }
}

// Procesar eliminación
if (isset($_GET['eliminar'])) {
    $id = $_GET['eliminar'];

    $db->query("DELETE FROM inventario WHERE id = $id");
    bitacora('eliminacion', 'inventario', "Eliminó producto ID: $id");
    header("Location: index.php?success=Producto eliminado correctamente");
    exit;
}

// Obtener lista de productos
$productos = $db->query("SELECT * FROM inventario ORDER BY nombre");

// Función para registrar movimientos
function registrarMovimiento($producto_id, $tipo, $cantidad, $motivo, $usuario_id, $notas)
{
    global $db;

    $stmt = $db->prepare("INSERT INTO movimientos_inventario 
                        (producto_id, tipo, cantidad, motivo, usuario_id, notas) 
                        VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param('isisis', $producto_id, $tipo, $cantidad, $motivo, $usuario_id, $notas);
    $stmt->execute();

    // Actualizar stock
    $operacion = $tipo === 'entrada' ? '+' : '-';
    $db->query("UPDATE inventario SET stock = stock $operacion $cantidad WHERE id = $producto_id");
}
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Gestión de Inventario - <?= SITE_NAME ?></title>
    <link rel="stylesheet" href="../../public/css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>
    <div class="main-container">
        <?php include __DIR__ . '/../../components/header.php'; ?>
        <?php include __DIR__ . '/../../components/sidebar.php'; ?>

        <main class="main-content">
            <div class="card">
                <h2 class="page-title">Gestión de Inventario</h2>

                <?php if (isset($_GET['success'])): ?>
                    <div class="alert alert-success"><?= htmlspecialchars($_GET['success']) ?></div>
                <?php endif; ?>

                <?php if (isset($error)): ?>
                    <div class="alert alert-error"><?= $error ?></div>
                <?php endif; ?>

                <div class="card">
                    <h3><?= isset($_GET['editar']) ? 'Editar Producto' : 'Agregar Nuevo Producto' ?></h3>
                    <form method="post" class="form-container">
                        <?php if (isset($_GET['editar'])): ?>
                            <input type="hidden" name="id" value="<?= $producto['id'] ?>">
                        <?php endif; ?>

                        <div class="form-row">
                            <div class="form-group">
                                <label>Código:</label>
                                <input type="text" name="codigo" class="form-control" required
                                    value="<?= isset($producto) ? $producto['codigo'] : '' ?>">
                            </div>
                            <div class="form-group">
                                <label>Nombre:</label>
                                <input type="text" name="nombre" class="form-control" required
                                    value="<?= isset($producto) ? $producto['nombre'] : '' ?>">
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Descripción:</label>
                            <textarea name="descripcion" class="form-control" rows="2"><?= isset($producto) ? $producto['descripcion'] : '' ?></textarea>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label>Categoría:</label>
                                <select name="categoria" class="form-control" required>
                                    <?php foreach (['medicamento', 'alimento', 'equipo', 'suministro'] as $cat): ?>
                                        <option value="<?= $cat ?>" <?= isset($producto) && $producto['categoria'] == $cat ? 'selected' : '' ?>>
                                            <?= ucfirst($cat) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <?php if (!isset($_GET['editar'])): ?>
                                <div class="form-group">
                                    <label>Stock Inicial:</label>
                                    <input type="number" name="stock" class="form-control" min="0" value="0" required>
                                </div>
                            <?php endif; ?>

                            <div class="form-group">
                                <label>Stock Mínimo:</label>
                                <input type="number" name="stock_minimo" class="form-control" min="0"
                                    value="<?= isset($producto) ? $producto['stock_minimo'] : '5' ?>" required>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label>Precio de Compra:</label>
                                <div class="input-group">
                                    <span class="input-icon">₡</span>
                                    <input type="number" name="precio_compra" class="form-control" step="0.01" min="0"
                                        value="<?= isset($producto) ? $producto['precio_compra'] : '0' ?>" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Precio de Venta:</label>
                                <div class="input-group">
                                    <span class="input-icon">₡</span>
                                    <input type="number" name="precio_venta" class="form-control" step="0.01" min="0"
                                        value="<?= isset($producto) ? $producto['precio_venta'] : '0' ?>" required>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Proveedor:</label>
                            <input type="text" name="proveedor" class="form-control"
                                value="<?= isset($producto) ? $producto['proveedor'] : '' ?>">
                        </div>

                        <div class="form-actions">
                            <?php if (isset($_GET['editar'])): ?>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Actualizar
                                </button>
                                <a href="index.php" class="btn">
                                    <i class="fas fa-times"></i> Cancelar
                                </a>
                            <?php else: ?>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> Guardar
                                </button>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>

                <div class="card">
                    <div class="table-header">
                        <h3>Listado de Productos</h3>
                    </div>

                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Código</th>
                                <th>Nombre</th>
                                <th>Categoría</th>
                                <th>Stock</th>
                                <th>Precio Venta</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($productos->num_rows === 0): ?>
                                <tr>
                                    <td colspan="6" class="text-center">No hay productos registrados</td>
                                </tr>
                            <?php else: ?>
                                <?php while ($prod = $productos->fetch_assoc()):
                                    $stock_bajo = $prod['stock'] <= $prod['stock_minimo'];
                                ?>
                                    <tr class="<?= $stock_bajo ? 'row-warning' : '' ?>">
                                        <td><?= htmlspecialchars($prod['codigo']) ?></td>
                                        <td><?= htmlspecialchars($prod['nombre']) ?></td>
                                        <td><?= ucfirst($prod['categoria']) ?></td>
                                        <td>
                                            <div class="stock-info">
                                                <?= $prod['stock'] ?>
                                                <?php if ($stock_bajo): ?>
                                                    <span class="badge badge-danger">
                                                        <i class="fas fa-exclamation-triangle"></i> Mín: <?= $prod['stock_minimo'] ?>
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td>₡<?= number_format($prod['precio_venta'], 2) ?></td>
                                        <td class="actions">
                                            <div class="action-buttons">
                                                <a href="index.php?editar=<?= $prod['id'] ?>" class="btn btn-small">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="movimientos.php?producto_id=<?= $prod['id'] ?>" class="btn btn-small">
                                                    <i class="fas fa-exchange-alt"></i>
                                                </a>
                                                <a href="index.php?eliminar=<?= $prod['id'] ?>" class="btn btn-small btn-danger"
                                                    onclick="return confirm('¿Está seguro de eliminar este producto?')">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <?php include '../../components/footer.php'; ?>
</body>

</html>