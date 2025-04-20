<?php
require_once '../../config.php';
verificarRol('admin,cajero');

$filtro_fecha_desde = $_GET['fecha_desde'] ?? date('Y-m-01');
$filtro_fecha_hasta = $_GET['fecha_hasta'] ?? date('Y-m-d');

$sql = "SELECT 
            DATE(f.fecha) as fecha,
            COUNT(*) as cantidad_facturas,
            SUM(f.subtotal) as subtotal,
            SUM(f.iva) as iva,
            SUM(f.total) as total
        FROM facturas f
        WHERE DATE(f.fecha) BETWEEN ? AND ?
        GROUP BY DATE(f.fecha)
        ORDER BY fecha DESC";

$stmt = $db->prepare($sql);
$stmt->bind_param('ss', $filtro_fecha_desde, $filtro_fecha_hasta);
$stmt->execute();
$reporte = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$sql_totales = "SELECT 
                    SUM(subtotal) as subtotal,
                    SUM(iva) as iva,
                    SUM(total) as total
                FROM facturas
                WHERE DATE(fecha) BETWEEN ? AND ?";

$stmt_totales = $db->prepare($sql_totales);
$stmt_totales->bind_param('ss', $filtro_fecha_desde, $filtro_fecha_hasta);
$stmt_totales->execute();
$totales = $stmt_totales->get_result()->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/css/styles.css" />
    <title>Reportes de Facturas</title>
</head>

<body>
    <?php include '../../components/header.php'; ?>
    <?php include '../../components/sidebar.php'; ?>

    <main class="main-content">
        <div class="page-title">
            <h1><i class="fas fa-file-invoice"></i> Reportes de facturas</h1>
        </div>

        <div class="card">
            <h3>Filtros de búsqueda</h3>
            <form method="GET" action="reportes.php" class="form-filtros">
                <div class="form-group">
                    <label for="fecha_desde">Fecha desde:</label>
                    <input type="date" id="fecha_desde" name="fecha_desde" class="form-control"
                        value="<?= $filtro_fecha_desde ?>">
                </div>

                <div class="form-group">
                    <label for="fecha_hasta">Fecha hasta:</label>
                    <input type="date" id="fecha_hasta" name="fecha_hasta" class="form-control"
                        value="<?= $filtro_fecha_hasta ?>">
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary">Aplicar Filtros</button>
                </div>
            </form>

            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Cantidad de Facturas</th>
                            <th>Subtotal</th>
                            <th>IVA</th>
                            <th>Total</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reporte as $row): ?>
                            <tr>
                                <td><?= date('d/m/Y', strtotime($row['fecha'])) ?></td>
                                <td><?= $row['cantidad_facturas'] ?></td>
                                <td>₡<?= number_format($row['subtotal'], 2) ?></td>
                                <td>₡<?= number_format($row['iva'], 2) ?></td>
                                <td>₡<?= number_format($row['total'], 2) ?></td>
                                <td>
                                    <a href="detalles.php?fecha=<?= $row['fecha'] ?>" class="btn btn-primary">Ver Detalles</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="totales">
                <br>
                <h4>Totales generales</h4>
                <table class="totales-table">
                    <tr>
                        <th>Subtotal</th>
                        <td>₡<?= number_format($totales['subtotal'], 2) ?></td>
                    </tr>
                    <tr>
                        <th>IVA</th>
                        <td>₡<?= number_format($totales['iva'], 2) ?></td>
                    </tr>
                    <tr>
                        <th>Total</th>
                        <td>₡<?= number_format($totales['total'], 2) ?></td>
                    </tr>
                </table>
            </div>
        </div>
    </main>

    <?php include '../../components/footer.php'; ?>
</body>

</html>

<script>
    function exportarCSV() {
        let csv = 'Fecha,Cantidad Facturas,Subtotal,IVA,Total\n';

        document.querySelectorAll('.data-table tbody tr').forEach(row => {
            const cells = row.querySelectorAll('td');

            // Evitar filas sin datos (como "No hay datos..." o "Totales")
            if (cells.length >= 5 && !row.classList.contains('totales')) {
                csv += `"${cells[0].textContent.trim()}","${cells[1].textContent.trim()}",`;
                csv += `"${cells[2].textContent.replace('₡', '').trim()}","${cells[3].textContent.replace('₡', '').trim()}",`;
                csv += `"${cells[4].textContent.replace('₡', '').trim()}"\n`;
            }
        });

        const blob = new Blob([csv], {
            type: 'text/csv;charset=utf-8;'
        });
        const url = URL.createObjectURL(blob);
        const link = document.createElement('a');
        link.setAttribute('href', url);
        link.setAttribute('download', `reporte_financiero_${new Date().toISOString().slice(0,10)}.csv`);
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }

    function exportarExcel() {
        const originalTable = document.getElementById('data-table');
        const clonedTable = originalTable.cloneNode(true);
        const rows = clonedTable.querySelectorAll('tr');
        rows.forEach(row => {
            row.deleteCell(-1);
        });
        const wb = XLSX.utils.book_new();
        const ws = XLSX.utils.table_to_sheet(clonedTable);
        XLSX.utils.book_append_sheet(wb, ws, "Reporte");
        XLSX.writeFile(wb, `reporte_financiero_${new Date().toISOString().slice(0,10)}.xlsx`);
    }
</script>