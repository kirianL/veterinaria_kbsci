<?php
require_once '../../config.php';

verificarRol('admin,veterinario'); // Solo admin y veterinarios pueden acceder

$filtro_estado = $_GET['estado'] ?? 'pendiente';
$filtro_fecha = $_GET['fecha'] ?? date('Y-m-d');

$sql = "SELECT c.*, p.nombre as paciente_nombre, p.tipo_animal, p.duenio_nombre, u.nombre as veterinario 
        FROM citas c
        JOIN pacientes p ON c.paciente_id = p.id
        JOIN usuarios u ON c.usuario_id = u.id
        WHERE DATE(c.fecha) = ?";

if ($filtro_estado != 'todas') {
    $sql .= " AND c.estado = ?";
    $params = [$filtro_fecha, $filtro_estado];
} else {
    $params = [$filtro_fecha];
}

$sql .= " ORDER BY c.fecha ASC";

$stmt = $db->prepare($sql);
$stmt->bind_param(str_repeat('s', count($params)), ...$params);
$stmt->execute();
$citas = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Gestión de Citas - <?= SITE_NAME ?></title>
    <link rel="stylesheet" href="../../public/css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>
    <div class="main-container">
        <?php include __DIR__ . '/../../components/header.php'; ?>
        <?php include __DIR__ . '/../../components/sidebar.php'; ?>

        <main class="main-content">
            <div class="card">
                <h2 class="page-title">Gestión de Citas</h2>

                <div class="card filtros-card">
                    <form method="get" class="form-filtros">
                        <div class="form-row">
                            <div class="form-group">
                                <label>Fecha:</label>
                                <input type="date" name="fecha" value="<?= $filtro_fecha ?>" class="form-control">
                            </div>

                            <div class="form-group">
                                <label>Estado:</label>
                                <select name="estado" class="form-control">
                                    <option value="pendiente" <?= $filtro_estado == 'pendiente' ? 'selected' : '' ?>>Pendientes</option>
                                    <option value="completada" <?= $filtro_estado == 'completada' ? 'selected' : '' ?>>Completadas</option>
                                    <option value="todas" <?= $filtro_estado == 'todas' ? 'selected' : '' ?>>Todas</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">Filtrar</button>
                                <a href="index.php" class="btn">Hoy</a>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="card">
                    <div class="table-header">
                        <div class="table-info">
                            <span>Citas encontradas: <?= count($citas) ?></span>
                        </div>
                        <a href="nueva.php" class="btn btn-primary">Nueva Cita</a>
                    </div>

                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Hora</th>
                                <th>Paciente</th>
                                <th>Tipo</th>
                                <th>Dueño</th>
                                <th>Motivo</th>
                                <th>Veterinario</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($citas)): ?>
                                <tr>
                                    <td colspan="7" class="text-center">No hay citas programadas</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($citas as $cita): ?>
                                    <tr>
                                        <td><?= date('H:i', strtotime($cita['fecha'])) ?></td>
                                        <td><?= htmlspecialchars($cita['paciente_nombre']) ?></td>
                                        <td><?= ucfirst($cita['tipo_animal']) ?></td>
                                        <td><?= htmlspecialchars($cita['duenio_nombre']) ?></td>
                                        <td><?= htmlspecialchars($cita['motivo']) ?></td>
                                        <td><?= htmlspecialchars($cita['veterinario']) ?></td>
                                        <td>
                                            <span class="estado-badge estado-<?= $cita['estado'] ?>">
                                                <?= ucfirst($cita['estado']) ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
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