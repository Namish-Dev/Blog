<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

include 'db.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: dashboard.php");
    exit;
}

$stmt = $conn->prepare("SELECT * FROM posts WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$post = $stmt->get_result()->fetch_assoc();

if (!$post) {
    echo "Post not found.";
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $content = $_POST['content'];

    $update = $conn->prepare("UPDATE posts SET title = ?, content = ? WHERE id = ?");
    $update->bind_param("ssi", $title, $content, $id);
    $update->execute();

    header("Location: dashboard.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Post</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">
    <h2>Edit Post</h2>

    <form method="post">
        <label>Title:</label>
        <input type="text" name="title" value="<?= htmlspecialchars($post['title']) ?>" required>

        <label>Content:</label>
        <textarea name="content" rows="6" required><?= htmlspecialchars($post['content']) ?></textarea>

        <input type="submit" value="Update Post">
    </form>

    <div style="text-align: center; margin-top: 20px;">
        <a href="dashboard.php" class="button">← Back to Dashboard</a>
    </div>
</div>

</body>
</html>
