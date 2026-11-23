<?php
require_once __DIR__ . '/config/db.php';

// Parámetros de Opción A (Búsqueda) y Opción D (Filtro de favoritos)
$busqueda = trim($_GET['q'] ?? '');
$campo = $_GET['campo'] ?? 'todos';
$soloFavoritos = isset($_GET['favoritos']) && $_GET['favoritos'] === '1';

$camposPermitidos = ['todos', 'nombre', 'correo', 'ciudad'];
if (!in_array($campo, $camposPermitidos, true)) {
    $campo = 'todos';
}

// Construcción dinámica segura de la consulta con sentencias preparadas
$condiciones = [];
$parametros = [];

if ($busqueda !== '') {
    $like = '%' . $busqueda . '%';
    if ($campo === 'nombre') {
        $condiciones[] = 'nombre LIKE :q';
        $parametros[':q'] = $like;
    } elseif ($campo === 'correo') {
        $condiciones[] = 'correo LIKE :q';
        $parametros[':q'] = $like;
    } elseif ($campo === 'ciudad') {
        $condiciones[] = 'ciudad LIKE :q';
        $parametros[':q'] = $like;
    } else {
        $condiciones[] = '(nombre LIKE :q1 OR correo LIKE :q2 OR ciudad LIKE :q3)';
        $parametros[':q1'] = $like;
        $parametros[':q2'] = $like;
        $parametros[':q3'] = $like;
    }
}

if ($soloFavoritos) {
    $condiciones[] = 'favorito = 1';
}

$sql = 'SELECT id, nombre, telefono, correo, ciudad, favorito, fecha_creacion FROM contactos';
if (!empty($condiciones)) {
    $sql .= ' WHERE ' . implode(' AND ', $condiciones);
}
$sql .= ' ORDER BY favorito DESC, nombre ASC';

$stmt = $pdo->prepare($sql);
$stmt->execute($parametros);
$contactos = $stmt->fetchAll();

// Contadores generales para las pestañas
$totales = $pdo->query('SELECT COUNT(*) AS total, SUM(CASE WHEN favorito = 1 THEN 1 ELSE 0 END) AS favoritos FROM contactos')->fetch();
$totalContactos = (int) ($totales['total'] ?? 0);
$totalFavoritos = (int) ($totales['favoritos'] ?? 0);

