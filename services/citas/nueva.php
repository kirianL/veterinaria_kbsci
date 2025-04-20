<?php
require_once '../../config.php';
verificarRol('admin,veterinario');

$pacientes = $db->query("SELECT id, nombre, tipo_animal, duenio_nombre FROM pacientes ORDER BY nombre");
$veterinarios = $db->query("SELECT id, nombre FROM usuarios WHERE rol = 'veterinario' ORDER BY nombre");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $paciente_id = $_POST['paciente_id'];
    $fecha = $_POST['fecha'] . ' ' . $_POST['hora'];
    $motivo = sanitizar($_POST['motivo']);
    $veterinario_id = $_POST['veterinario_id'];

    $stmt = $db->prepare("INSERT INTO citas 
                         (paciente_id, usuario_id, fecha, motivo, estado) 
                         VALUES (?, ?, ?, ?, 'pendiente')");
    $stmt->bind_param('iiss', $paciente_id, $veterinario_id, $fecha, $motivo);

    if ($stmt->execute()) {
        $cita_id = $db->insert_id;
        bitacora('creacion', 'citas', "Nueva cita ID: $cita_id");
        header("Location: index.php?success=Cita creada correctamente");
        exit;
    } else {
        $error = "Error al crear la cita: " . $db->error;
    }
}
?>


<?php include '../../components/header.php'; ?>
<?php include '../../components/sidebar.php'; ?>

<div class="main-container">
    <main class="main-content">
        <div class="card">
            <h2 class="page-title">Nueva cita</h2>

            <?php if (isset($error)): ?>
                <div class="alert alert-error"><?= $error ?></div>
            <?php endif; ?>

            <form method="post" class="form-container">
                <div class="form-group">
                    <label>Paciente:</label>
                    <select name="paciente_id" class="form-control" required>
                        <option value="">Seleccionar paciente</option>
                        <?php while ($paciente = $pacientes->fetch_assoc()): ?>
                            <option value="<?= $paciente['id'] ?>">
                                <?= htmlspecialchars($paciente['nombre']) ?>
                                (<?= ucfirst($paciente['tipo_animal']) ?> - Dueño: <?= htmlspecialchars($paciente['duenio_nombre']) ?>)
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Fecha:</label>
                        <input type="date" name="fecha"
                            class="form-control"
                            min="<?= date('Y-m-d') ?>"
                            value="<?= date('Y-m-d') ?>"
                            required>
                    </div>

                    <div class="form-group">
                        <label>Hora:</label>
                        <input type="time" name="hora"
                            class="form-control"
                            min="08:00"
                            max="17:00"
                            value="09:00"
                            step="1800"
                            required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Veterinario:</label>
                    <select name="veterinario_id" class="form-control" required>
                        <option value="">Seleccionar veterinario</option>
                        <?php while ($vet = $veterinarios->fetch_assoc()): ?>
                            <option value="<?= $vet['id'] ?>" <?= $vet['id'] == $_SESSION['user']['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($vet['nombre']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Motivo de la cita:</label>
                    <textarea name="motivo"
                        class="form-control"
                        rows="4"
                        required
                        placeholder="Describa el motivo de la cita..."></textarea>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Guardar Cita
                    </button>
                    <a href="index.php" class="btn">
                        <i class="fas fa-times"></i> Cancelar
                    </a>
                </div>
            </form>
        </div>
    </main>
</div>

<?php include '../../components/footer.php'; ?>