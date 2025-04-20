<?php
require_once '../../config.php';
verificarRol('admin,veterinario');

// Validar producto
$producto_id = $_GET['producto_id'] ?? null;
if (!$producto_id || !$producto = $db->query("SELECT * FROM inventario WHERE id = $producto_id")->fetch_assoc()) {
    header("Location: index.php?error=Producto no encontrado");
    exit;
}

// Procesar movimiento
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tipo = sanitizar($_POST['tipo']);
    $cantidad = (int)$_POST['cantidad'];
    $motivo = sanitizar($_POST['motivo']);
    $notas = sanitizar($_POST['notas']);

    // Validar cantidad para salidas
    if ($tipo === 'salida' && $cantidad > $producto['stock']) {
        $error = "No hay suficiente stock para esta salida";
    } else {
        // Registrar movimiento
        registrarMovimiento($producto_id, $tipo, $cantidad, $motivo, $_SESSION['user']['id'], $notas);

        bitacora(
            'movimiento',
            'inventario',
            "$tipo de $cantidad unidades de producto ID: $producto_id"
        );
        header("Location: movimientos.php?producto_id=$producto_id&success=Movimiento registrado");
        exit;
    }
}

// Obtener movimientos del producto
$movimientos = $db->query("
    SELECT m.*, u.nombre as usuario 
    FROM movimientos_inventario m
    JOIN usuarios u ON m.usuario_id = u.id
    WHERE m.producto_id = $producto_id
    ORDER BY m.fecha DESC
");

// Función para registrar movimientos (ya definida en index.php)
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

<?php include '../../components/header.php'; ?>
<?php include '../../components/sidebar.php'; ?>
<div class="main-container">
    <main class="main-content">
        <div class="card">
            <h2 class="page-title">Movimientos de Inventario</h2>
            <h3 class="subtitle"><?= htmlspecialchars($producto['nombre']) ?> (Stock: <?= $producto['stock'] ?>)</h3>

            <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success"><?= htmlspecialchars($_GET['success']) ?></div>
            <?php endif; ?>

            <?php if (isset($error)): ?>
                <div class="alert alert-error"><?= $error ?></div>
            <?php endif; ?>

            <div class="card">
                <h3>Registrar nuevo movimiento</h3>
                <form method="post" class="form-container">
                    <div class="form-row">
                        <div class="form-group">
                            <label>Tipo:</label>
                            <select name="tipo" class="form-control" required>
                                <option value="entrada">Entrada</option>
                                <option value="salida">Salida</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Cantidad:</label>
                            <input type="number" name="cantidad" class="form-control" min="1" required>
                        </div>

                        <div class="form-group">
                            <label>Motivo:</label>
                            <select name="motivo" class="form-control" required>
                                <option value="compra">Compra</option>
                                <option value="venta">Venta</option>
                                <option value="ajuste">Ajuste de inventario</option>
                                <option value="donacion">Donación</option>
                                <option value="perdida">Pérdida/Desecho</option>
                                <option value="devolucion">Devolución</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Notas:</label>
                        <textarea name="notas" class="form-control" rows="2"></textarea>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Registrar</button>
                        <a href="index.php" class="btn">Volver</a>
                    </div>
                </form>
            </div>

            <div class="card">
                <div class="table-header">
                    <h3>Historial de movimientos</h3>
                </div>

                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Tipo</th>
                            <th>Cantidad</th>
                            <th>Motivo</th>
                            <th>Usuario</th>
                            <th>Notas</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($movimientos->num_rows === 0): ?>
                            <tr>
                                <td colspan="6" class="text-center">No hay movimientos registrados</td>
                            </tr>
                        <?php else: ?>
                            <?php while ($mov = $movimientos->fetch_assoc()): ?>
                                <tr>
                                    <td><?= date('d/m/Y H:i', strtotime($mov['fecha'])) ?></td>
                                    <td>
                                        <span class="badge <?= $mov['tipo'] === 'entrada' ? 'badge-success' : 'badge-danger' ?>">
                                            <?= ucfirst($mov['tipo']) ?>
                                        </span>
                                    </td>
                                    <td><?= $mov['cantidad'] ?></td>
                                    <td><?= ucfirst($mov['motivo']) ?></td>
                                    <td><?= htmlspecialchars($mov['usuario']) ?></td>
                                    <td><?= htmlspecialchars($mov['notas']) ?></td>
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