// Mensajes de confirmación tras acciones
$mensajes = [
    'creado'    => 'Contacto registrado correctamente.',
    'editado'   => 'Contacto actualizado correctamente.',
    'eliminado' => 'Contacto eliminado correctamente.',
    'favorito'  => 'Estado de favorito actualizado.',
];
$alerta = $mensajes[$_GET['msg'] ?? ''] ?? '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agenda Personal de Contactos</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <header class="topbar">
        <div class="container topbar-inner">
            <div>
                <h1>Agenda Personal de Contactos</h1>
                <p class="subtitle">Gestión de contactos con búsqueda inteligente y favoritos</p>
            </div>
            <a href="crear.php" class="btn btn-primary">+ Nuevo contacto</a>
        </div>
    </header>

    <main class="container">
        <?php if ($alerta !== ''): ?>
            <div class="alert alert-success" role="status">
                <?= e($alerta) ?>
            </div>
        <?php endif; ?>

        <!-- Barra de filtros: Opción D (Todos vs Solo Favoritos) y Opción A (Búsqueda) -->
        <section class="toolbar" aria-label="Filtros y búsqueda">
            <div class="tabs" role="tablist">
                <a
                    href="index.php<?= $busqueda !== '' ? '?q=' . urlencode($busqueda) . '&campo=' . urlencode($campo) : '' ?>"
                    class="tab <?= !$soloFavoritos ? 'active' : '' ?>"
                >
                    Todos <span class="badge"><?= $totalContactos ?></span>
                </a>
                <a
                    href="index.php?favoritos=1<?= $busqueda !== '' ? '&q=' . urlencode($busqueda) . '&campo=' . urlencode($campo) : '' ?>"
                    class="tab <?= $soloFavoritos ? 'active' : '' ?>"
                >
                    &#9733; Solo favoritos <span class="badge"><?= $totalFavoritos ?></span>
                </a>
            </div>

            <form method="GET" action="index.php" class="search-form">
                <?php if ($soloFavoritos): ?>
                    <input type="hidden" name="favoritos" value="1">
                <?php endif; ?>

                <select name="campo" aria-label="Buscar en campo" class="input select-campo">
                    <option value="todos" <?= $campo === 'todos' ? 'selected' : '' ?>>Todos (Nombre, Correo, Ciudad)</option>
                    <option value="nombre" <?= $campo === 'nombre' ? 'selected' : '' ?>>Solo por Nombre</option>
                    <option value="correo" <?= $campo === 'correo' ? 'selected' : '' ?>>Solo por Correo</option>
                    <option value="ciudad" <?= $campo === 'ciudad' ? 'selected' : '' ?>>Solo por Ciudad</option>
                </select>

                <input
                    type="text"
                    name="q"
                    value="<?= e($busqueda) ?>"
                    placeholder="Buscar por nombre, correo o ciudad..."
                    class="input search-input"
                    aria-label="Término de búsqueda"
                >

                <button type="submit" class="btn btn-secondary">Buscar</button>

                <?php if ($busqueda !== '' || $soloFavoritos): ?>
                    <a href="index.php" class="btn btn-ghost">Limpiar</a>
                <?php endif; ?>
            </form>
        </section>

        <!-- Resultados -->
        <section class="card">
            <div class="card-header">
                <h2>
                    <?php if ($soloFavoritos): ?>
                        Contactos favoritos
                    <?php else: ?>
                        Lista de contactos
                    <?php endif; ?>
                    <span class="count-text">(<?= count($contactos) ?> encontrados)</span>
                </h2>
            </div>

            <?php if (empty($contactos)): ?>
                <div class="empty-state">
                    <p>No se encontraron contactos con los criterios seleccionados.</p>
                    <?php if ($busqueda !== '' || $soloFavoritos): ?>
                        <a href="index.php" class="btn btn-secondary">Ver todos los contactos</a>
                    <?php else: ?>
                        <a href="crear.php" class="btn btn-primary">Registrar primer contacto</a>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Favorito</th>
                                <th>Nombre</th>
                                <th>Teléfono</th>
                                <th>Correo</th>
                                <th>Ciudad</th>
                                <th class="col-actions">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($contactos as $c): ?>
                                <?php $esFav = (int) $c['favorito'] === 1; ?>
                                <tr>
                                    <td data-label="Favorito">
                                        <form method="POST" action="toggle_favorito.php" class="inline-form">
                                            <input type="hidden" name="id" value="<?= (int) $c['id'] ?>">
                                            <input type="hidden" name="redirect" value="<?= e($_SERVER['REQUEST_URI']) ?>">
                                            <button
                                                type="submit"
                                                class="fav-btn <?= $esFav ? 'is-fav' : '' ?>"
                                                title="<?= $esFav ? 'Quitar de favoritos' : 'Marcar como favorito' ?>"
                                            >
                                                <span class="star"><?= $esFav ? '&#9733;' : '&#9734;' ?></span>
                                                <span class="fav-label"><?= $esFav ? 'Sí' : 'No' ?></span>
                                            </button>
                                        </form>
                                    </td>
                                    <td data-label="Nombre" class="cell-name"><?= e($c['nombre']) ?></td>
                                    <td data-label="Teléfono"><?= e($c['telefono']) ?></td>
                                    <td data-label="Correo">
                                        <a href="mailto:<?= e($c['correo']) ?>" class="mail-link"><?= e($c['correo']) ?></a>
                                    </td>
                                    <td data-label="Ciudad"><?= e($c['ciudad']) ?></td>
                                    <td data-label="Acciones" class="col-actions">
                                        <a href="editar.php?id=<?= (int) $c['id'] ?>" class="btn btn-sm btn-secondary">Editar</a>
                                        <form
                                            method="POST"
                                            action="eliminar.php"
                                            class="inline-form"
                                            onsubmit="return confirm('¿Seguro que deseas eliminar a <?= e($c['nombre']) ?>?');"
                                        >
                                            <input type="hidden" name="id" value="<?= (int) $c['id'] ?>">
                                            <button type="submit" class="btn btn-sm btn-danger">Eliminar</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </section>
    </main>

    <footer class="footer">
        <div class="container">
            Agenda Personal de Contactos &middot; PHP &amp; MySQL
        </div>
    </footer>
</body>
</html>
