<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

include 'db.php';

$id = $_GET['id'] ?? null;
if (!$id || !is_numeric($id)) {
    header("Location: dashboard.php");
    exit;
}

// Fetch the post
$stmt = $conn->prepare("SELECT * FROM posts WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$post = $stmt->get_result()->fetch_assoc();

if (!$post) {
    echo "Post not found.";
    exit;
}

$update_success = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);

    $update = $conn->prepare("UPDATE posts SET title = ?, content = ? WHERE id = ?");
    $update->bind_param("ssi", $title, $content, $id);
    
    if ($update->execute()) {
        $update_success = true;
        header("Location: dashboard.php");
        exit;
    } else {
        $error_message = "Failed to update the post. Try again.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Post</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #181a1b;
            color: #e0e0e0;
        }
        .form-control {
            background-color: #23272b;
            color: #e0e0e0;
            border: 1px solid #444;
        }
        .form-control:focus {
            background-color: #23272b;
            color: #fff;
            border-color: #0d6efd;
        }
        .btn-primary {
            background-color: #0d6efd;
            border: none;
        }
        .btn-primary:hover {
            background-color: #0b5ed7;
        }
        .card {
            background-color: #23272b;
            border: 1px solid #343a40;
        }
    </style>
</head>
<body>

<div class="container d-flex justify-content-center align-items-center" style="min-height: 100vh;">
    <div class="col-md-8">
        <div class="card p-4 shadow">
            <h3 class="mb-4 text-center">Edit Post</h3>

            <?php if (!empty($error_message)): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error_message) ?></div>
            <?php endif; ?>

            <form method="post" novalidate>
                <div class="mb-3">
                    <label class="form-label">Title:</label>
                    <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($post['title']) ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Content:</label>
                    <textarea name="content" rows="6" class="form-control" required><?= htmlspecialchars($post['content']) ?></textarea>
                </div>

                <button type="submit" class="btn btn-primary w-100">Update Post</button>
            </form>

            <div class="text-center mt-3">
                <a href="dashboard.php" class="btn btn-outline-light">← Back to Dashboard</a>
            </div>
        </div>
    </div>
</div>

</body>
</html>
