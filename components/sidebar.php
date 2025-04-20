<?php
if (isset($_SESSION['user']) && is_array($_SESSION['user']) && isset($_SESSION['user']['rol'])):
    $currentFile = basename($_SERVER['PHP_SELF']);
    $currentUri = $_SERVER['REQUEST_URI'] ?? '';

    $user = $_SESSION['user'];
    $urlFotos = BASE_URL . '/public/img/usuarios/';
    $foto = !empty($user['foto']) ? htmlspecialchars($user['foto']) : 'default.jpg';
    $fotoPath = $urlFotos . $foto;
    $nombre = htmlspecialchars($user['nombre'] ?? 'Usuario');
    $rol = htmlspecialchars($user['rol'] ?? 'Rol');
?>
    <!DOCTYPE html>
    <html lang="es">

    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Panel</title>
        <link rel="stylesheet" href="<?= BASE_URL ?>/public/css/estilos.css">
    </head>

    <body>
        <nav class="sidebar">
            <div class="sidebar-header">
                <img src="<?= $fotoPath ?>" alt="Foto de perfil" class="sidebar-header-img">
                <h3><?= $nombre ?></h3>
                <p><?= ucfirst($rol) ?></p>
            </div>

            <ul class="sidebar-menu">
                <li>
                    <a href="<?= BASE_URL ?>/dashboard.php" class="<?= $currentFile == 'dashboard.php' ? 'active' : '' ?>">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a>
                </li>

                <?php if (in_array($user['rol'], ['admin', 'veterinario'])): ?>
                    <li>
                        <a href="<?= BASE_URL ?>/services/citas/" class="<?= strpos($currentUri, '/services/citas/') !== false ? 'active' : '' ?>">
                            <i class="fas fa-calendar-alt"></i> Citas
                        </a>
                    </li>
                    <li>
                        <a href="<?= BASE_URL ?>/services/inventario/" class="<?= strpos($currentUri, '/services/inventario/') !== false ? 'active' : '' ?>">
                            <i class="fas fa-boxes"></i> Inventario
                        </a>
                    </li>
                <?php endif; ?>

                <?php if (in_array($user['rol'], ['admin', 'cajero'])): ?>
                    <li>
                        <a href="<?= BASE_URL ?>/services/facturacion/" class="<?= strpos($currentUri, '/services/facturacion/') !== false ? 'active' : '' ?>">
                            <i class="fas fa-file-invoice-dollar"></i> Facturación
                        </a>
                    </li>
                <?php endif; ?>

                <?php if ($user['rol'] === 'admin'): ?>
                    <li>
                        <a href="<?= BASE_URL ?>/services/bitacora/registros.php" class="<?= strpos($currentUri, '/services/bitacora/') !== false ? 'active' : '' ?>">
                            <i class="fas fa-clipboard-list"></i> Bitácora
                        </a>
                    </li>

                    <?php
                    $isAdminSection = strpos($currentUri, '/services/administracion/') !== false;
                    $isUsersPage = $isAdminSection && ($currentFile == 'index.php');
                    $isRolesPage = $isAdminSection && $currentFile == 'roles.php';
                    ?>

                    <li class="menu-item-has-children <?= $isAdminSection ? 'active' : '' ?>">
                        <a href="<?= BASE_URL ?>/services/administracion/" class="<?= $isAdminSection ? 'active' : '' ?>">
                            <i class="fas fa-cog"></i> Administración
                        </a>
                        <ul class="sub-menu" style="<?= $isAdminSection ? 'display: block;' : '' ?>">
                            <li>
                                <a href="<?= BASE_URL ?>/services/administracion/" class="<?= $isUsersPage ? 'active' : '' ?>">
                                    <i class="fas fa-users"></i> Usuarios
                                </a>
                            </li>
                            <li>
                                <a href="<?= BASE_URL ?>/services/administracion/roles.php" class="<?= $isRolesPage ? 'active' : '' ?>">
                                    <i class="fas fa-user-shield"></i> Roles
                                </a>
                            </li>
                        </ul>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                document.querySelectorAll('.menu-item-has-children > a').forEach(item => {
                    item.addEventListener('click', function(e) {
                        if (this.parentElement.classList.contains('menu-item-has-children')) {
                            e.preventDefault();
                            const parentItem = this.parentElement;
                            const submenu = this.nextElementSibling;
                            const isActive = parentItem.classList.contains('active');

                            document.querySelectorAll('.menu-item-has-children').forEach(item => {
                                item.classList.remove('active');
                                item.querySelector('.sub-menu').style.display = 'none';
                            });

                            if (!isActive) {
                                parentItem.classList.add('active');
                                submenu.style.display = 'block';
                            }

                            this.setAttribute('aria-expanded', parentItem.classList.contains('active'));
                        }
                    });
                });

                document.querySelectorAll('.menu-item-has-children').forEach(item => {
                    const link = item.querySelector('a');
                    link.setAttribute('aria-expanded', item.classList.contains('active'));
                });
            });
        </script>
    </body>

    </html>
<?php endif; ?>