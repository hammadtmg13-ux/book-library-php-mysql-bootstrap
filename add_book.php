<?php
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $author = trim($_POST['author'] ?? '');
    $genre = $_POST['genre'] ?? 'fiction';
    $status = $_POST['status'] ?? 'to-read';
    $rating = isset($_POST['rating']) && $_POST['rating'] !== '' ? (int)$_POST['rating'] : null;
    $pages = (int)($_POST['pages'] ?? 0);

    $allowedGenres = ['fiction', 'non-fiction', 'science', 'history', 'biography', 'fantasy', 'self-help', 'mystery'];
    if (!in_array($genre, $allowedGenres, true)) {
        $genre = 'fiction';
    }
    $allowedStatus = ['to-read', 'reading', 'finished'];
    if (!in_array($status, $allowedStatus, true)) {
        $status = 'to-read';
    }
    if ($rating !== null && ($rating < 1 || $rating > 5)) {
        $rating = null;
    }
    if ($pages < 0) {
        $pages = 0;
    }

    if ($title !== '' && $author !== '') {
        $stmt = $pdo->prepare("INSERT INTO books (title, author, genre, status, rating, pages) VALUES (:title, :author, :genre, :status, :rating, :pages)");
        $stmt->execute([':title' => $title, ':author' => $author, ':genre' => $genre, ':status' => $status, ':rating' => $rating, ':pages' => $pages]);
    }
}

header('Location: index.php');
exit;
