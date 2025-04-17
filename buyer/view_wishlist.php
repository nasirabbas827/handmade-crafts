<?php
session_start();
include('config.php');

// Check if the user is logged in
if (!isset($_SESSION["id"])) {
    header("location: login.php");
    exit;
}

$user_id = $_SESSION["id"];

// Fetch user's wishlist
$sql_wishlist = "SELECT p.ProductID, p.ProductName, p.Price, p.ImageURL, p.Description, c.name AS category_name
                 FROM wishlists w
                 JOIN products p ON w.product_id = p.ProductID
                 JOIN categories c ON p.CategoryID = c.id
                 WHERE w.user_id = $user_id";
$result_wishlist = mysqli_query($conn, $sql_wishlist);

// Handle remove from wishlist
if (isset($_POST['remove_from_wishlist'])) {
    $product_id_to_remove = $_POST['product_id'];

    // Remove product from wishlist
    $sql_remove = "DELETE FROM wishlists WHERE user_id = $user_id AND product_id = $product_id_to_remove";
    if (mysqli_query($conn, $sql_remove)) {
        echo "<script>alert('Product removed from wishlist!'); window.location.href='view_wishlist.php';</script>";
    } else {
        echo "<script>alert('Error removing product from wishlist.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Your Wishlist</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">
</head>
<body>
    <?php include('buyer_navbar.php'); ?>

    <div class="container mt-5">
        <h2 class="mb-4">Your Wishlist</h2>
        
        <?php if (mysqli_num_rows($result_wishlist) > 0): ?>
            <div class="row">
                <?php while ($row = mysqli_fetch_assoc($result_wishlist)): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card">
                            <img class="card-img-top" src="../seller/products_images/<?php echo $row['ImageURL']; ?>" alt="Product Image">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo $row['ProductName']; ?></h5>
                                <p class="card-text"><strong>Category:</strong> <?php echo $row['category_name']; ?></p>
                                <p class="card-text"><strong>Description:</strong> <?php echo $row['Description']; ?></p>
                                <p class="card-text"><strong>Price:</strong> RS: <?php echo number_format($row['Price'], 2); ?></p>
                                
                                <!-- Remove from wishlist form -->
                                <form method="POST" action="view_wishlist.php">
                                    <input type="hidden" name="product_id" value="<?php echo $row['ProductID']; ?>">
                                    <button type="submit" name="remove_from_wishlist" class="btn btn-danger">Remove from Wishlist</button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="alert alert-info">Your wishlist is empty.</div>
        <?php endif; ?>
    </div>

    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
</body>
</html>
