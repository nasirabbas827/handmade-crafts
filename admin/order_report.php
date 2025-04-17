<?php
session_start();
include 'config.php';

// Check if user is logged in as admin
if (!isset($_SESSION["id"]) || $_SESSION["usertype"] != "admin") {
    header("location: admin_login.php");
    exit;
}

$from_date = $_GET['from_date'] ?? '';
$to_date = $_GET['to_date'] ?? '';

// Build WHERE clause for filtering by date range
$where_clause = "";
if ($from_date && $to_date) {
    $where_clause = "WHERE o.OrderTime BETWEEN '$from_date' AND '$to_date'";
}

// Fetch all orders with order items, usernames, and payment image
$sql_select = "SELECT o.*, oi.*, p.ProductName, u.Username, t.PaymentImage 
               FROM orders o
               INNER JOIN order_items oi ON o.OrderID = oi.OrderID
               INNER JOIN products p ON oi.ProductID = p.ProductID
               INNER JOIN users u ON o.UserID = u.id
               LEFT JOIN transactions t ON o.OrderID = t.OrderID
               $where_clause
               ORDER BY o.OrderID DESC";
$result = mysqli_query($conn, $sql_select);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Panel - View Orders</title>
    <!-- Add Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">
</head>
<body>
    <?php include 'admin_navbar.php'; ?>
    <div class="container mt-4">
    <h2>View Orders</h2>


        <!-- Date Filter Form -->
        <form method="GET" class="form-inline mb-4">
            <label class="mr-2">From:</label>
            <input type="date" name="from_date" value="<?php echo $from_date; ?>" class="form-control mr-3">
            <label class="mr-2">To:</label>
            <input type="date" name="to_date" value="<?php echo $to_date; ?>" class="form-control mr-3">
            <button type="submit" class="btn btn-primary">Filter</button>
            <a href="?" class="btn btn-secondary ml-2">Reset</a>
        </form>

        <!-- Print Button -->
        <a href="" class="btn btn-outline-dark float-right mb-4" onclick="window.print()">Print</a>


        <?php if (mysqli_num_rows($result) > 0): ?>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Username</th>
                        <th>Total Price</th>
                        <th>Delivery Address</th>
                        <th>Order Status</th>
                        <th>Product Name</th>
                        <th>Quantity</th>
                        <th>Payment Image</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?php echo $row['OrderID']; ?></td>
                            <td><?php echo $row['Username']; ?></td>
                            <td>RS: <?php echo number_format($row['TotalPrice'], 2); ?></td>
                            <td><?php echo $row['DeliveryAddress']; ?></td>
                            <td><?php echo $row['OrderStatus']; ?></td>
                            <td><?php echo $row['ProductName']; ?></td>
                            <td><?php echo $row['Quantity']; ?></td>
                            <td>
                                <?php
                                // Check if payment image exists for the transaction
                                if ($row['PaymentImage']) {
                                    echo "<img src='../buyer/{$row['PaymentImage']}' width='100' height='100' class='img-thumbnail'>";
                                } else {
                                    echo "No Payment Image";
                                }
                                ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="alert alert-info">No orders found in the selected date range.</div>
        <?php endif; ?>
    </div>

    <!-- Add Bootstrap JS (Optional) -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
</body>
</html>
