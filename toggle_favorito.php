<?php
require_once __DIR__ . '/config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    if ($id) {
        $stmt = $pdo->prepare('UPDATE contactos SET favorito = IF(favorito = 1, 0, 1) WHERE id = :id');
        $stmt->execute([':id' => $id]);
    }
}

header('Location: index.php?msg=favorito');
exit;
