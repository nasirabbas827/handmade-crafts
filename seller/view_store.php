<?php
session_start();
include 'config.php';

if (!isset($_SESSION["id"]) || $_SESSION["usertype"] != "seller") {
    header("location: login.php");
    exit;
}

$seller_id = $_SESSION['id'];

// Fetch store information
$sql_store = "SELECT * FROM stores WHERE SellerID = ?";
$stmt_store = mysqli_prepare($conn, $sql_store);
mysqli_stmt_bind_param($stmt_store, "i", $seller_id);
mysqli_stmt_execute($stmt_store);
$result_store = mysqli_stmt_get_result($stmt_store);
$row_store = mysqli_fetch_assoc($result_store);

// Handle form submission for editing store
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['edit_store'])) {
        $store_name = $_POST['store_name'];
        $description = $_POST['description'];
        $location = $_POST['location'];

        $sql_update_store = "UPDATE stores SET StoreName = ?, Description = ?, Location = ? WHERE SellerID = ?";
        $stmt_update_store = mysqli_prepare($conn, $sql_update_store);
        mysqli_stmt_bind_param($stmt_update_store, "sssi", $store_name, $description, $location, $seller_id);
        mysqli_stmt_execute($stmt_update_store);

        // Redirect to view store page after updating store
        header("location: view_store.php");
        exit;
    }

    // Handle form submission for deleting store
    if (isset($_POST['delete_store'])) {
        $sql_delete_store = "DELETE FROM stores WHERE SellerID = ?";
        $stmt_delete_store = mysqli_prepare($conn, $sql_delete_store);
        mysqli_stmt_bind_param($stmt_delete_store, "i", $seller_id);
        mysqli_stmt_execute($stmt_delete_store);

        // Redirect to home page after deleting store
        header("location: seller_home.php");
        exit;
    }
}

mysqli_stmt_close($stmt_store);
mysqli_close($conn);
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Store</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">
</head>
<body>
    <?php include 'seller_navbar.php'; ?>

    <div class="container mt-5 mb-5">
        <h2 class="text-center">Your Store</h2>
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                            <div class="form-group">
                                <label for="store_name">Store Name</label>
                                <input type="text" class="form-control" name="store_name" value="<?php echo $row_store['StoreName']; ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="description">Description</label>
                                <textarea class="form-control" name="description" rows="3"><?php echo $row_store['Description']; ?></textarea>
                            </div>
                            <div class="form-group">
                                <label for="location">Location</label>
                                <input type="text" class="form-control" name="location" value="<?php echo $row_store['Location']; ?>" required>
                            </div>
                            <button type="submit" class="btn btn-primary" name="edit_store">Save Changes</button>
                            <button type="submit" class="btn btn-danger" name="delete_store">Delete Store</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
