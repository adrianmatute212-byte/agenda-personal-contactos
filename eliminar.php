<?php
require_once __DIR__ . '/config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    if ($id) {
        $stmt = $pdo->prepare('DELETE FROM contactos WHERE id = :id');
        $stmt->execute([':id' => $id]);
        header('Location: index.php?msg=eliminado');
        exit;
    }
}

header('Location: index.php');
exit;
