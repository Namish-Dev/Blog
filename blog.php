<?php
include 'db.php';

// Optional: search functionality
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$limit = 5;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $limit;

// Count total approved posts
if ($search) {
    $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM posts WHERE is_approved = 1 AND (title LIKE ? OR content LIKE ?)");
    $like = "%$search%";
    $stmt->bind_param("ss", $like, $like);
} else {
    $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM posts WHERE is_approved = 1");
}
$stmt->execute();
$result = $stmt->get_result();
$total = $result->fetch_assoc()['total'];
$stmt->close();

$total_pages = ceil($total / $limit);

// Fetch posts
if ($search) {
    $stmt = $conn->prepare("SELECT * FROM posts WHERE is_approved = 1 AND (title LIKE ? OR content LIKE ?) ORDER BY created_at DESC LIMIT ?, ?");
    $stmt->bind_param("ssii", $like, $like, $start, $limit);
} else {
    $stmt = $conn->prepare("SELECT * FROM posts WHERE is_approved = 1 ORDER BY created_at DESC LIMIT ?, ?");
    $stmt->bind_param("ii", $start, $limit);
}
$stmt->execute();
$posts = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Blog Posts</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #181a1b;
            color: #e0e0e0;
        }
        .card {
            background-color: #23272b;
            color: #e0e0e0;
            border: 1px solid #343a40;
        }
        .form-control {
            background-color: #23272b;
            color: #e0e0e0;
            border: 1px solid #444;
        }
    </style>
</head>
<body>

<div class="container mt-5">
    <h2 class="text-center mb-4">📰 Public Blog Posts</h2>

    <!-- Search -->
    <form method="get" class="input-group mb-4">
        <input type="text" name="search" class="form-control" placeholder="Search posts..."
               value="<?= htmlspecialchars($search) ?>">
        <button type="submit" class="btn btn-primary">Search</button>
    </form>

    <!-- Display posts -->
    <?php if ($posts->num_rows > 0): ?>
        <?php while ($row = $posts->fetch_assoc()): ?>
            <div class="card mb-4 shadow">
                <div class="card-body">
                    <h4 class="card-title"><?= htmlspecialchars($row['title']) ?></h4>
                    <p class="card-text"><?= nl2br(htmlspecialchars($row['content'])) ?></p>
                    <small class="text-muted">Posted on <?= $row['created_at'] ?></small>
                </div>
            </div>
        <?php endwhile; ?>

        <!-- Pagination -->
        <nav>
            <ul class="pagination justify-content-center">
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                        <a class="page-link bg-dark text-light" href="?page=<?= $i ?>&search=<?= urlencode($search) ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>

    <?php else: ?>
        <div class="alert alert-info text-center">No approved posts found.</div>
    <?php endif; ?>
</div>

</body>
</html>
