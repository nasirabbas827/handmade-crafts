<?php
// start session and check if user is logged in as seller
session_start();
include 'config.php';

if (!isset($_SESSION["id"]) || $_SESSION["usertype"] != "seller") {
    header("location: login.php");
    exit;
}

// Check if product ID is provided in the URL
if (!isset($_GET['product_id'])) {
    header("location: view_products.php");
    exit;
}

$product_id = $_GET['product_id'];

// Fetch product details
$sql_product = "SELECT * FROM products WHERE ProductID = ? AND SellerID = ?";
$stmt_product = mysqli_prepare($conn, $sql_product);
mysqli_stmt_bind_param($stmt_product, "ii", $product_id, $_SESSION['id']);
mysqli_stmt_execute($stmt_product);
$result_product = mysqli_stmt_get_result($stmt_product);

// If product not found or not owned by the seller, redirect to view_products.php
if (mysqli_num_rows($result_product) == 0) {
    header("location: view_products.php");
    exit;
}

$row_product = mysqli_fetch_assoc($result_product);

// If form is submitted, update product details
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $product_name = $_POST['product_name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $stock_quantity = $_POST['stock_quantity'];
    $category_id = $_POST['category_id'];
    $image_filename = $row_product['ImageURL']; // Retain the current image filename by default

    // Check if a new image is uploaded
    if ($_FILES['product_image']['error'] === UPLOAD_ERR_OK) {
        $file_extension = pathinfo($_FILES['product_image']['name'], PATHINFO_EXTENSION);
        $image_filename = uniqid() . '.' . $file_extension;
        $upload_path = 'products_images/' . $image_filename;

        if (move_uploaded_file($_FILES['product_image']['tmp_name'], $upload_path)) {
            // If image upload is successful, delete the old image if necessary
            $old_image_path = 'products_images/' . $row_product['ImageURL'];
            if (file_exists($old_image_path)) {
                unlink($old_image_path);
            }
        } else {
            $error_message = 'Failed to upload new product image. Please try again.';
        }
    }

    // Update product in the database
    $sql_update_product = "UPDATE products SET ProductName = ?, Description = ?, Price = ?, StockQuantity = ?, CategoryID = ?, ImageURL = ? WHERE ProductID = ?";
    $stmt_update_product = mysqli_prepare($conn, $sql_update_product);
    mysqli_stmt_bind_param($stmt_update_product, "sssssss", $product_name, $description, $price, $stock_quantity, $category_id, $image_filename, $product_id);
    mysqli_stmt_execute($stmt_update_product);
    mysqli_stmt_close($stmt_update_product);

    // Redirect to the view products page
    header("location: view_products.php");
    exit;
}

// Fetch categories from the database
$sql_categories = "SELECT id, name FROM categories";
$result_categories = mysqli_query($conn, $sql_categories);
?>

<!DOCTYPE html>
<html>

<head>
    <title>Seller Panel - Edit Product</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">
</head>

<body>
    <?php include 'seller_navbar.php'; ?>

    <div class="container mt-5 mb-5">
        <div class="card mx-auto" style="max-width: 600px;">
            <div class="card-body">
                <h2 class="text-center">Edit Product</h2>
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>?product_id=<?php echo $product_id; ?>" method="post" enctype="multipart/form-data">
                    <div class="form-group">
                        <label>Product Name</label>
                        <input type="text" class="form-control" name="product_name" value="<?php echo $row_product['ProductName']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea class="form-control" name="description" required><?php echo $row_product['Description']; ?></textarea>
                    </div>
                    <div class="form-group">
                        <label>Price</label>
                        <input type="number" class="form-control" name="price" value="<?php echo $row_product['Price']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Stock Quantity</label>
                        <input type="number" class="form-control" name="stock_quantity" value="<?php echo $row_product['StockQuantity']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Category</label>
                        <select class="form-control" name="category_id" required>
                            <?php while ($row_category = mysqli_fetch_assoc($result_categories)) : ?>
                                <option value="<?php echo $row_category['id']; ?>" <?php if ($row_category['id'] == $row_product['CategoryID']) echo 'selected'; ?>><?php echo $row_category['name']; ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Product Image</label>
                        <input type="file" class="form-control-file" name="product_image" accept="image/png, image/jpeg, image/jpg">
                        <p>Current Image: <img src="products_images/<?php echo $row_product['ImageURL']; ?>" width="100"></p>
                    </div>
                    <button type="submit" class="btn btn-primary">Update Product</button>
                    <a class="btn btn-outline-dark" href="view_products.php">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</body>

</html>
