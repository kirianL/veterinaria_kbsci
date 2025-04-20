<?php
require_once '../../config.php';
verificarRol('admin,cajero');
$fecha = isset($_GET['fecha']) ? $_GET['fecha'] : '';
if (empty($fecha)) {
    echo "Fecha no válida.";
    exit;
}

$sql = "SELECT f.id, f.codigo, f.cliente_nombre, f.fecha, f.subtotal, f.iva, f.total
        FROM facturas f
        WHERE DATE(f.fecha) = ?";
$stmt = $db->prepare($sql);
$stmt->bind_param('s', $fecha);
$stmt->execute();
$facturas = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

if (empty($facturas)) {
    echo "No se encontraron facturas para la fecha seleccionada.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/css/styles.css" />
    <title>Detalles de las facturas - <?= date('d/m/Y', strtotime($fecha)) ?></title>
</head>

<body>
    <?php include '../../components/header.php'; ?>
    <?php include '../../components/sidebar.php'; ?>

    <main class="main-content">
        <div class="page-title">
            <h1><i class="fas fa-file-invoice"></i> Detalles de las facturas - <?= date('d/m/Y', strtotime($fecha)) ?></h1>
        </div>

        <div class="card">
            <h3 style="margin-bottom: 10px;">Facturas </h3>

            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Cliente</th>
                            <th>Subtotal</th>
                            <th>IVA</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($facturas as $factura): ?>
                            <tr>
                                <td><?= htmlspecialchars($factura['codigo']) ?></td>
                                <td><?= htmlspecialchars($factura['cliente_nombre']) ?></td>
                                <td>₡<?= number_format($factura['subtotal'], 2) ?></td>
                                <td>₡<?= number_format($factura['iva'], 2) ?></td>
                                <td>₡<?= number_format($factura['total'], 2) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="form-group">
                <button class="boton" onclick="window.location.href='reportes.php'">
                    <i class="fas fa-arrow-left"></i> Volver a reportes
                </button>
            </div>

        </div>
    </main>

    <?php include '../../components/footer.php'; ?>
</body>

</html>