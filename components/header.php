<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$usuarioLogueado = isset($_SESSION['user']);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars(SITE_NAME) ?> - <?= htmlspecialchars($page_title ?? 'Panel') ?></title>
    <link rel="stylesheet" href="<?= BASE_URL ?? '' ?>/public/css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>
    <header class="main-header">
        <div class="header-left">
            <h1><a href="<?= BASE_URL ?? '' ?>/dashboard.php"><?= htmlspecialchars(SITE_NAME) ?></a></h1>
        </div>
        <?php if ($usuarioLogueado): ?>
            <div class="header-right">
                <div class="user-info">
                    <a style="text-decoration: none; color:white;" href="<?= BASE_URL ?? '' ?>/logout.php" class="logout-btn" id="logoutLink">
                        <i class="fas fa-sign-out-alt"></i>
                        Cerrar sesión
                    </a>
                </div>
            </div>
        <?php endif; ?>
    </header>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const logoutLink = document.getElementById('logoutLink');
            if (logoutLink) {
                logoutLink.addEventListener('click', function(e) {
                    if (!confirm('¿Está seguro que desea cerrar sesión?')) {
                        e.preventDefault();
                    }
                });
            }
        });
    </script>
</body>

</html>