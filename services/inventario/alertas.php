<?php
require_once '../../config.php';
verificarRol('admin,veterinario');

// Obtener productos con stock bajo
$productos = $db->query("
    SELECT * FROM inventario 
    WHERE stock <= stock_minimo 
    ORDER BY (stock/stock_minimo) ASC, nombre
");
?>

<?php include '../../components/header.php'; ?>
<?php include '../../components/sidebar.php'; ?>

<div class="content">
    <h2>Alertas de Stock</h2>

    <div class="alertas-container">
        <?php if ($productos->num_rows === 0): ?>
            <div class="alert success">No hay productos con stock bajo</div>
        <?php else: ?>
            <div class="grid-alertas">
                <?php while ($prod = $productos->fetch_assoc()):
                    $porcentaje = round(($prod['stock'] / $prod['stock_minimo']) * 100);
                ?>
                    <div class="alerta-item">
                        <h3><?= htmlspecialchars($prod['nombre']) ?></h3>
                        <p><strong>Código:</strong> <?= htmlspecialchars($prod['codigo']) ?></p>
                        <p><strong>Categoría:</strong> <?= ucfirst($prod['categoria']) ?></p>

                        <div class="progress-bar">
                            <div class="progress" style="width: <?= min($porcentaje, 100) ?>%"></div>
                            <span><?= $porcentaje ?>%</span>
                        </div>

                        <p class="stock-info">
                            <span class="stock-actual"><?= $prod['stock'] ?></span> /
                            <span class="stock-minimo"><?= $prod['stock_minimo'] ?></span>
                        </p>

                        <div class="alerta-actions">
                            <a href="movimientos.php?producto_id=<?= $prod['id'] ?>&tipo=entrada" class="btn small primary">
                                Registrar Entrada
                            </a>
                            <a href="index.php?editar=<?= $prod['id'] ?>" class="btn small">
                                Editar Producto
                            </a>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php endif; ?>
    </div>
</div>


<?php include '../../components/footer.php'; ?>