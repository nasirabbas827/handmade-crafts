<?php
session_start();
include 'config.php';

// Ensure that the user is a seller
if (!isset($_SESSION["id"]) || $_SESSION["usertype"] != "seller") {
    header("location: login.php");
    exit;
}

$seller_id = $_SESSION['id'];

// Handle delete request
if (isset($_GET['delete'])) {
    $reviewID = intval($_GET['delete']);
    // Delete the review only if the product belongs to the seller
    $sql_check_product = "SELECT p.SellerID 
                          FROM reviews r 
                          INNER JOIN order_items oi ON r.OrderID = oi.OrderID 
                          INNER JOIN products p ON oi.ProductID = p.ProductID 
                          WHERE r.ReviewID = $reviewID";
    $result_check = mysqli_query($conn, $sql_check_product);
    $product = mysqli_fetch_assoc($result_check);

    // Check if the product is the seller's
    if ($product['SellerID'] == $seller_id) {
        $sql_delete = "DELETE FROM reviews WHERE ReviewID = $reviewID";
        mysqli_query($conn, $sql_delete);
    }
    header("Location: view_reviews.php"); // Reload page after deletion
    exit;
}

// Fetch all reviews with product, user, and order details, ensuring product is from the seller
$sql_select = "SELECT r.*, p.ProductName, u.Username, oi.ProductID, oi.Quantity, o.OrderID 
               FROM reviews r 
               INNER JOIN order_items oi ON r.OrderID = oi.OrderID 
               INNER JOIN orders o ON oi.OrderID = o.OrderID 
               INNER JOIN products p ON oi.ProductID = p.ProductID
               INNER JOIN users u ON r.UserID = u.id
               WHERE p.SellerID = $seller_id"; // Ensure product is from the seller
$result = mysqli_query($conn, $sql_select);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Seller Dashboard - View Reviews</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">
</head>
<body>
    <?php include 'seller_navbar.php'; ?>

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
