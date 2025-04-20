<?php
require_once 'config.php';
verificarRol('admin');

$config = $db->query("SELECT * FROM configuracion LIMIT 1")->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $iva = (float)$_POST['iva'];
    $tipo_cambio = (float)$_POST['tipo_cambio'];
    $alerta_stock = isset($_POST['alerta_stock']) ? 1 : 0;
    $dias_alerta = (int)$_POST['dias_alerta'];

    $stmt = $db->prepare("UPDATE configuracion SET 
                         iva = ?, 
                         tipo_cambio_dolar = ?, 
                         alerta_stock_minimo = ?, 
                         dias_alerta_citas = ?");
    $stmt->bind_param('ddii', $iva, $tipo_cambio, $alerta_stock, $dias_alerta);

    if ($stmt->execute()) {
        bitacora('Actualizacion', "administracion", "Configuración del sistema actualizada por el administrador");
        $mensaje = "Configuración actualizada correctamente";
        $config = $db->query("SELECT * FROM configuracion LIMIT 1")->fetch_assoc();
    } else {
        $error = "Error al actualizar configuración";
    }
}
?>

<?php include '../../components/header.php'; ?>
<?php include '../../components/sidebar.php'; ?>

<div class="content">
    <h2>Configuración del Sistema</h2>

    <?php if (isset($mensaje)): ?>
        <div class="alert success"><?= $mensaje ?></div>
    <?php endif; ?>

    <?php if (isset($error)): ?>
        <div class="alert error"><?= $error ?></div>
    <?php endif; ?>

    <form method="post">
        <div class="form-group">
            <label>IVA (%):</label>
            <input type="number" name="iva" step="0.01" min="0" max="100"
                value="<?= $config['iva'] ?>" required>
        </div>

        <div class="form-group">
            <label>Tipo de cambio (USD a CRC):</label>
            <input type="number" name="tipo_cambio" step="0.01" min="0"
                value="<?= $config['tipo_cambio_dolar'] ?>" required>
        </div>

        <div class="form-group">
            <label>
                <input type="checkbox" name="alerta_stock"
                    <?= $config['alerta_stock_minimo'] ? 'checked' : '' ?>>
                Alertas de stock mínimo
            </label>
        </div>

        <div class="form-group">
            <label>Días previos para alerta de citas:</label>
            <input type="number" name="dias_alerta" min="0" max="30"
                value="<?= $config['dias_alerta_citas'] ?>" required>
        </div>

        <button type="submit" class="btn primary">Guardar Configuración</button>
    </form>
</div>

<?php include '../../components/footer.php'; ?>