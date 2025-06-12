<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

include 'db.php';

$id = $_GET['id'] ?? null;

if (!$id || !ctype_digit($id)) {
    header("Location: dashboard.php");
    exit;
}

// Step 1: Fetch post
$stmt = $conn->prepare("SELECT * FROM posts WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$post = $stmt->get_result()->fetch_assoc();

if (!$post) {
    echo "Post not found.";
    exit;
}

// Step 2: On POST, delete
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $del = $conn->prepare("DELETE FROM posts WHERE id = ?");
    $del->bind_param("i", $id);
    $del->execute();

    header("Location: dashboard.php?deleted=1");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Delete Post</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #181a1b;
            color: #e0e0e0;
        }
        .card {
            background-color: #23272b;
            border: 1px solid #343a40;
        }
        .btn-danger {
            background-color: #dc3545;
            border: none;
        }
        .btn-danger:hover {
            background-color: #c82333;
        }
        .btn-secondary {
            background-color: #6c757d;
        }
    </style>
</head>
<body>

<div class="container d-flex justify-content-center align-items-center" style="min-height: 100vh;">
    <div class="col-md-6">
        <div class="card p-4 shadow">
            <h3 class="mb-3 text-center">Confirm Delete</h3>
            <p class="text-center">Are you sure you want to delete the post titled:</p>
            <h5 class="text-center text-danger">"<?= htmlspecialchars($post['title']) ?>"</h5>

            <form method="post" class="text-center mt-4">
                <button type="submit" class="btn btn-danger">Yes, Delete</button>
                <a href="dashboard.php" class="btn btn-secondary ms-3">Cancel</a>
            </form>
        </div>
    </div>
</div>

</body>
</html>
