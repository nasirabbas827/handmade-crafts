<?php
session_start();
include('config.php');

if (!isset($_SESSION["id"]) || $_SESSION["usertype"] != "seller") {
    header("location: login.php");
    exit;
}

$seller_id = $_SESSION['id'];
$from_date = $_GET['from_date'] ?? '';
$to_date = $_GET['to_date'] ?? '';

$where_clause = "WHERE products.SellerID = $seller_id";
if ($from_date && $to_date) {
    $where_clause .= " AND DATE(orders.OrderTime) BETWEEN '$from_date' AND '$to_date'";
}

$sql_orders = "SELECT orders.*, products.ProductName, order_items.Quantity 
               FROM orders 
               INNER JOIN order_items ON orders.OrderID = order_items.OrderID 
               INNER JOIN products ON order_items.ProductID = products.ProductID 
               $where_clause 
               ORDER BY orders.OrderID DESC";
$result_orders = mysqli_query($conn, $sql_orders);

$order_statuses = ['Pending', 'Cancel', 'In Process', 'Delivered'];

if (isset($_POST['update_status'])) {
    $order_id = $_POST['order_id'];
    $new_status = $_POST['status'];
    mysqli_query($conn, "UPDATE orders SET OrderStatus = '$new_status' WHERE OrderID = $order_id");
    header("Location: ".$_SERVER['PHP_SELF']);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Product Orders</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">

</head>
<body>
<?php include('seller_navbar.php'); ?>

<div class="container mt-5">
    <h2 class="mb-4">My Product Orders</h2>

    <!-- Date Filter Form -->
    <form method="GET" class="form-inline mb-4">
        <label class="mr-2">From:</label>
        <input type="date" name="from_date" value="<?php echo $from_date; ?>" class="form-control mr-3">
        <label class="mr-2">To:</label>
        <input type="date" name="to_date" value="<?php echo $to_date; ?>" class="form-control mr-3">
        <button type="submit" class="btn btn-primary">Filter</button>
        <a href="?" class="btn btn-secondary ml-2">Reset</a>
    </form>

    <a href="" class="btn btn-outline-dark float-right mb-4" onclick="window.print()">Print</a>

    <?php if (mysqli_num_rows($result_orders) > 0): ?>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Product</th>
                    <th>Qty</th>
                    <th>Total Price</th>
                    <th>Address</th>
                    <th>Status</th>
                    <th>Payment Image</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result_orders)): ?>
                    <tr>
                        <td><?php echo $row['OrderID']; ?></td>
                        <td><?php echo $row['ProductName']; ?></td>
                        <td><?php echo $row['Quantity']; ?></td>
                        <td>RS: <?php echo number_format($row['TotalPrice'], 2); ?></td>
                        <td><?php echo $row['DeliveryAddress']; ?></td>
                        <td><?php echo $row['OrderStatus']; ?></td>
                        <td>
                            <?php
                            if ($row['PaymentStatus'] == 'paid') {
                                $oid = $row['OrderID'];
                                $trans_q = mysqli_query($conn, "SELECT PaymentImage FROM transactions WHERE OrderID = $oid");
                                if (mysqli_num_rows($trans_q) > 0) {
                                    $trans = mysqli_fetch_assoc($trans_q);
                                    echo "<img src='../buyer/{$trans['PaymentImage']}' width='100' height='100' class='img-thumbnail'>";
                                } else {
                                    echo "No Image";
                                }
                            } else {
                                echo "Unpaid";
                            }
                            ?>
                        </td>
                        <td>
                            <form method="POST">
                                <input type="hidden" name="order_id" value="<?php echo $row['OrderID']; ?>">
                                <select name="status" class="form-control">
                                    <?php foreach ($order_statuses as $status): ?>
                                        <option value="<?php echo $status; ?>" <?php if ($row['OrderStatus'] == $status) echo 'selected'; ?>><?php echo $status; ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <button type="submit" name="update_status" class="btn btn-primary mt-2">Update</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="alert alert-info">No orders found in the selected range.</div>
    <?php endif; ?>
</div>

</body>
</html>
