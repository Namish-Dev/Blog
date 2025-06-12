<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}
include 'db.php';

$error_message = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST['title']); // Remove unnecessary whitespace
    $content = trim($_POST['content']);

    // Server-side validation to ensure fields are not empty
    if (empty($title) || empty($content)) {
        $error_message = "Both title and content are required.";
    } else {
        // Secure insertion with prepared statements
        $stmt = $conn->prepare("INSERT INTO posts (title, content) VALUES (?, ?)");
        $stmt->bind_param("ss", $title, $content);
        $stmt->execute();

        header("Location: dashboard.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Create Post</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #181a1b;
            color: #e0e0e0;
        }
        .container {
            max-width: 600px;
            margin-top: 60px;
            background-color: #23272b;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 10px #000;
        }
        input[type="text"], textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            background-color: #2c3034;
            border: 1px solid #444;
            color: #e0e0e0;
        }
        input[type="submit"], .button {
            background-color: #0d6efd;
            border: none;
            padding: 10px 20px;
            color: #fff;
            border-radius: 5px;
            text-decoration: none;
        }
        input[type="submit"]:hover, .button:hover {
            background-color: #0b5ed7;
        }
        label {
            margin-bottom: 5px;
            font-weight: bold;
        }
        .alert {
            background-color: #2c3034;
            color: #e0e0e0;
            padding: 10px;
            border-left: 5px solid #dc3545;
            margin-bottom: 20px;
        }
        .form-label {
            color: #e0e0e0;
        }
        h3, h2, h1 {
            color: #e0e0e0;
        }
    </style>

    <script>
        // Client-side form validation before submission
        function validateForm() {
            const title = document.forms["postForm"]["title"].value.trim();
            const content = document.forms["postForm"]["content"].value.trim();

            if (title === "" || content === "") {
                alert("Both title and content are required.");
                return false; // prevent submission
            }
            return true;
        }
    </script>
</head>
<body>

<div class="container">
    <h2 class="mb-4 text-center">Create New Post</h2>

    <!-- Show error if any (from server) -->
    <?php if (!empty($error_message)): ?>
        <div class="alert text-center"><?= $error_message ?></div>
    <?php endif; ?>

    <form name="postForm" method="post" onsubmit="return validateForm();"> <!-- Trigger JS validation -->
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
