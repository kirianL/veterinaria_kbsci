<?php
require_once '../../config.php';
verificarRol('admin');

$directorioFotos = __DIR__ . '/../../public/img/usuarios/';
$urlFotos = BASE_URL . '/public/img/usuarios/';

// Eliminar usuario
if (isset($_GET['borrar'])) {
    $id = (int) $_GET['borrar'];

    if ($id !== (int) $_SESSION['user']['id']) {
        $stmt = $db->prepare("SELECT foto FROM usuarios WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($usuario = $resultado->fetch_assoc()) {
            if ($usuario['foto'] && file_exists($directorioFotos . $usuario['foto'])) {
                unlink($directorioFotos . $usuario['foto']);
            }

            $stmt = $db->prepare("DELETE FROM usuarios WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();

            bitacora("Eliminación", "Administracion", "Eliminó usuario ID: $id");
            header("Location: index.php");
            exit;
        }
    } else {
        $error = "No puedes borrar tu propio perfil.";
    }
}

// Crear o editar usuario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cedula = sanitizar($_POST['cedula']);
    $nombre = sanitizar($_POST['nombre']);
    $rol = sanitizar($_POST['rol']);
    $fotoNombre = '';

    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $extension = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
        $fotoNombre = 'foto_' . uniqid() . '.' . $extension;
        move_uploaded_file($_FILES['foto']['tmp_name'], $directorioFotos . $fotoNombre);
    }

    if (isset($_POST['agregar'])) {
        $password = password_hash(sanitizar($_POST['password']), PASSWORD_DEFAULT);

        $stmt = $db->prepare("INSERT INTO usuarios (cedula, nombre, password, rol, foto) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param('sssss', $cedula, $nombre, $password, $rol, $fotoNombre);

        if ($stmt->execute()) {
            bitacora("Creación", "Administración", "Agregó usuario: $nombre ($cedula)");
            $mensaje = "Usuario agregado correctamente.";
        } else {
            $error = "Error al agregar usuario.";
        }
    } elseif (isset($_POST['editar'])) {
        $id = (int) $_POST['id'];

        $stmt = $db->prepare("SELECT foto FROM usuarios WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $usuarioExistente = $resultado->fetch_assoc();
        $fotoActual = $usuarioExistente['foto'];

        if ($fotoNombre) {
            if ($fotoActual && file_exists($directorioFotos . $fotoActual)) {
                unlink($directorioFotos . $fotoActual);
            }
        } else {
            $fotoNombre = $fotoActual;
        }

        if ($_SESSION['user']['id'] == $id && $fotoNombre) {
            $_SESSION['user']['foto'] = $fotoNombre;
        }

        $stmt = $db->prepare("UPDATE usuarios SET cedula = ?, nombre = ?, rol = ?, foto = ? WHERE id = ?");
        $stmt->bind_param('ssssi', $cedula, $nombre, $rol, $fotoNombre, $id);

        if ($stmt->execute()) {
            bitacora("Edición", "Administración", "Editó usuario ID: $id");
            $mensaje = "Usuario actualizado correctamente.";
        } else {
            $error = "Error al actualizar usuario.";
        }
    }
}

// Bloquear o desbloquear usuario
if (isset($_GET['accion'], $_GET['id'])) {
    $id = (int) $_GET['id'];
    $accion = $_GET['accion'] === 'bloquear' ? 1 : 0;

    if ($accion === 0) { // Si se está desbloqueando
        // Reiniciar los intentos cuando se desbloquea
        $stmt = $db->prepare("UPDATE usuarios SET bloqueado = ?, intentos_login = 0 WHERE id = ?");
        $stmt->bind_param("ii", $accion, $id);
        $stmt->execute();
        $accionTexto = "Desbloqueó";
    } else { // Si se está bloqueando
        $stmt = $db->prepare("UPDATE usuarios SET bloqueado = ? WHERE id = ?");
        $stmt->bind_param("ii", $accion, $id);
        $stmt->execute();
        $accionTexto = "Bloqueó";
    }

    bitacora("Bloqueo/Desbloqueo", "Administración", "$accionTexto usuario ID: $id");
    header("Location: index.php");
    exit;
}

// Obtener usuarios
$usuarios = $db->query("SELECT * FROM usuarios ORDER BY nombre");

include '../../components/header.php';
include '../../components/sidebar.php';
?>

<!-- HTML del código -->
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/css/styles.css">
    <title>Gestión de usuarios</title>
</head>

<body>
    <div class="main-content">
        <h1 class="page-title">Gestión de usuarios</h1>

        <?php if (isset($mensaje)): ?>
            <div class="alert alert-success"><?= $mensaje ?></div>
        <?php endif; ?>

        <?php if (isset($error)): ?>
            <div class="alert alert-error"><?= $error ?></div>
        <?php endif; ?>

        <div class="card">
            <h3><?= isset($_GET['editar']) ? 'Editar usuario' : 'Agregar nuevo usuario' ?></h3>
            <form method="post" class="form-container" enctype="multipart/form-data">
                <?php if (isset($_GET['editar'])):
                    $editar = $db->query("SELECT * FROM usuarios WHERE id = " . (int) $_GET['editar'])->fetch_assoc(); ?>
                    <input type="hidden" name="id" value="<?= $editar['id'] ?>">
                <?php endif; ?>

                <div class="form-group">
                    <label>Cédula:</label>
                    <input type="text" name="cedula" class="form-control" required value="<?= $editar['cedula'] ?? '' ?>">
                </div>

                <div class="form-group">
                    <label>Nombre Completo:</label>
                    <input type="text" name="nombre" class="form-control" required value="<?= $editar['nombre'] ?? '' ?>">
                </div>

                <div class="form-group">
                    <label>Rol:</label>
                    <select name="rol" class="form-control" required>
                        <option value="admin" <?= (isset($editar) && $editar['rol'] === 'admin') ? 'selected' : '' ?>>Administrador</option>
                        <option value="veterinario" <?= (isset($editar) && $editar['rol'] === 'veterinario') ? 'selected' : '' ?>>Veterinario</option>
                        <option value="cajero" <?= (isset($editar) && $editar['rol'] === 'cajero') ? 'selected' : '' ?>>Cajero</option>
                    </select>
                </div>

                <?php if (!isset($_GET['editar'])): ?>
                    <div class="form-group">
                        <label>Contraseña:</label>
                        <input type="password" name="password" class="form-control" required minlength="6">
                    </div>
                <?php endif; ?>

                <div class="form-group">
                    <label>Foto:</label>
                    <input type="file" name="foto" class="form-control">
                    <?php if (isset($editar) && $editar['foto']): ?>
                        <br>
                        <img src="<?= $urlFotos . $editar['foto'] ?>" alt="Foto actual" width="100">
                    <?php endif; ?>
                </div>

                <div class="form-actions">
                    <?php if (isset($_GET['editar'])): ?>
                        <button type="submit" name="editar" class="btn btn-primary">Actualizar</button>
                        <a href="index.php" class="btn">Cancelar</a>
                    <?php else: ?>
                        <button type="submit" name="agregar" class="btn btn-primary">Guardar</button>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <div class="card">
            <div class="table-header">
                <h3>Listado de usuarios</h3>
            </div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Cédula</th>
                        <th>Nombre</th>
                        <th>Rol</th>
                        <th>Estado</th>
                        <th>Foto</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($usuario = $usuarios->fetch_assoc()): ?>
                        <tr>
                            <td><?= $usuario['cedula'] ?></td>
                            <td><?= $usuario['nombre'] ?></td>
                            <td><?= ucfirst($usuario['rol']) ?></td>
                            <td>
                                <span class="estado-badge <?= $usuario['bloqueado'] ? 'estado-pendiente' : 'estado-completada' ?>">
                                    <?= $usuario['bloqueado'] ? 'Bloqueado' : 'Activo' ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($usuario['foto']): ?>
                                    <img src="<?= $urlFotos . $usuario['foto'] ?>" alt="Foto" width="50">
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                            <td class="actions">
                                <a href="index.php?editar=<?= $usuario['id'] ?>" class="btn btn-primary">Editar</a>
                                <?php if ($usuario['id'] !== $_SESSION['user']['id']): ?>
                                    <?php if ($usuario['bloqueado']): ?>
                                        <a href="index.php?accion=desbloquear&id=<?= $usuario['id'] ?>" class="btn btn-small btn-success">Desbloquear</a>
                                    <?php else: ?>
                                        <a href="index.php?accion=bloquear&id=<?= $usuario['id'] ?>" class="btn btn-small btn-danger">Bloquear</a>
                                    <?php endif; ?>
                                    <a href="index.php?borrar=<?= $usuario['id'] ?>" class="btn btn-small btn-danger" onclick="return confirm('¿Estás seguro de que deseas eliminar este usuario?');">Borrar</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php include '../../components/footer.php'; ?>
</body>

</html>