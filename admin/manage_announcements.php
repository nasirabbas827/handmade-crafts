<?php
session_start();
include 'config.php';

// Check if the user is an admin
if (!isset($_SESSION["id"]) || $_SESSION["usertype"] != "admin") {
    header("location: admin_login.php");
    exit;
}

// Handle Announcement Deletion
if (isset($_GET['delete'])) {
    $announcement_id = $_GET['delete'];

    $sql_delete = "DELETE FROM announcements WHERE id = $announcement_id";
    if (mysqli_query($conn, $sql_delete)) {
        $success_message = "Announcement deleted successfully.";
    } else {
        $error_message = "Error deleting announcement: " . mysqli_error($conn);
    }
}

// Handle Announcement Submission (Add or Update)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $content = mysqli_real_escape_string($conn, $_POST['content']);

    // Insert a new announcement into the database
    $sql_insert = "INSERT INTO announcements (title, content, status, created_at, updated_at) 
                   VALUES ('$title', '$content', 'active', NOW(), NOW())";

    if (mysqli_query($conn, $sql_insert)) {
        $success_message = "Announcement added successfully.";
    } else {
        $error_message = "Error adding announcement: " . mysqli_error($conn);
    }
}

// Fetch existing announcements
$sql_fetch = "SELECT * FROM announcements ORDER BY announcement_date DESC";
$result = mysqli_query($conn, $sql_fetch);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Admin - Announcements</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
  <link rel="stylesheet" href="./css/style.css">

</head>
<body>
<?php include 'admin_navbar.php'; ?>

<div class="container mt-4">
    <div class="card mx-auto" style="max-width: 700px;">
        <div class="card-body">
            <h2>Manage Announcements</h2>

            <?php if (isset($success_message)) : ?>
                <div class="alert alert-success"><?php echo $success_message; ?></div>
            <?php endif; ?>
            <?php if (isset($error_message)) : ?>
                <div class="alert alert-danger"><?php echo $error_message; ?></div>
            <?php endif; ?>

            <!-- Add Announcement Form -->
            <form method="post">
                <div class="form-group">
                    <label>Title</label>
                    <input type="text" name="title" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Content</label>
                    <textarea name="content" class="form-control" rows="4" required></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Add Announcement</button>
            </form>
        </div>
    </div>

    <!-- Display Announcements -->
    <h4 class="mt-4">Existing Announcements</h4>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Title</th>
                <th>Content</th>
                <th>Date</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                <tr>
                    <td><?php echo $row['title']; ?></td>
                    <td><?php echo htmlspecialchars($row['content']); ?></td>
                    <td><?php echo $row['announcement_date']; ?></td>
                    <td><?php echo ucfirst($row['status']); ?></td>
                    <td>
                        <a href="?delete=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this announcement?')">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
</body>
</html>
