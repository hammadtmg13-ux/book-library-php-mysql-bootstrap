<?php
require 'config.php';

$id = (int)($_GET['id'] ?? 0);
$status = $_GET['status'] ?? '';

$allowed = ['to-read', 'reading', 'finished'];
if ($id > 0 && in_array($status, $allowed, true)) {
    $stmt = $pdo->prepare("UPDATE books SET status = :status WHERE id = :id");
    $stmt->execute([':status' => $status, ':id' => $id]);
}

header('Location: index.php');
exit;
