<?php
require_once 'config.php';

if (isset($_SESSION['user'])) {
    header("Location: " . BASE_URL . "/dashboard.php");
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cedula = sanitizar($_POST['cedula']);
    $password = sanitizar($_POST['password']);

    $intentos = verificarIntentosLogin($cedula);
    if ($intentos >= MAX_LOGIN_ATTEMPTS) {
        $error = "Cuenta bloqueada por muchos intentos fallidos. Intente más tarde.";
    } else {
        $stmt = $db->prepare("SELECT * FROM usuarios WHERE cedula = ?");
        $stmt->bind_param('s', $cedula);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();

        if ($user && $password) {
            $_SESSION['user'] = $user;
            $db->query("UPDATE usuarios SET intentos_login = 0 WHERE cedula = '$cedula'");
            bitacora('login', 'autenticacion', 'Inicio de sesión exitoso');
            header("Location: " . BASE_URL . "/dashboard.php");
            exit;
        } else {
            $db->query("UPDATE usuarios SET intentos_login = intentos_login + 1 WHERE cedula = '$cedula'");
            $error = "Credenciales incorrectas. Intentos restantes: " . (MAX_LOGIN_ATTEMPTS - $intentos - 1);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= SITE_NAME ?> - Login</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/css/login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>
    <div class="container">
        <div class="login-panel">
            <div class="login-content">
                <h1>Bienvenido a <span>KBSci</span></h1>
                <p>Inicie sesión con sus credenciales</p>

                <?php if ($error): ?>
                    <div class="alert error"><?= $error ?></div>
                <?php endif; ?>

                <form method="post" class="login-form">
                    <div class="form-group">
                        <label for="cedula"><i class="fas fa-id-card"></i> Cédula:</label>
                        <input type="text" id="cedula" name="cedula" required>
                    </div>

                    <div class="form-group">
                        <label for="password"><i class="fas fa-lock"></i> Contraseña:</label>
                        <input type="password" id="password" name="password" required>
                    </div>

                    <button type="submit" class="btn-login">
                        <i class="fas fa-sign-in-alt"></i> Ingresar
                    </button>
                </form>

                <div class="footer-note">
                    Sistema de gestión de clínica veterinaria
                </div>
            </div>
        </div>
        <div class="image-panel"></div>
    </div>
</body>

</html>