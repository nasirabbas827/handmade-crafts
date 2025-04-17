<?php
session_start();
include('config.php');


$product_id = $_GET['product_id'];

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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Product Details & Reviews</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">
    <style>
        .fa-star { color: gold; }
        .product-details img { width: 100%; height: auto; max-height: 300px; object-fit: cover; }
    </style>
</head>
<body>
<?php include('navbar.php'); ?>

<div class="container mt-5">
    <h2 class="mb-4">Product Details</h2>
    <?php if ($product): ?>
        <div class="card mb-5 product-details">
            <div class="row no-gutters">
                <div class="col-md-4">
                    <img src="seller/products_images/<?php echo $product['ImageURL']; ?>" class="card-img" alt="Product Image">
                </div>
                <div class="col-md-8">
                    <div class="card-body">
                        <h4 class="card-title"><?php echo $product['ProductName']; ?></h4>
                        <p class="card-text"><strong>Category:</strong> <?php echo $product['category_name']; ?></p>
                        <p class="card-text"><strong>Description:</strong> <?php echo $product['Description']; ?></p>
                        <p class="card-text"><strong>Price:</strong> RS: <?php echo number_format($product['Price'], 2); ?></p>
                        <p class="card-text"><strong>Stock:</strong> <?php echo $product['StockQuantity']; ?></p>
                        <p class="card-text"><strong>Store:</strong> <?php echo $product['StoreName']; ?></p>
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
                            <img class="card-img-top" src="buyer/uploads/<?php echo $row['Image']; ?>" alt="Review Image">
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
        <div class="alert alert-info">No reviews available for this product.</div>
    <?php endif; ?>
</div>

<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
</body>
</html>
