<?php
require 'config.php';

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$genreFilter = isset($_GET['genre']) ? $_GET['genre'] : 'all';
$statusFilter = isset($_GET['status']) ? $_GET['status'] : 'all';

// Fetch books with optional search, genre and status filters
$sql = "SELECT * FROM books WHERE 1=1";
$params = [];

if ($search !== '') {
    $sql .= " AND (title LIKE :search OR author LIKE :search)";
    $params[':search'] = '%' . $search . '%';
}
if ($genreFilter !== 'all') {
    $sql .= " AND genre = :genre";
    $params[':genre'] = $genreFilter;
}
if ($statusFilter !== 'all') {
    $sql .= " AND status = :status";
    $params[':status'] = $statusFilter;
}
$sql .= " ORDER BY created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$books = $stmt->fetchAll();

// Library stats
$totalBooks = (int)$pdo->query("SELECT COUNT(*) FROM books")->fetchColumn();
$finishedBooks = (int)$pdo->query("SELECT COUNT(*) FROM books WHERE status = 'finished'")->fetchColumn();
$readingBooks = (int)$pdo->query("SELECT COUNT(*) FROM books WHERE status = 'reading'")->fetchColumn();
$avgRating = $pdo->query("SELECT ROUND(AVG(rating), 1) FROM books WHERE rating IS NOT NULL")->fetchColumn();
$pagesRead = (int)$pdo->query("SELECT COALESCE(SUM(pages), 0) FROM books WHERE status = 'finished'")->fetchColumn();

$progress = $totalBooks > 0 ? round(($finishedBooks / $totalBooks) * 100) : 0;

$genres = ['fiction' => 'Fiction', 'non-fiction' => 'Non-Fiction', 'science' => 'Science', 'history' => 'History', 'biography' => 'Biography', 'fantasy' => 'Fantasy', 'self-help' => 'Self-Help', 'mystery' => 'Mystery'];
$statuses = ['to-read' => 'To Read', 'reading' => 'Reading', 'finished' => 'Finished'];

