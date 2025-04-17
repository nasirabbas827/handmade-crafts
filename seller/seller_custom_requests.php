<?php
session_start();
if (!isset($_SESSION["id"]) || $_SESSION["usertype"] != "seller") {
    header("location: ../login.php");
    exit;
}

include 'config.php';
$seller_id = $_SESSION['id'];

// Update handler
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['request_id'])) {
    $request_id = intval($_POST['request_id']);
    $estimated_price = floatval($_POST['estimated_price']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    $seller_response = mysqli_real_escape_string($conn, $_POST['seller_response_message']);

    $update_sql = "UPDATE custom_requests 
                   SET estimated_price='$estimated_price', status='$status', seller_response_message='$seller_response' 
                   WHERE id='$request_id' AND seller_id='$seller_id'";
    mysqli_query($conn, $update_sql);
}

// Fetch seller's custom design requests
$sql = "SELECT * FROM custom_requests WHERE seller_id = '$seller_id' ORDER BY created_at DESC";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Seller Custom Requests</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">
</head>
<body>
<?php include('seller_navbar.php'); ?>

<div class="container mt-5">
    <h2>Custom Design Requests</h2>

    <?php if (mysqli_num_rows($result) > 0): ?>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Title</th>
                    <th>Details</th>
                    <th>Estimated Price</th>
                    <th>Status</th>
                    <th>Response</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <form method="POST">
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo htmlspecialchars($row['title']); ?></td>
                            <td><?php echo nl2br(htmlspecialchars($row['details'])); ?></td>
                            <td>
                                <input type="number" name="estimated_price" value="<?php echo $row['estimated_price']; ?>" class="form-control" step="0.01" required>
                            </td>
                            <td>
                                <select name="status" class="form-control" required>
                                    <option value="pending" <?= $row['status'] == 'pending' ? 'selected' : '' ?>>Pending</option>
                                    <option value="accepted" <?= $row['status'] == 'accepted' ? 'selected' : '' ?>>Accepted</option>
                                    <option value="declined" <?= $row['status'] == 'declined' ? 'selected' : '' ?>>Declined</option>
                                    <option value="in progress" <?= $row['status'] == 'in progress' ? 'selected' : '' ?>>In Progress</option>
                                    <option value="completed" <?= $row['status'] == 'completed' ? 'selected' : '' ?>>Completed</option>
                                </select>
                            </td>
                            <td>
                                <textarea name="seller_response_message" class="form-control" rows="2"><?php echo htmlspecialchars($row['seller_response_message']); ?></textarea>
                            </td>
                            <td>
                                <input type="hidden" name="request_id" value="<?php echo $row['id']; ?>">
                                <button type="submit" class="btn btn-sm btn-success">Update</button>
                            </td>
                        </form>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="alert alert-info">No custom requests found for you.</div>
    <?php endif; ?>
</div>

</body>
</html>

<?php mysqli_close($conn); ?>
