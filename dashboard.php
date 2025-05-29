<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}
include 'db.php';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">
    <h2>Welcome, <?= htmlspecialchars($_SESSION['user']) ?>!</h2>

    <div class="top-links">
        <a href="create.php" class="button"> New Post +</a>
        <a href="logout.php" class="button logout">Logout</a>
    </div>

    <hr>

    <?php
    $result = $conn->query("SELECT * FROM posts ORDER BY created_at DESC");

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<div class='post'>";
            echo "<h3>" . htmlspecialchars($row['title']) . "</h3>";
            echo "<p>" . nl2br(htmlspecialchars($row['content'])) . "</p>";
            echo "<small>Posted on " . $row['created_at'] . "</small><br>";
            echo "<a href='edit.php?id={$row['id']}' class='btn edit'>Edit</a> ";
            echo "<a href='delete.php?id={$row['id']}' class='btn delete'>Delete</a><hr>";
            echo "</div>";
        }
    } else {
        echo "<p>No posts yet. <a href='create.php'>Add one</a>.</p>";
    }
    ?>
</div>

</body>
</html>
