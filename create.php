<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $content = $_POST['content'];

    $stmt = $conn->prepare("INSERT INTO posts (title, content) VALUES (?, ?)");
    $stmt->bind_param("ss", $title, $content);
    $stmt->execute();

    header("Location: dashboard.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Create Post</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">
    <h2>Create New Post</h2>

    <form method="post">
        <label>Title:</label>
        <input type="text" name="title" required>

        <label>Content:</label>
        <textarea name="content" rows="6" required></textarea>

        <input type="submit" value="Create Post">
    </form>

    <div style="text-align: center; margin-top: 20px;">
        <a href="dashboard.php" class="button">← Back to Dashboard</a>
    </div>
</div>

</body>
</html>
