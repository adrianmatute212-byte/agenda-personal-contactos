<?php
require_once __DIR__ . '/config/db.php';

$errores = [];
$nombre   = '';
$telefono = '';
$correo   = '';
$ciudad   = '';
$favorito = 0;

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
        $stmt = $pdo->prepare(
            'INSERT INTO contactos (nombre, telefono, correo, ciudad, favorito)
             VALUES (:nombre, :telefono, :correo, :ciudad, :favorito)'
        );
        $stmt->execute([
            ':nombre'   => $nombre,
            ':telefono' => $telefono,
            ':correo'   => $correo,
            ':ciudad'   => $ciudad,
            ':favorito' => $favorito,
        ]);

        header('Location: index.php?msg=creado');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuevo contacto - Agenda Personal</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <header class="topbar">
        <div class="container topbar-inner">
            <div>
                <h1>Nuevo contacto</h1>
                <p class="subtitle">Registra un nuevo contacto en tu agenda</p>
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

            <form method="POST" action="crear.php" class="contact-form">
                <div class="form-group">
                    <label for="nombre">Nombre completo *</label>
                    <input type="text" id="nombre" name="nombre" value="<?= e($nombre) ?>" required class="input" placeholder="Ej. Ana Gabriela Ortiz">
                </div>

                <div class="form-group">
                    <label for="telefono">Teléfono *</label>
                    <input type="text" id="telefono" name="telefono" value="<?= e($telefono) ?>" required class="input" placeholder="Ej. 0991234567">
                </div>

                <div class="form-group">
                    <label for="correo">Correo electrónico *</label>
                    <input type="email" id="correo" name="correo" value="<?= e($correo) ?>" required class="input" placeholder="Ej. correo@ejemplo.com">
                </div>

                <div class="form-group">
                    <label for="ciudad">Ciudad *</label>
                    <input type="text" id="ciudad" name="ciudad" value="<?= e($ciudad) ?>" required class="input" placeholder="Ej. Cuenca">
                </div>

                <div class="form-group checkbox-group">
                    <label class="checkbox-label">
                        <input type="checkbox" name="favorito" value="1" <?= $favorito === 1 ? 'checked' : '' ?>>
                        <span>&#9733; Marcar como contacto favorito (Sí)</span>
                    </label>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Guardar contacto</button>
                    <a href="index.php" class="btn btn-ghost">Cancelar</a>
                </div>
            </form>
        </section>
    </main>
</body>
</html>
