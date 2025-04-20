<?php
require_once '../../config.php';
verificarRol('admin,veterinario');

// Obtener ID de paciente si se especifica
$paciente_id = $_GET['paciente_id'] ?? null;

if ($paciente_id) {
    // Obtener información del paciente
    $paciente = $db->query("SELECT * FROM pacientes WHERE id = $paciente_id")->fetch_assoc();

    // Obtener historial de citas
    $citas = $db->query("
        SELECT c.*, u.nombre as veterinario 
        FROM citas c
        JOIN usuarios u ON c.usuario_id = u.id
        WHERE c.paciente_id = $paciente_id
        ORDER BY c.fecha DESC
    ")->fetch_all(MYSQLI_ASSOC);
} else {
    // Redirigir si no hay paciente seleccionado
    header("Location: index.php");
    exit;
}
?>

<?php include '../../components/header.php'; ?>
<?php include '../../components/sidebar.php'; ?>

<div class="content">
    <h2>Historial Médico</h2>

    <div class="paciente-info">
        <h3><?= htmlspecialchars($paciente['nombre']) ?></h3>
        <p><strong>Tipo:</strong> <?= ucfirst($paciente['tipo_animal']) ?></p>
        <p><strong>Edad:</strong> <?= $paciente['edad'] ? $paciente['edad'] . ' años' : 'No especificada' ?></p>
        <p><strong>Peso:</strong> <?= $paciente['peso'] ? $paciente['peso'] . ' kg' : 'No especificado' ?></p>
        <p><strong>Dueño:</strong> <?= htmlspecialchars($paciente['duenio_nombre']) ?></p>
        <p><strong>Teléfono:</strong> <?= htmlspecialchars($paciente['duenio_telefono']) ?></p>
        <p><strong>Alergias/Notas:</strong> <?= htmlspecialchars($paciente['alergias']) ?></p>
    </div>

    <div class="historial-container">
        <h3>Registro de Citas</h3>

        <?php if (empty($citas)): ?>
            <p class="text-center">No hay citas registradas para este paciente</p>
        <?php else: ?>
            <?php foreach ($citas as $cita): ?>
                <div class="cita-item <?= $cita['estado'] ?>">
                    <div class="cita-header">
                        <span class="fecha"><?= date('d/m/Y H:i', strtotime($cita['fecha'])) ?></span>
                        <span class="estado"><?= ucfirst($cita['estado']) ?></span>
                        <span class="veterinario">Dr. <?= htmlspecialchars($cita['veterinario']) ?></span>
                    </div>

                    <div class="cita-body">
                        <p><strong>Motivo:</strong> <?= htmlspecialchars($cita['motivo']) ?></p>

                        <?php if ($cita['diagnostico']): ?>
                            <p><strong>Diagnóstico:</strong> <?= htmlspecialchars($cita['diagnostico']) ?></p>
                        <?php endif; ?>

                        <?php if ($cita['tratamiento']): ?>
                            <p><strong>Tratamiento:</strong> <?= htmlspecialchars($cita['tratamiento']) ?></p>
                        <?php endif; ?>

                        <?php if ($cita['notas']): ?>
                            <p><strong>Notas adicionales:</strong> <?= htmlspecialchars($cita['notas']) ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <div class="actions">
        <a href="index.php" class="btn">Volver al listado</a>
    </div>
</div>

<?php include '../../components/footer.php'; ?>