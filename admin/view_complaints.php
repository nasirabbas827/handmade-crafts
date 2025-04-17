<?php
session_start();
include('config.php');

// Check if user is logged in as admin
if (!isset($_SESSION["id"]) || $_SESSION["usertype"] != "admin") {
    header("location: admin_login.php");
    exit;
}

// Handle delete request
if (isset($_GET['delete'])) {
    $complaintID = intval($_GET['delete']);
    $sql_delete = "DELETE FROM complaints WHERE ComplaintID = $complaintID";
    mysqli_query($conn, $sql_delete);
    header("Location: view_complaints.php");
    exit;
}

// Fetch all complaints
$sql_complaints = "SELECT c.ComplaintID, u.Username, c.ComplaintReason, c.Text, c.SubmissionDate 
                   FROM complaints c 
                   INNER JOIN users u ON c.UserID = u.id";

$result_complaints = mysqli_query($conn, $sql_complaints);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - View Complaints</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">
</head>
<body>
    <?php include('admin_navbar.php'); ?>

    <div class="container mt-4">
        <h2>View Complaints</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Complaint ID</th>
                    <th>User</th>
                    <th>Reason</th>
                    <th>Text</th>
                    <th>Submission Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result_complaints)) : ?>
                    <tr>
                        <td><?php echo $row['ComplaintID']; ?></td>
                        <td><?php echo $row['Username']; ?></td>
                        <td><?php echo $row['ComplaintReason']; ?></td>
                        <td><?php echo $row['Text']; ?></td>
                        <td><?php echo $row['SubmissionDate']; ?></td>
                        <td>
                            <a href="?delete=<?php echo $row['ComplaintID']; ?>" 
                               class="btn btn-danger btn-sm" 
                               onclick="return confirm('Are you sure you want to delete this complaint?');">
                               Delete
                            </a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
</body>
</html>
