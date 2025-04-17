<?php
session_start();
include('config.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require './PHPMailer/src/Exception.php';
require './PHPMailer/src/PHPMailer.php';
require './PHPMailer/src/SMTP.php';

if (!isset($_SESSION["id"]) || $_SESSION["usertype"] != "buyer") {
    header("location: login.php");
    exit;
}

$user_id = $_SESSION["id"];
$query = "SELECT email FROM users WHERE id = $user_id";
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($result);

if (!$row) {
    echo "Error: Unable to fetch user email.";
    exit;
}

$user_email = $row['email'];

// Validate required POST fields
if (
    !isset($_POST['total_price']) || 
    !isset($_POST['delivery_address']) || 
    !isset($_POST['payment_method']) || 
    !isset($_POST['order_time'])
) {
    header("location: checkout.php");
    exit;
}

$total_price = $_POST['total_price'];
$delivery_address = mysqli_real_escape_string($conn, $_POST['delivery_address']);
$payment_method = mysqli_real_escape_string($conn, $_POST['payment_method']);
$order_time = $_POST['order_time'];
$payment_status = 'unpaid';

// Insert order into orders table
$sql_insert_order = "INSERT INTO orders (UserID, TotalPrice, DeliveryAddress, PaymentMethod, PaymentStatus, OrderTime, OrderStatus) 
                     VALUES ('$user_id', '$total_price', '$delivery_address', '$payment_method', '$payment_status', '$order_time', 'Pending')";

if (mysqli_query($conn, $sql_insert_order)) {
    $order_id = mysqli_insert_id($conn);

    foreach ($_SESSION['cart'] as $item) {
        $product_id = $item['product_id'];
        $quantity = $item['quantity'];

        // Update stock
        $sql_update_quantity = "UPDATE products SET StockQuantity = StockQuantity - $quantity WHERE ProductID = $product_id";
        mysqli_query($conn, $sql_update_quantity);

        // Insert into order_items
        $sql_insert_order_item = "INSERT INTO order_items (OrderID, ProductID, Quantity) 
                                  VALUES ('$order_id', '$product_id', '$quantity')";
        mysqli_query($conn, $sql_insert_order_item);
    }

    // Clear cart
    unset($_SESSION['cart']);

    // Send confirmation email
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'nasiryt.827@gmail.com';
    $mail->Password = 'mtvp ruzp aqfu tfxt';
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    $mail->setFrom('nasiryt.827@gmail.com', 'NASIR ABBAS');
    $mail->addAddress($user_email);

    $mail->Subject = 'Order Confirmation';
    $mail->Body = "Your order has been placed successfully.\nOrder ID: $order_id\nPayment Method: $payment_method\nOrder Time: $order_time";

    if ($mail->send()) {
        header("location: order_confirmation.php?order_id=$order_id");
        exit;
    } else {
        echo 'Email could not be sent.';
    }
} else {
    echo "Error: " . $sql_insert_order . "<br>" . mysqli_error($conn);
}

mysqli_close($conn);
?>
