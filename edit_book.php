<?php
require 'config.php';

$id = (int)($_GET['id'] ?? 0);
$genres = ['fiction' => 'Fiction', 'non-fiction' => 'Non-Fiction', 'science' => 'Science', 'history' => 'History', 'biography' => 'Biography', 'fantasy' => 'Fantasy', 'self-help' => 'Self-Help', 'mystery' => 'Mystery'];
$statuses = ['to-read' => 'To Read', 'reading' => 'Reading', 'finished' => 'Finished'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    $title = trim($_POST['title'] ?? '');
    $author = trim($_POST['author'] ?? '');
    $genre = $_POST['genre'] ?? 'fiction';
    $status = $_POST['status'] ?? 'to-read';
    $rating = isset($_POST['rating']) && $_POST['rating'] !== '' ? (int)$_POST['rating'] : null;
    $pages = (int)($_POST['pages'] ?? 0);

    if (!in_array($genre, array_keys($genres), true)) {
        $genre = 'fiction';
    }
    if (!in_array($status, array_keys($statuses), true)) {
        $status = 'to-read';
    }
    if ($rating !== null && ($rating < 1 || $rating > 5)) {
        $rating = null;
    }
    if ($pages < 0) {
        $pages = 0;
    }

    if ($id > 0 && $title !== '' && $author !== '') {
        $stmt = $pdo->prepare("UPDATE books SET title = :title, author = :author, genre = :genre, status = :status, rating = :rating, pages = :pages WHERE id = :id");
        $stmt->execute([':title' => $title, ':author' => $author, ':genre' => $genre, ':status' => $status, ':rating' => $rating, ':pages' => $pages, ':id' => $id]);
    }
    header('Location: index.php');
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM books WHERE id = :id");
$stmt->execute([':id' => $id]);
$book = $stmt->fetch();

if (!$book) {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Book - BookNest</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h4 class="card-title mb-3">Edit Book</h4>
                    <form action="edit_book.php" method="POST">
                        <input type="hidden" name="id" value="<?php echo $book['id']; ?>">
                        <div class="mb-3">
                            <label class="form-label">Book title</label>
                            <input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($book['title']); ?>" required maxlength="255">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Author</label>
                            <input type="text" name="author" class="form-control" value="<?php echo htmlspecialchars($book['author']); ?>" required maxlength="255">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Genre</label>
                            <select name="genre" class="form-select">
                                <?php foreach ($genres as $key => $label): ?>
                                    <option value="<?php echo $key; ?>" <?php echo $book['genre'] === $key ? 'selected' : ''; ?>><?php echo $label; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <?php foreach ($statuses as $key => $label): ?>
                                    <option value="<?php echo $key; ?>" <?php echo $book['status'] === $key ? 'selected' : ''; ?>><?php echo $label; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Rating (1 to 5)</label>
                                <input type="number" name="rating" class="form-control" min="1" max="5" value="<?php echo $book['rating']; ?>" placeholder="No rating">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Pages</label>
                                <input type="number" name="pages" class="form-control" min="0" value="<?php echo $book['pages']; ?>">
                            </div>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">Save changes</button>
                            <a href="index.php" class="btn btn-outline-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