function stars($rating) {
    if ($rating === null) return '<span class="text-muted small">Not rated</span>';
    $out = '';
    for ($i = 1; $i <= 5; $i++) {
        $out .= $i <= $rating ? '&#9733;' : '&#9734;';
    }
    return '<span class="stars">' . $out . '</span>';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BookNest - Personal Library Tracker</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<div class="container py-4">
    <header class="text-center mb-4">
        <h1 class="app-title">BookNest</h1>
        <p class="text-muted">Your personal library, neatly on one shelf.</p>
    </header>

    <!-- Library stats -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card stat-card shadow-sm text-center"><div class="card-body">
                <h3 class="mb-0"><?php echo $totalBooks; ?></h3><small class="text-muted">Total books</small>
            </div></div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card stat-card shadow-sm text-center"><div class="card-body">
                <h3 class="mb-0 text-success"><?php echo $finishedBooks; ?></h3><small class="text-muted">Finished</small>
            </div></div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card stat-card shadow-sm text-center"><div class="card-body">
                <h3 class="mb-0 text-primary"><?php echo $readingBooks; ?></h3><small class="text-muted">Reading now</small>
            </div></div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card stat-card shadow-sm text-center"><div class="card-body">
                <h3 class="mb-0 text-warning"><?php echo $avgRating !== null ? $avgRating : '-'; ?></h3><small class="text-muted">Average rating</small>
            </div></div>
        </div>
    </div>

    <!-- Reading progress -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h5 class="card-title mb-0">Reading Progress</h5>
                <span class="badge bg-primary fs-6"><?php echo $finishedBooks; ?> / <?php echo $totalBooks; ?> finished</span>
            </div>
            <div class="progress" style="height: 22px;">
                <div class="progress-bar bg-success" role="progressbar" style="width: <?php echo $progress; ?>%;" aria-valuenow="<?php echo $progress; ?>" aria-valuemin="0" aria-valuemax="100"><?php echo $progress; ?>%</div>
            </div>
            <p class="text-muted small mb-0 mt-2"><?php echo number_format($pagesRead); ?> pages read across finished books.</p>
        </div>
    </div>

    <!-- Add book form -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h5 class="card-title">Add a Book</h5>
            <form action="add_book.php" method="POST" class="row g-3">
                <div class="col-md-4">
                    <input type="text" name="title" class="form-control" placeholder="Book title" required maxlength="255">
                </div>
                <div class="col-md-3">
                    <input type="text" name="author" class="form-control" placeholder="Author" required maxlength="255">
                </div>
                <div class="col-md-2">
                    <select name="genre" class="form-select">
                        <?php foreach ($genres as $key => $label): ?>
                            <option value="<?php echo $key; ?>"><?php echo $label; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-1">
                    <select name="status" class="form-select">
                        <?php foreach ($statuses as $key => $label): ?>
                            <option value="<?php echo $key; ?>"><?php echo $label; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-1">
                    <input type="number" name="pages" class="form-control" placeholder="Pages" min="0" value="0">
                </div>
                <div class="col-md-1 d-grid">
                    <button type="submit" class="btn btn-primary">Add</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Search and filter -->
    <form method="GET" class="row g-3 mb-4">
        <div class="col-md-5">
            <input type="search" name="search" class="form-control" placeholder="Search by title or author..." value="<?php echo htmlspecialchars($search); ?>">
        </div>
        <div class="col-md-3">
            <select name="genre" class="form-select" onchange="this.form.submit()">
                <option value="all" <?php echo $genreFilter === 'all' ? 'selected' : ''; ?>>All genres</option>
                <?php foreach ($genres as $key => $label): ?>
                    <option value="<?php echo $key; ?>" <?php echo $genreFilter === $key ? 'selected' : ''; ?>><?php echo $label; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2">
            <select name="status" class="form-select" onchange="this.form.submit()">
                <option value="all" <?php echo $statusFilter === 'all' ? 'selected' : ''; ?>>All statuses</option>
                <?php foreach ($statuses as $key => $label): ?>
                    <option value="<?php echo $key; ?>" <?php echo $statusFilter === $key ? 'selected' : ''; ?>><?php echo $label; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2 d-grid">
            <button type="submit" class="btn btn-outline-secondary">Search</button>
        </div>
    </form>

    <!-- Book list -->
    <?php if (empty($books)): ?>
        <div class="alert alert-info text-center">No books found. Add your first book above to start your library.</div>
    <?php else: ?>
        <div class="row g-3">
            <?php foreach ($books as $book): ?>
                <div class="col-md-6">
                    <div class="card book-card shadow-sm <?php echo $book['status'] === 'finished' ? 'book-finished' : ''; ?>">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <h5 class="card-title mb-1"><?php echo htmlspecialchars($book['title']); ?></h5>
                                <span class="badge <?php echo $book['status'] === 'finished' ? 'bg-success' : ($book['status'] === 'reading' ? 'bg-primary' : 'bg-secondary'); ?>"><?php echo $statuses[$book['status']]; ?></span>
                            </div>
                            <p class="text-muted mb-1">by <?php echo htmlspecialchars($book['author']); ?></p>
                            <p class="mb-2">
                                <span class="badge bg-info text-dark"><?php echo $genres[$book['genre']]; ?></span>
                                <?php echo stars($book['rating']); ?>
                                <?php if ($book['pages'] > 0): ?><span class="text-muted small ms-1"><?php echo $book['pages']; ?> pages</span><?php endif; ?>
                            </p>
                            <div class="d-flex flex-wrap gap-2">
                                <?php if ($book['status'] !== 'reading'): ?>
                                    <a href="update_status.php?id=<?php echo $book['id']; ?>&status=reading" class="btn btn-sm btn-outline-primary">Start reading</a>
                                <?php endif; ?>
                                <?php if ($book['status'] !== 'finished'): ?>
                                    <a href="update_status.php?id=<?php echo $book['id']; ?>&status=finished" class="btn btn-sm btn-outline-success">Mark finished</a>
                                <?php endif; ?>
                                <a href="edit_book.php?id=<?php echo $book['id']; ?>" class="btn btn-sm btn-outline-secondary">Edit</a>
                                <a href="delete_book.php?id=<?php echo $book['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirmDelete();">Delete</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <footer class="text-center text-muted small mt-5">
        Built by Hammad with HTML, CSS, JavaScript, Bootstrap, MySQL and PHP
    </footer>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/script.js"></script>
</body>
</html>
