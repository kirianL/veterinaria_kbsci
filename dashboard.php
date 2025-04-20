<?php
require_once __DIR__ . '/config.php';

// Verificar sesión
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$citas_hoy = $db->query("SELECT COUNT(*) as total FROM citas WHERE DATE(fecha) = CURDATE()")->fetch_assoc();
$stock_bajo = $db->query("SELECT COUNT(*) as total FROM inventario WHERE stock <= stock_minimo")->fetch_assoc();
$ventas_hoy = $db->query("SELECT SUM(total) as total FROM facturas WHERE DATE(fecha) = CURDATE()")->fetch_assoc();
?>
<!-- dashboard.php -->
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Sistema Veterinario</title>
    <link rel="stylesheet" href="public/css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>
    <div class="main-container">
        <?php include __DIR__ . '/components/header.php'; ?>
        <?php include __DIR__ . '/components/sidebar.php'; ?>

        <main class="main-content">
            <h1 class="dashboard-title">Panel de Control</h1>

            <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success"><?= htmlspecialchars($_GET['success']) ?></div>
            <?php endif; ?>

            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-error"><?= htmlspecialchars($_GET['error']) ?></div>
            <?php endif; ?>

            <div class="dashboard-horizontal">
                <div class="info-card info-citas">
                    <div class="info-header">
                        <h2>Citas del día</h2>
                        <span class="info-total"><?= $citas_hoy['total'] ?></span>
                    </div>
                    <p class="info-description">Pacientes agendados para hoy.</p>
                    <ul class="info-list">
                        <li>Última cita: 3:30 PM</li>
                        <li>Primera cita: 8:00 AM</li>
                    </ul>
                    <a href="services/citas/" class="info-btn">Ver detalles</a>
                </div>
                <div class="info-card info-ventas">
                    <div class="info-header">
                        <h2>Ventas del día</h2>
                        <span class="info-total">₡<?= number_format($ventas_hoy['total'] ?? 0, 2) ?></span>
                    </div>
                    <p class="info-description">Total por servicios y productos.</p>
                    <ul class="info-list">
                        <li>Tickets: 14</li>
                        <li>Promedio: ₡<?= number_format(($ventas_hoy['total'] ?? 0) / 14, 2) ?></li>
                    </ul>
                    <a href="services/facturacion/reportes.php" class="info-btn">Ver reportes</a>
                </div>
            </div>

            <div class="card">
                <h2>Actividad Reciente</h2>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Módulo</th>
                            <th>Acción</th>
                            <th>Detalles</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $actividad = $db->query("
                            SELECT b.*, u.nombre as usuario 
                            FROM bitacora b
                            JOIN usuarios u ON b.usuario_id = u.id
                            ORDER BY b.fecha DESC LIMIT 5
                        ");

                        while ($item = $actividad->fetch_assoc()): ?>
                            <tr>
                                <td><?= date('d/m/Y H:i', strtotime($item['fecha'])) ?></td>
                                <td><?= ucfirst($item['modulo']) ?></td>
                                <td><?= ucfirst($item['accion']) ?></td>
                                <td><?= htmlspecialchars($item['descripcion']) ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <?php include __DIR__ . '/components/footer.php'; ?>

    <script src="public/js/scripts.js"></script>
</body>

</html>