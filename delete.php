<?php
require_once 'lib/db.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if ($id) {
    $stmt = $pdo->prepare("DELETE FROM transactions WHERE id = :id");
    $stmt->execute([':id' => $id]);
}

header('Location: index.php?success=1');
exit;