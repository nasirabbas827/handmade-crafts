<?php
session_start();
include('config.php');

if (!isset($_SESSION["id"])) {
    header("location: login.php");
    exit;
}

if (!isset($_GET['product_id'])) {
    header("location: buyer_home.php");
    exit;
}

$product_id = $_GET['product_id'];
$user_id = $_SESSION['id'];

// Fetch product details
$sql_product = "SELECT p.*, c.name AS category_name, s.StoreName 
                FROM products p
                JOIN categories c ON p.CategoryID = c.id
                JOIN stores s ON p.SellerID = s.SellerID
                WHERE p.ProductID = $product_id";
$product_result = mysqli_query($conn, $sql_product);
$product = mysqli_fetch_assoc($product_result);

// Fetch reviews
$sql_reviews = "SELECT u.Username, r.Comment, r.Rating, r.Image 
                FROM orders o 
                INNER JOIN users u ON o.UserID = u.id 
                INNER JOIN order_items oi ON o.OrderID = oi.OrderID 
                INNER JOIN reviews r ON oi.OrderID = r.OrderID 
                WHERE oi.ProductID = $product_id";
$result_reviews = mysqli_query($conn, $sql_reviews);

// Check wishlist status
$wishlist_sql = "SELECT * FROM wishlists WHERE user_id = $user_id AND product_id = $product_id";
$wishlist_result = mysqli_query($conn, $wishlist_sql);
$in_wishlist = mysqli_num_rows($wishlist_result) > 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Product Details & Reviews</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css">
    <style>
        .fa-star { color: gold; }
        .product-details img { width: 100%; height: auto; max-height: 300px; object-fit: cover; }
    </style>
</head>
<body>
<?php include('buyer_navbar.php'); ?>

<div class="container mt-5">
    <h2 class="mb-4">Product Details</h2>
    <?php if ($product): ?>
        <div class="card mb-5 product-details">
            <div class="row no-gutters">
                <div class="col-md-4">
                    <img src="../seller/products_images/<?php echo $product['ImageURL']; ?>" class="card-img" alt="Product Image">
                </div>
                <div class="col-md-8">
                    <div class="card-body">
                        <h4 class="card-title"><?php echo $product['ProductName']; ?></h4>
                        <p class="card-text"><strong>Category:</strong> <?php echo $product['category_name']; ?></p>
                        <p class="card-text"><strong>Description:</strong> <?php echo $product['Description']; ?></p>
                        <p class="card-text"><strong>Price:</strong> RS: <?php echo number_format($product['Price'], 2); ?></p>
                        <p class="card-text"><strong>Stock:</strong> <?php echo $product['StockQuantity']; ?></p>
                        <p class="card-text"><strong>Store:</strong> <?php echo $product['StoreName']; ?></p>
                        <form action="toggle_wishlist.php" method="post" class="mt-3">
                            <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
                            <button type="submit" class="btn <?php echo $in_wishlist ? 'btn-danger' : 'btn-outline-primary'; ?>">
                                <i class="fa <?php echo $in_wishlist ? 'fa-heart' : 'fa-heart-o'; ?>"></i>
                                <?php echo $in_wishlist ? 'Remove from Wishlist' : 'Add to Wishlist'; ?>
                            </button>
                        </form>
                        <!-- Custom Design Request Button -->
                        <form action="custom_design_request.php" method="get" class="mt-3">
                            <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
                            <button type="submit" class="btn btn-info">Request Custom Design</button>
                        </form>
                    </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <h3 class="mb-4">User Reviews</h3>
    <?php if (mysqli_num_rows($result_reviews) > 0): ?>
        <div class="row">
            <?php while ($row = mysqli_fetch_assoc($result_reviews)) : ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <?php if (!empty($row['Image'])) : ?>
                            <img class="card-img-top" src="uploads/<?php echo $row['Image']; ?>" alt="Review Image">
                        <?php endif; ?>
                        <div class="card-body">
                            <h5 class="card-title">User: <?php echo htmlspecialchars($row['Username']); ?></h5>
                            <p><strong>Rating:</strong> 
                                <?php for ($i = 0; $i < intval($row['Rating']); $i++) echo '<span class="fa fa-star"></span>'; ?>
                            </p>
                            <p class="card-text"><strong>Comment:</strong> <?php echo htmlspecialchars($row['Comment']); ?></p>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <div class="container alert alert-info">No reviews available for this product.</div>
    <?php endif; ?>
</div>

<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
</body>
</html>
