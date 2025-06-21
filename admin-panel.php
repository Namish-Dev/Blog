<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'admin') {
    header("Location: dashboard.php");
    exit;
}

include 'db.php';

// ----------- Handle Role Update -----------
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_role'])) {
    $userId = $_POST['user_id'];
    $newRole = $_POST['role'];
    $stmt = $conn->prepare("UPDATE users SET role = ? WHERE id = ?");
    $stmt->bind_param("si", $newRole, $userId);
    $stmt->execute();
    $stmt->close();
}

// ----------- Handle User Deletion -----------
if (isset($_GET['delete_user'])) {
    $deleteId = $_GET['delete_user'];
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $deleteId);
    $stmt->execute();
    $stmt->close();
    header("Location: admin-panel.php");
    exit;
}

// ----------- Handle Post Approval -----------
if (isset($_GET['approve'])) {
    $approveId = $_GET['approve'];
    $stmt = $conn->prepare("UPDATE posts SET is_approved = 1 WHERE id = ?");
    $stmt->bind_param("i", $approveId);
    $stmt->execute();
    $stmt->close();
    header("Location: admin-panel.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #181a1b;
            color: #e0e0e0;
        }
        .card {
            background-color: #23272b;
            border: 1px solid #343a40;
            color: #e0e0e0;
        }
        .table {
            color: #e0e0e0;
        }
        .table th, .table td {
            border-color: #444;
        }
        .btn-secondary { background-color: #6c757d; border: none; }
        .btn-secondary:hover { background-color: #5a6268; }
        .btn-warning, .btn-danger { color: #fff; }
        .btn-sm { font-size: 0.8rem; }
    </style>
</head>
<body>

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">Admin Panel</h2>
        <a href="dashboard.php" class="btn btn-secondary">← Back to Dashboard</a>
    </div>

    <!-- Analytics -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card p-3 mb-3">
                <h4>Total Users</h4>
                <p class="fs-4">
                    <?= $conn->query("SELECT COUNT(*) AS total FROM users")->fetch_assoc()['total'] ?>
                </p>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card p-3 mb-3">
                <h4>Total Posts</h4>
                <p class="fs-4">
                    <?= $conn->query("SELECT COUNT(*) AS total FROM posts")->fetch_assoc()['total'] ?>
                </p>
            </div>
        </div>
    </div>

    <!-- Manage Users -->
    <div class="card p-4 mb-5 shadow">
        <h4 class="mb-4">👥 Manage Users</h4>
        <table class="table table-bordered table-dark table-hover">
            <thead>
                <tr>
                    <th>ID</th><th>Username</th><th>Role</th><th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $users = $conn->query("SELECT id, username, role FROM users");
                while ($user = $users->fetch_assoc()):
                ?>
                <tr>
                    <td><?= $user['id'] ?></td>
                    <td><?= htmlspecialchars($user['username']) ?></td>
                    <td>
                        <form method="post" class="d-flex gap-2">
                            <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                            <select name="role" class="form-select form-select-sm bg-dark text-light" required>
                                <option value="user" <?= $user['role'] === 'user' ? 'selected' : '' ?>>User</option>
                                <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                            </select>
                            <button type="submit" name="update_role" class="btn btn-warning btn-sm">Update</button>
                        </form>
                    </td>
                    <td>
                        <a href="?delete_user=<?= $user['id'] ?>" class="btn btn-danger btn-sm"
                           onclick="return confirm('Are you sure you want to delete this user?')">Delete</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <!-- Unapproved Posts -->
    <div class="card p-4 shadow">
        <h4 class="mb-4">📝 Pending Post Approvals</h4>
        <?php
        $posts = $conn->query("SELECT * FROM posts WHERE is_approved = 0 ORDER BY created_at DESC");
        if ($posts->num_rows > 0):
        ?>
        <table class="table table-bordered table-dark table-hover">
            <thead>
                <tr><th>ID</th><th>Title</th><th>Date</th><th>Action</th></tr>
            </thead>
            <tbody>
                <?php while ($post = $posts->fetch_assoc()): ?>
                <tr>
                    <td><?= $post['id'] ?></td>
                    <td><?= htmlspecialchars($post['title']) ?></td>
                    <td><?= $post['created_at'] ?></td>
                    <td><a href="?approve=<?= $post['id'] ?>" class="btn btn-success btn-sm">Approve</a></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <?php else: ?>
            <p>No pending posts.</p>
        <?php endif; ?>
    </div>
</div>

</body>
</html>
