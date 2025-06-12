<?php
include 'db.php';

$success_message = "";
$error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']); // Clean input
    $password = trim($_POST['password']);
    $role = 'editor'; // Default role

    if (strlen($username) < 3 || strlen($password) < 6) {
        $error_message = "Username must be at least 3 characters and password at least 6 characters.";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT); // Securely hash password

        $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $hashed_password, $role);

        if ($stmt->execute()) {
            $success_message = "Registration successful. <a href='login.php'>Login here</a>";
        } else {
            $error_message = "Username already exists or something went wrong.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f2f5;
        }

        .container {
            width: 400px;
            margin: 60px auto;
            padding: 30px;
            background-color: white;
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
            border-radius: 8px;
        }

        h2 {
            text-align: center;
            color: #333;
        }

        label {
            display: block;
            margin: 10px 0 5px;
            color: #555;
        }

        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 4px;
            border: 1px solid #ccc;
        }

        input[type="submit"] {
            width: 100%;
            padding: 10px;
            background-color: #0066cc;
            border: none;
            color: white;
            font-weight: bold;
            cursor: pointer;
            border-radius: 4px;
        }

        input[type="submit"]:hover {
            background-color: #004c99;
        }

        p {
            text-align: center;
        }

        a {
            color: #0066cc;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Register</h2>

    <?php if (!empty($success_message)): ?>
        <p style="color: green;"><?= $success_message ?></p>
    <?php elseif (!empty($error_message)): ?>
        <p style="color: red;"><?= $error_message ?></p>
    <?php endif; ?>

    <form method="post" novalidate>
        <label>Username:</label>
        <input type="text" name="username" minlength="3" required>

        <label>Password:</label>
        <input type="password" name="password" minlength="6" required>

        <input type="submit" value="Register">
    </form>
</div>
</body>
</html>
