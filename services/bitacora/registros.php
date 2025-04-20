<?php
require_once '../../config.php';
verificarRol('admin');

$filtros = [
    'usuario' => $_GET['usuario'] ?? '',
    'accion' => $_GET['accion'] ?? '',
    'fecha_desde' => $_GET['fecha_desde'] ?? '',
    'fecha_hasta' => $_GET['fecha_hasta'] ?? date('Y-m-d')
];

$sql = "SELECT b.*, u.nombre as usuario_nombre FROM bitacora b 
        JOIN usuarios u ON b.usuario_id = u.id 
        WHERE 1=1";

$params = [];
$types = '';

if (!empty($filtros['usuario'])) {
    $sql .= " AND u.nombre LIKE ?";
    $params[] = '%' . $filtros['usuario'] . '%';
    $types .= 's';
}

if (!empty($filtros['accion'])) {
    $sql .= " AND b.accion = ?";
    $params[] = $filtros['accion'];
    $types .= 's';
}

if (!empty($filtros['fecha_desde'])) {
    $sql .= " AND DATE(b.fecha) >= ?";
    $params[] = $filtros['fecha_desde'];
    $types .= 's';
}

if (!empty($filtros['fecha_hasta'])) {
    $sql .= " AND DATE(b.fecha) <= ?";
    $params[] = $filtros['fecha_hasta'];
    $types .= 's';
}

$sql .= " ORDER BY b.fecha DESC";

$stmt = $db->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$resultado = $stmt->get_result();
$registros = $resultado->fetch_all(MYSQLI_ASSOC);

$acciones = $db->query("SELECT DISTINCT accion FROM bitacora ORDER BY accion")->fetch_all(MYSQLI_ASSOC);
?>



<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bitácora del Sistema</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.sheetjs.com/xlsx-0.20.0/package/dist/xlsx.full.min.js"></script>
</head>

<body>
    <?php include '../../components/header.php'; ?>
    <?php include '../../components/sidebar.php'; ?>
    <main class="main-content">
        <div class="page-title">
            <h1><i class="fas fa-clipboard-list"></i> Registros de Bitácora</h1>
            <p>Historial de actividades en el sistema</p>
        </div>

        <div class="card filtros-card">
            <h2><i class="fas fa-filter"></i> Filtros de Búsqueda</h2>
            <form method="get" class="form-filtros">
                <div class="form-row">
                    <div class="form-group">
                        <label for="usuario">Usuario:</label>
                        <input type="text" id="usuario" name="usuario" class="form-control"
                            value="<?= htmlspecialchars($filtros['usuario']) ?>" placeholder="Buscar por usuario">
                    </div>

                    <div class="form-group">
                        <label for="accion">Acción:</label>
                        <select id="accion" name="accion" class="form-control">
                            <option value="">Todas las acciones</option>
                            <?php foreach ($acciones as $accion): ?>
                                <option value="<?= $accion['accion'] ?>" <?= $filtros['accion'] == $accion['accion'] ? 'selected' : '' ?>>
                                    <?= ucfirst($accion['accion']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="fecha_desde">Fecha desde:</label>
                        <input type="date" id="fecha_desde" name="fecha_desde" class="form-control"
                            value="<?= $filtros['fecha_desde'] ?>">
                    </div>

                    <div class="form-group">
                        <label for="fecha_hasta">Fecha hasta:</label>
                        <input type="date" id="fecha_hasta" name="fecha_hasta" class="form-control"
                            value="<?= $filtros['fecha_hasta'] ?>">
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> Buscar
                    </button>
                    <a href="registros.php" class="btn">
                        <i class="fas fa-broom"></i> Limpiar
                    </a>
                </div>
            </form>
        </div>

        <div class="card">
            <div class="table-header">
                <div class="table-info">
                    <span>Mostrando <?= count($registros) ?> registros</span>
                </div>
                <div class="export-buttons">
                    <button onclick="exportarExcel()" class="btn-export btn-export-excel">
                        <i class="fas fa-file-excel"></i> Exportar Excel
                    </button>
                    <button onclick="exportarCSV()" class="btn-export btn-export-csv">
                        <i class="fas fa-file-csv"></i> Exportar CSV
                    </button>
                    <button onclick="window.print()" class="btn-export">
                        <i class="fas fa-print"></i> Imprimir
                    </button>
                </div>
            </div>

            <div class="table-responsive">
                <table class="data-table" id="tabla-bitacora">
                    <thead>
                        <tr>
                            <th>Fecha/Hora</th>
                            <th>Usuario</th>
                            <th>Acción</th>
                            <th>Módulo</th>
                            <th>Descripción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($registros)): ?>
                            <tr>
                                <td colspan="5" class="text-center">No se encontraron registros con los filtros aplicados</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($registros as $registro): ?>
                                <tr>
                                    <td><?= date('d/m/Y H:i', strtotime($registro['fecha'])) ?></td>
                                    <td><?= htmlspecialchars($registro['usuario_nombre']) ?></td>
                                    <td><span class="estado-badge"><?= ucfirst($registro['accion']) ?></span></td>
                                    <td><?= ucfirst($registro['modulo']) ?></td>
                                    <td><?= htmlspecialchars($registro['descripcion']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <script>
        function exportarCSV() {
            let csv = 'Fecha/Hora,Usuario,Acción,Módulo,Descripción\n';

            document.querySelectorAll('#tabla-bitacora tbody tr').forEach(row => {
                if (row.querySelector('.text-center') === null) {
                    const cells = row.querySelectorAll('td');
                    csv += `"${cells[0].textContent}","${cells[1].textContent}","${cells[2].textContent}",`;
                    csv += `"${cells[3].textContent}","${cells[4].textContent}"\n`;
                }
            });

            const blob = new Blob([csv], {
                type: 'text/csv;charset=utf-8;'
            });
            const link = document.createElement('a');
            const url = URL.createObjectURL(blob);

            link.setAttribute('href', url);
            link.setAttribute('download', `bitacora_${new Date().toISOString().slice(0,10)}.csv`);
            link.style.visibility = 'hidden';

            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }

        function exportarExcel() {
            const wb = XLSX.utils.book_new();
            const ws = XLSX.utils.table_to_sheet(document.getElementById('tabla-bitacora'));
            XLSX.utils.book_append_sheet(wb, ws, "Bitácora");
            XLSX.writeFile(wb, `bitacora_${new Date().toISOString().slice(0,10)}.xlsx`);
        }
    </script>
</body>

</html>