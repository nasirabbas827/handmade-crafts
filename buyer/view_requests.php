<?php
session_start();
include('config.php');

if (!isset($_SESSION["id"])) {
    header("location: login.php");
    exit;
}

$user_id = $_SESSION['id'];

// Fetch all custom design requests for the logged-in user
$sql_requests = "SELECT cr.id, cr.title, cr.details, cr.estimated_price, cr.status, cr.seller_response_message, s.StoreName
                 FROM custom_requests cr
                 JOIN stores s ON cr.seller_id = s.SellerID
                 WHERE cr.customer_id = $user_id
                 ORDER BY cr.created_at DESC";

$result_requests = mysqli_query($conn, $sql_requests);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Your Custom Design Requests</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">
</head>
<body>
<?php include('buyer_navbar.php'); ?>

<div class="container mt-5">
    <h2>Your Custom Design Requests</h2>

    <?php if (mysqli_num_rows($result_requests) > 0): ?>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Request Title</th>
                    <th>Store Name</th>
                    <th>Details</th>
                    <th>Estimated Price</th>
                    <th>Status</th>
                    <th>Seller Response</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result_requests)): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['id']); ?></td>
                        <td><?php echo htmlspecialchars($row['title']); ?></td>
                        <td><?php echo htmlspecialchars($row['StoreName']); ?></td>
                        <td><?php echo nl2br(htmlspecialchars($row['details'])); ?></td>
                        <td>RS: <?php echo number_format($row['estimated_price'], 2); ?></td>
                        <td><?php echo ucfirst($row['status']); ?></td>
                        <td><?php echo !empty($row['seller_response_message']) ? nl2br(htmlspecialchars($row['seller_response_message'])) : 'No response yet'; ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="alert alert-info">You have not made any custom design requests yet.</div>
    <?php endif; ?>
</div>

<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
</body>
</html>
