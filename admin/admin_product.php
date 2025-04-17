<?php
session_start();
include 'config.php';

// Check if user is logged in as admin
if (!isset($_SESSION["id"]) || $_SESSION["usertype"] != "admin") {
    header("location: admin_login.php");
    exit;
}

// Handle product status update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_status'])) {
    $product_id = $_POST['product_id'];
    $new_status = $_POST['status'];
    $update_sql = "UPDATE products SET status = ? WHERE ProductID = ?";
    $stmt = mysqli_prepare($conn, $update_sql);
    mysqli_stmt_bind_param($stmt, "si", $new_status, $product_id);
    mysqli_stmt_execute($stmt);
}

// Search products by name
$search_term = '';
$sql_select = "SELECT p.*, c.name AS category_name, u.username AS seller_name 
               FROM products p 
               INNER JOIN categories c ON p.CategoryID = c.id
               INNER JOIN users u ON p.SellerID = u.id
               WHERE u.usertype = 'seller'";

if (isset($_GET['search']) && !empty(trim($_GET['search']))) {
    $search_term = trim($_GET['search']);
    $sql_select .= " AND p.ProductName LIKE '%" . mysqli_real_escape_string($conn, $search_term) . "%'";
}

$result = mysqli_query($conn, $sql_select);
?>

<!DOCTYPE html>
<html>

<head>
    <title>Admin Panel - View Products</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">
    <style>
        .low-stock {
            background-color: #f8d7da !important;
        }
    </style>
</head>

<body>
    <?php include 'admin_navbar.php'; ?>
    <div class="container mt-4">
        <h2 class="text-center">View Products</h2>

        <!-- Search Form -->
        <form method="get" class="form-inline justify-content-center mb-3">
            <input type="text" name="search" class="form-control mr-2" placeholder="Search by Product Name" value="<?php echo htmlspecialchars($search_term); ?>">
            <button type="submit" class="btn btn-outline-primary">Search</button>
            <a href="admin_product.php" class="btn btn-outline-secondary ml-2">Reset</a>
        </form>

        <!-- Product Table -->
        <table class="table table-bordered table-striped">
            <thead class="thead-dark">
                <tr>
                    <th>Product ID</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Category</th>
                    <th>Seller</th>
                    <th>Status</th>
                    <th>Image</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                    <tr <?php echo ($row['StockQuantity'] < 10) ? 'class="low-stock"' : ''; ?>>
                        <td><?php echo $row['ProductID']; ?></td>
                        <td><?php echo $row['ProductName']; ?></td>
                        <td><?php echo $row['Description']; ?></td>
                        <td><?php echo $row['Price']; ?></td>
                        <td><?php echo $row['StockQuantity']; ?></td>
                        <td><?php echo $row['category_name']; ?></td>
                        <td><?php echo $row['seller_name']; ?></td>
                        <td>
                            <form method="post" class="form-inline">
                                <input type="hidden" name="product_id" value="<?php echo $row['ProductID']; ?>">
                                <select name="status" class="form-control form-control-sm mr-2">
                                    <option value="pending" <?php if ($row['status'] == 'pending') echo 'selected'; ?>>Pending</option>
                                    <option value="approved" <?php if ($row['status'] == 'approved') echo 'selected'; ?>>Approved</option>
                                    <option value="rejected" <?php if ($row['status'] == 'rejected') echo 'selected'; ?>>Rejected</option>
                                </select>
                                <button type="submit" name="update_status" class="btn btn-sm btn-success mt-2">Update</button>
                            </form>
                        </td>
                        <td><img src="../seller/products_images/<?php echo $row['ImageURL']; ?>" alt="Product Image" style="max-width: 100px; max-height: 100px;"></td>
                        <td><?php echo $row['status']; ?></td>

                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <!-- Optional JS -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
</body>

</html>
