<?php
// start session and check if user is logged in as seller
session_start();
include 'config.php';

if (!isset($_SESSION["id"]) || $_SESSION["usertype"] != "seller") {
    header("location: login.php");
    exit;
}

$seller_id = $_SESSION['id'];
$search_term = '';

// Search filter
if (isset($_GET['search'])) {
    $search_term = trim($_GET['search']);
    $sql_products = "SELECT p.*, c.name AS category_name FROM products p 
                     INNER JOIN categories c ON p.CategoryID = c.id 
                     WHERE SellerID = ? AND p.ProductName LIKE ?";
    $stmt_products = mysqli_prepare($conn, $sql_products);
    $like_search = "%$search_term%";
    mysqli_stmt_bind_param($stmt_products, "is", $seller_id, $like_search);
} else {
    $sql_products = "SELECT p.*, c.name AS category_name FROM products p 
                     INNER JOIN categories c ON p.CategoryID = c.id 
                     WHERE SellerID = ?";
    $stmt_products = mysqli_prepare($conn, $sql_products);
    mysqli_stmt_bind_param($stmt_products, "i", $seller_id);
}

mysqli_stmt_execute($stmt_products);
$result_products = mysqli_stmt_get_result($stmt_products);

$success_message = '';
$error_message = '';

// If product is to be deleted
if (isset($_POST['delete_product'])) {
    $product_id = $_POST['product_id'];
    $sql_delete_product = "DELETE FROM products WHERE ProductID = ?";
    $stmt_delete_product = mysqli_prepare($conn, $sql_delete_product);
    mysqli_stmt_bind_param($stmt_delete_product, "i", $product_id);
    if (mysqli_stmt_execute($stmt_delete_product)) {
        $success_message = 'Product deleted successfully.';
    } else {
        $error_message = 'Failed to delete product. Please try again.';
    }
    mysqli_stmt_close($stmt_delete_product);
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Seller Panel - View Products</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">
    <style>
        .low-stock {
            background-color: #f8d7da !important;
        }
    </style>
</head>

<body>
    <?php include 'seller_navbar.php'; ?>

    <div class="container mt-5 mb-5">
        <h2 class="text-center">View Products</h2>

        <!-- Search Bar -->
        <form method="get" class="form-inline justify-content-center mb-4">
            <input type="text" name="search" class="form-control mr-2" placeholder="Search by Product Name" value="<?php echo htmlspecialchars($search_term); ?>">
            <button type="submit" class="btn btn-outline-primary">Search</button>
            <a href="view_products.php" class="btn btn-outline-secondary ml-2">Reset</a>
        </form>

        <!-- Display messages -->
        <?php if ($success_message) : ?>
            <div class="alert alert-success"><?php echo $success_message; ?></div>
        <?php endif; ?>
        <?php if ($error_message) : ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <!-- Products Table -->
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Product Name</th>
                        <th>Description</th>
                        <th>Price</th>
                        <th>Stock Quantity</th>
                        <th>Category</th>
                        <th>Status</th>
                        <th>Image</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row_product = mysqli_fetch_assoc($result_products)) : ?>
                        <tr <?php echo ($row_product['StockQuantity'] < 10) ? 'class="low-stock"' : ''; ?>>
                            <td><?php echo $row_product['ProductName']; ?></td>
                            <td><?php echo $row_product['Description']; ?></td>
                            <td><?php echo $row_product['Price']; ?></td>
                            <td><?php echo $row_product['StockQuantity']; ?></td>
                            <td><?php echo $row_product['category_name']; ?></td>
                            <td><span class="badge badge-info"><?php echo ucfirst($row_product['status']); ?></span></td>
                            <td><img src="products_images/<?php echo $row_product['ImageURL']; ?>" style="max-width: 100px; max-height: 100px;"></td>
                            <td>
                                <a href="edit_product.php?product_id=<?php echo $row_product['ProductID']; ?>" class="btn btn-primary btn-sm mb-2">Edit</a>
                                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" style="display: inline;">
                                    <input type="hidden" name="product_id" value="<?php echo $row_product['ProductID']; ?>">
                                    <button type="submit" name="delete_product" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this product?');">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>
