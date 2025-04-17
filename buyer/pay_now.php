<?php
session_start();
include('config.php');

// Check if user is logged in
if (!isset($_SESSION["id"]) || $_SESSION["usertype"] != "buyer") {
    header("location: login.php");
    exit;
}

// Check if order_id is provided
if (!isset($_GET['order_id'])) {
    echo "Invalid request.";
    exit;
}

$order_id = intval($_GET['order_id']);
$user_id = $_SESSION['id'];

// Fetch order details
$sql_order = "SELECT * FROM orders WHERE OrderID = $order_id AND UserID = $user_id";
$result_order = mysqli_query($conn, $sql_order);
if (mysqli_num_rows($result_order) == 0) {
    echo "Order not found.";
    exit;
}

$order = mysqli_fetch_assoc($result_order);

// Handle payment submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES['payment_image'])) {
    $target_dir = "transactions/";
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    $file_name = basename($_FILES["payment_image"]["name"]);
    $target_file = $target_dir . time() . "_" . $file_name;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    $allowed_types = ["jpg", "jpeg", "png", "gif"];

    if (in_array($imageFileType, $allowed_types)) {
        if (move_uploaded_file($_FILES["payment_image"]["tmp_name"], $target_file)) {
            // Insert into transactions table
            $stmt = $conn->prepare("INSERT INTO transactions (OrderID, PaymentImage) VALUES (?, ?)");
            $stmt->bind_param("is", $order_id, $target_file);
            $stmt->execute();

            // Update order payment status
            $update = "UPDATE orders SET PaymentStatus = 'paid' WHERE OrderID = $order_id";
            mysqli_query($conn, $update);

            echo "<script>alert('Payment uploaded successfully.'); window.location='my_orders.php';</script>";
            exit;
        } else {
            echo "Error uploading file.";
        }
    } else {
        echo "Invalid file type.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Pay Now</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">

</head>
<body>
<?php include('buyer_navbar.php'); ?>

<div class="container mt-5">
    <h2 class="mb-4">Pay for Order #<?php echo $order['OrderID']; ?></h2>

    <div class="card mb-4">
        <div class="card-body">
            <p><strong>Total Price:</strong> RS: <?php echo number_format($order['TotalPrice'], 2); ?></p>
            <p><strong>Delivery Address:</strong> <?php echo $order['DeliveryAddress']; ?></p>
            <p><strong>Order Status:</strong> <?php echo $order['OrderStatus']; ?></p>
            <p><strong>Payment Status:</strong> <?php echo ucfirst($order['PaymentStatus']); ?></p>
            <hr>
            <h5>Order Items:</h5>
            <ul>
                <?php
                $sql_items = "SELECT p.ProductName, oi.Quantity 
                              FROM order_items oi 
                              JOIN products p ON oi.ProductID = p.ProductID 
                              WHERE oi.OrderID = $order_id";
                $result_items = mysqli_query($conn, $sql_items);
                while ($item = mysqli_fetch_assoc($result_items)) {
                    echo "<li>" . $item['ProductName'] . " (Qty: " . $item['Quantity'] . ")</li>";
                }
                ?>
            </ul>
        </div>
    </div>
    <div class="card mx-auto" style="max-width: 600px;">
        <div class="card-body">
    <form method="post" enctype="multipart/form-data" class="border p-4">
        <div class="form-group">
            <label for="payment_image">Upload Transaction Screenshot:</label>
            <input type="file" class="form-control-file" id="payment_image" name="payment_image" required>
        </div>
        <button type="submit" class="btn btn-success">Submit Payment</button>
    </form>
</div>
</div>
</div>

</body>
</html>
