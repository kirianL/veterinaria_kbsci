<?php
session_start();

define('DB_HOST', 'localhost');
define('DB_NAME', 'veterinaria_kbsci');
define('DB_USER', 'root');
define('DB_PASS', '');

define('SITE_NAME', 'Veterinaria KBSCI ');
define('BASE_URL', 'http://localhost/veterinaria_kbsci');
define('MAX_LOGIN_ATTEMPTS', 5);

$db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($db->connect_error) {
    die("Error de conexión: " . $db->connect_error);
}

function sanitizar($data)
{
    return htmlspecialchars(trim($data));
}

function verificarRol($rolesPermitidos)
{
    $roles = explode(',', $rolesPermitidos);
    if (!isset($_SESSION['user'])) {
        header("Location: ../login.php");
        exit;
    }
    if (!in_array($_SESSION['user']['rol'], $roles)) {
        header("Location: ../dashboard.php?error=Acceso no autorizado");
        exit;
    }
}

function bitacora($accion, $modulo, $descripcion = '')
{
    global $db;
    $usuario_id = $_SESSION['user']['id'] ?? 0;
    $ip = $_SERVER['REMOTE_ADDR'];

    $stmt = $db->prepare("INSERT INTO bitacora 
                         (usuario_id, accion, modulo, descripcion, ip) 
                         VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param('issss', $usuario_id, $accion, $modulo, $descripcion, $ip);
    $stmt->execute();
}

function verificarIntentosLogin($cedula)
{
    global $db;
    $stmt = $db->prepare("SELECT intentos_login FROM usuarios WHERE cedula = ?");
    $stmt->bind_param('s', $cedula);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc()['intentos_login'] ?? 0;
}
