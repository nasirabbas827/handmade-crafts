<?php
session_start();
include 'config.php';

// Check if user is logged in as admin
if (!isset($_SESSION["id"]) || $_SESSION["usertype"] != "admin") {
    header("location: admin_login.php");
    exit;
}

// Handle delete request
if (isset($_GET['delete'])) {
    $reviewID = intval($_GET['delete']);
    $sql_delete = "DELETE FROM reviews WHERE ReviewID = $reviewID";
    mysqli_query($conn, $sql_delete);
    header("Location: view_reviews.php"); // Reload page after deletion
    exit;
}

// Fetch all reviews with product, user, and order details
$sql_select = "SELECT r.*, p.ProductName, u.Username, oi.ProductID, oi.Quantity, o.OrderID 
               FROM reviews r 
               INNER JOIN order_items oi ON r.OrderID = oi.OrderID 
               INNER JOIN orders o ON oi.OrderID = o.OrderID 
               INNER JOIN products p ON oi.ProductID = p.ProductID
               INNER JOIN users u ON r.UserID = u.id";
$result = mysqli_query($conn, $sql_select);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard - View Reviews</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">
</head>
<body>
    <?php include 'admin_navbar.php'; ?>

    <div class="container mt-4">
        <h2>View Reviews</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Review ID</th>
                    <th>Username</th>
                    <th>Product Name</th>
                    <th>Rating</th>
                    <th>Comment</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                    <tr>
                        <td><?php echo $row['ReviewID']; ?></td>
                        <td><?php echo $row['Username']; ?></td>
                        <td><?php echo $row['ProductName']; ?></td>
                        <td><?php echo $row['Rating']; ?></td>
                        <td><?php echo $row['Comment']; ?></td>
                        <td>
                            <a href="?delete=<?php echo $row['ReviewID']; ?>" 
                               class="btn btn-danger btn-sm" 
                               onclick="return confirm('Are you sure you want to delete this review?');">
                               Delete
                            </a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
</body>
</html>
