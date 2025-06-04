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
    <!-- Bootstrap Dark Theme CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #181a1b !important;
            color: #e0e0e0 !important;
        }
        .card {
            background-color: #23272b;
            color: #e0e0e0;
            border: 1px solid #343a40;
        }
        .form-control, .form-control:focus {
            background-color: #23272b;
            color: #e0e0e0;
            border-color: #444;
        }
        .btn-primary, .btn-success, .btn-danger {
            border: none;
        }
        .btn-primary {
            background-color: #0d6efd;
        }
        .btn-primary:hover {
            background-color: #0b5ed7;
        }
        .btn-success {
            background-color: #198754;
        }
        .btn-success:hover {
            background-color: #157347;
        }
        .btn-danger {
            background-color: #dc3545;
        }
        .btn-danger:hover {
            background-color: #bb2d3b;
        }
        .pagination .page-link {
            background-color: #23272b;
            color: #e0e0e0;
            border-color: #444;
        }
        .pagination .page-item.active .page-link {
            background-color: #0d6efd;
            border-color: #0d6efd;
            color: #fff;
        }
        .alert-info {
            background-color: #23272b;
            color: #e0e0e0;
            border-color: #444;
        }
        a.alert-link {
            color: #0d6efd;
        }
        hr {
            border-top: 2px solid #343a40;
        }
    </style>
</head>
<body class="bg-dark">

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">

            <!-- Welcome and Top Links -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0 fw-bold">Welcome, <?= htmlspecialchars($_SESSION['user']) ?>!</h2>
                <div>
                    <a href="create.php" class="btn btn-success me-2">New Post +</a>
                    <a href="logout.php" class="btn btn-danger">Logout</a>
                </div>
            </div>

            <!-- Search form -->
            <form method="GET" action="dashboard.php" class="input-group mb-4">
                <input type="text" name="search" class="form-control" placeholder="Search posts..."
                    value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>"> <!--  Sets the input value to the 'search' query parameter from the URL if it exists, safely escaped for HTML output.  -->

                <button type="submit" class="btn btn-primary">Search</button>
            </form>

            <hr class="border-secondary">

            <?php
            // ----------- PAGINATION SETUP -----------
            $limit = 5;//Number of posts per page
            $page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1; //This line checks if the user has provided a valid page number in the URL. If yes, it uses that number; otherwise, it defaults to page 1.
            $start = ($page - 1) * $limit;//Calculate the starting point for the SQL query

            // ----------- SEARCH SETUP -----------
            $search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';//It checks if the user has entered anything in the search box (via URL), and if they have, it sanitizes the input to protect your database. If the user didn't search anything, it sets the variable to an empty string.
            $search_sql = $search ? "WHERE title LIKE '%$search%' OR content LIKE '%$search%'" : '';//This is for the SQL query to filter posts based on the search term. If the user has searched for something, it will look for that term in the title or content of the posts. If not, it will return all posts.

            // ----------- COUNT TOTAL MATCHING POSTS -----------
            $count_query = "SELECT COUNT(*) AS total FROM posts $search_sql";
            $count_result = mysqli_query($conn, $count_query);
            $total = mysqli_fetch_assoc($count_result)['total'];//This line counts how many posts match the search criteria. It runs a SQL query that counts all posts, applying the search filter if there is one.
            $total_pages = ceil($total / $limit);//This line calculates the total number of pages needed to display all posts, based on the number of posts per page (limit). It uses the `ceil` function to round up to the nearest whole number, ensuring that any remaining posts that don't fill a complete page still get their own page.

            // ----------- FETCH POSTS FOR CURRENT PAGE -----------
            $query = "SELECT * FROM posts $search_sql ORDER BY created_at DESC LIMIT $start, $limit";
            $result = mysqli_query($conn, $query);

            // ----------- DISPLAY POSTS -----------
            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<div class='card mb-4 shadow'>";
                    echo "<div class='card-body'>";
                    echo "<h4 class='card-title fw-semibold'>" . htmlspecialchars($row['title']) . "</h4>";
                    echo "<p class='card-text'>" . nl2br(htmlspecialchars($row['content'])) . "</p>";
                    echo "<div class='d-flex justify-content-between align-items-center mt-3'>";
                    echo "<small class='text-secondary'>Posted on " . $row['created_at'] . "</small>";
                    echo "<div>";
                    echo "<a href='edit.php?id=" . $row['id'] . "' class='btn btn-sm btn-primary me-2'>Edit</a>";
                    echo "<a href='delete.php?id=" . $row['id'] . "' class='btn btn-sm btn-danger'>Delete</a>";
                    echo "</div>";
                    echo "</div>";
                    echo "</div>";
                    echo "</div>";
                }

                // ----------- PAGINATION LINKS -----------
                echo "<nav aria-label='Page navigation'>";
                echo "<ul class='pagination justify-content-center'>";
                for ($i = 1; $i <= $total_pages; $i++) {
                    $active = $i == $page ? "active" : "";
                    $searchParam = $search ? "&search=" . urlencode($search) : "";
                    echo "<li class='page-item $active'><a class='page-link' href='?search=" . urlencode($search) . "&page=$i'>$i</a></li>";
                }
                echo "</ul>";
                echo "</nav>";

            } else {
                echo "<div class='alert alert-info text-center'>No posts found. <a href='create.php' class='alert-link'>Add one</a>.</div>";
            }
            ?>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>