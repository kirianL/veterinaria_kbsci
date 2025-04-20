<?php
require_once '../../config.php';
verificarRol('admin');

$permisos = [
    'admin' => ['dashboard', 'citas', 'inventario', 'facturacion', 'administracion', 'bitacora'],
    'veterinario' => ['dashboard', 'citas'],
    'cajero' => ['dashboard', 'facturacion']
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rol = sanitizar($_POST['rol']);
    $modulos = $_POST['modulos'] ?? [];

    $_SESSION['permisos'][$rol] = $modulos;

    bitacora("Actualizacion", "Administracion", "Actualizó permisos para rol: $rol");
    $mensaje = "Permisos actualizados correctamente";
}
?>

<?php include '../../components/header.php'; ?>
<?php include '../../components/sidebar.php'; ?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/css/styles.css" />
    <title>Gestión de Permisos</title>
</head>

<body>
    <main class="main-content">
        <div class="card">
            <h1 class="page-title">Gestión de permisos por rol</h1>

            <?php if (isset($mensaje)): ?>
                <div class="alert alert-success"><?= $mensaje ?></div>
            <?php endif; ?>

            <form method="post" class="form-container">
                <div class="form-row">
                    <div class="form-group">
                        <label for="select-rol">Seleccionar Rol:</label>
                        <select name="rol" id="select-rol" class="form-control" required>
                            <option value="">Seleccione un rol</option>
                            <option value="admin">Administrador</option>
                            <option value="veterinario">Veterinario</option>
                            <option value="cajero">Cajero</option>
                        </select>
                    </div>
                </div>

                <div id="permisos-container" class="permisos-container" style="display: none;">
                    <h3 class="permisos-title">Permisos disponibles</h3>
                    <div class="checkbox-grid">
                        <label class="checkbox-label">
                            <input type="checkbox" name="modulos[]" value="dashboard" class="checkbox-input">
                            <span class="checkbox-custom"></span>
                            <span class="checkbox-text">Dashboard</span>
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="modulos[]" value="citas" class="checkbox-input">
                            <span class="checkbox-custom"></span>
                            <span class="checkbox-text">Citas</span>
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="modulos[]" value="inventario" class="checkbox-input">
                            <span class="checkbox-custom"></span>
                            <span class="checkbox-text">Inventario</span>
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="modulos[]" value="facturacion" class="checkbox-input">
                            <span class="checkbox-custom"></span>
                            <span class="checkbox-text">Facturación</span>
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="modulos[]" value="administracion" class="checkbox-input">
                            <span class="checkbox-custom"></span>
                            <span class="checkbox-text">Administración</span>
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="modulos[]" value="bitacora" class="checkbox-input">
                            <span class="checkbox-custom"></span>
                            <span class="checkbox-text">Bitácora</span>
                        </label>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Guardar Permisos
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </main>

    <script>
        document.getElementById('select-rol').addEventListener('change', function() {
            const container = document.getElementById('permisos-container');
            const checkboxes = document.querySelectorAll('input[name="modulos[]"]');

            if (this.value) {
                container.style.display = 'block';

                checkboxes.forEach(cb => cb.checked = false);

                const permisosActuales = {
                    'admin': ['dashboard', 'citas', 'inventario', 'facturacion', 'administracion', 'bitacora'],
                    'veterinario': ['dashboard', 'citas'],
                    'cajero': ['dashboard', 'facturacion']
                } [this.value] || [];

                checkboxes.forEach(cb => {
                    if (permisosActuales.components(cb.value)) {
                        cb.checked = true;
                    }
                });
            } else {
                container.style.display = 'none';
            }
        });
    </script>

    <?php include '../../components/footer.php'; ?>
</body>

</html>