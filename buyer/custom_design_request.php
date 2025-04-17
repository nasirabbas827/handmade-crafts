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

// Fetch product details to show in the request form
$sql_product = "SELECT p.ProductName, s.StoreName, s.SellerID, p.Price 
                FROM products p
                JOIN stores s ON p.SellerID = s.SellerID
                WHERE p.ProductID = $product_id";
$product_result = mysqli_query($conn, $sql_product);
$product = mysqli_fetch_assoc($product_result);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $details = mysqli_real_escape_string($conn, $_POST['details']);
    $estimated_price = $product['Price'];  // Use the seller's price as the estimated price
    $seller_id = $product['SellerID'];

    // Insert request into the database
    $sql_request = "INSERT INTO custom_requests (customer_id, seller_id, title, details, estimated_price)
                    VALUES ('$user_id', '$seller_id', '$title', '$details', '$estimated_price')";
    if (mysqli_query($conn, $sql_request)) {
        echo "<script>alert('Custom design request submitted successfully!'); window.location.href='buyer_home.php';</script>";
    } else {
        echo "<script>alert('Error submitting request.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Custom Design Request</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">
</head>
<body>
<?php include('buyer_navbar.php'); ?>

<div class="container mt-5">
    <div class="card mx-auto" style="max-width: 600px;">
        <div class="card-body">
            <h2 class="mb-4">Submit Your Custom Design Request for <?php echo $product['ProductName']; ?></h2>
            
            <form action="custom_design_request.php?product_id=<?php echo $product_id; ?>" method="POST">
                <div class="form-group">
                    <label for="title">Request Title</label>
                    <input type="text" name="title" id="title" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="details">Request Details</label>
                    <textarea name="details" id="details" class="form-control" rows="5" required></textarea>
                </div>
                <!-- No input for estimated price, as it is auto-set by the seller's price -->
                <input type="hidden" name="estimated_price" value="<?php echo $product['Price']; ?>"> <!-- Hidden field to send the estimated price -->
                <button type="submit" class="btn btn-primary">Submit Request</button>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
</body>
</html>
