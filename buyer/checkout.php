<?php
session_start();
include('config.php');

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

if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    echo "Your cart is empty.";
    exit;
}

$cart_items = $_SESSION['cart'];
$total_price = 0;

foreach ($cart_items as $item) {
    $total_price += $item['discount_price'] * $item['quantity'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Checkout</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">
</head>
<body>
<?php include('buyer_navbar.php'); ?>

<div class="container mt-5">
    <h2 class="mb-4">Checkout</h2>
    <div class="row">
        <div class="col-md-12">
            <table class="table">
                <thead>
                <tr>
                    <th>Product Name</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Total</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($cart_items as $item): ?>
                    <tr>
                        <td><?php echo $item['product_name']; ?></td>
                        <td><?php echo $item['quantity']; ?></td>
                        <td>RS: <?php echo number_format($item['discount_price'], 2); ?></td>
                        <td>RS: <?php echo number_format($item['discount_price'] * $item['quantity'], 2); ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>

            <div class="text-right">
                <h4>Total: RS: <?php echo number_format($total_price, 2); ?></h4>
            </div>

            <div class="card mx-auto" style="max-width: 600px;">
                <div class="card-body">
                    <form action="place_order.php" method="post">
                        <input type="hidden" name="total_price" value="<?php echo $total_price; ?>">
                        <input type="hidden" name="order_time" value="<?php echo date('Y-m-d H:i:s'); ?>">

                        <div class="form-group">
                            <label for="email">Email:</label>
                            <input type="email" class="form-control" id="email" value="<?php echo $user_email; ?>" readonly>
                        </div>

                        <div class="form-group">
                            <label for="delivery_address">Delivery Address:</label>
                            <input type="text" class="form-control" id="delivery_address" name="delivery_address" required>
                        </div>

                        <div class="form-group">
                            <label for="payment_method">Payment Method:</label>
                            <select class="form-control" name="payment_method" id="payment_method" required>
                                <option value="paypal">PayPal</option>
                                <option value="credit_card">Credit Card</option>
                                <option value="easypaisa">EasyPaisa</option>
                                <option value="jazzcash">JazzCash</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary mb-5">Place Order</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
</body>
</html>
