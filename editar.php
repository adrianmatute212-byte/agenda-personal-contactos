<?php
require_once __DIR__ . '/config/db.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    header('Location: index.php');
    exit;
}

$stmt = $pdo->prepare('SELECT id, nombre, telefono, correo, ciudad, favorito FROM contactos WHERE id = :id');
$stmt->execute([':id' => $id]);
$contacto = $stmt->fetch();

if (!$contacto) {
    header('Location: index.php');
    exit;
}

$errores  = [];
$nombre   = $contacto['nombre'];
$telefono = $contacto['telefono'];
$correo   = $contacto['correo'];
$ciudad   = $contacto['ciudad'];
$favorito = (int) $contacto['favorito'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre   = trim($_POST['nombre'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    $correo   = trim($_POST['correo'] ?? '');
    $ciudad   = trim($_POST['ciudad'] ?? '');
    $favorito = isset($_POST['favorito']) && $_POST['favorito'] === '1' ? 1 : 0;

    if ($nombre === '') {
        $errores[] = 'El nombre es obligatorio.';
    }
    if ($telefono === '') {
        $errores[] = 'El teléfono es obligatorio.';
    }
    if ($correo === '' || !filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $errores[] = 'Ingresa un correo electrónico válido.';
    }
    if ($ciudad === '') {
        $errores[] = 'La ciudad es obligatoria.';
    }

    if (empty($errores)) {
        $update = $pdo->prepare(
            'UPDATE contactos
             SET nombre = :nombre, telefono = :telefono, correo = :correo, ciudad = :ciudad, favorito = :favorito
             WHERE id = :id'
        );
        $update->execute([
            ':nombre'   => $nombre,
            ':telefono' => $telefono,
            ':correo'   => $correo,
            ':ciudad'   => $ciudad,
            ':favorito' => $favorito,
            ':id'       => $id,
        ]);

        header('Location: index.php?msg=editado');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar contacto - Agenda Personal</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <header class="topbar">
        <div class="container topbar-inner">
            <div>
                <h1>Editar contacto</h1>
                <p class="subtitle">Actualiza los datos de <?= e($contacto['nombre']) ?></p>
            </div>
            <a href="index.php" class="btn btn-ghost-light">&larr; Volver a la lista</a>
        </div>
    </header>

    <main class="container container-narrow">
        <section class="card form-card">
            <?php if (!empty($errores)): ?>
                <div class="alert alert-error">
                    <ul>
                        <?php foreach ($errores as $err): ?>
                            <li><?= e($err) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form method="POST" action="editar.php?id=<?= (int) $id ?>" class="contact-form">
                <div class="form-group">
                    <label for="nombre">Nombre completo *</label>
                    <input type="text" id="nombre" name="nombre" value="<?= e($nombre) ?>" required class="input">
                </div>

                <div class="form-group">
                    <label for="telefono">Teléfono *</label>
                    <input type="text" id="telefono" name="telefono" value="<?= e($telefono) ?>" required class="input">
                </div>

                <div class="form-group">
                    <label for="correo">Correo electrónico *</label>
                    <input type="email" id="correo" name="correo" value="<?= e($correo) ?>" required class="input">
                </div>

                <div class="form-group">
                    <label for="ciudad">Ciudad *</label>
                    <input type="text" id="ciudad" name="ciudad" value="<?= e($ciudad) ?>" required class="input">
                </div>

                <div class="form-group checkbox-group">
                    <label class="checkbox-label">
                        <input type="checkbox" name="favorito" value="1" <?= $favorito === 1 ? 'checked' : '' ?>>
                        <span>&#9733; Marcar como contacto favorito (Sí)</span>
                    </label>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Guardar cambios</button>
                    <a href="index.php" class="btn btn-ghost">Cancelar</a>
                </div>
            </form>
        </section>
    </main>
</body>
</html>
