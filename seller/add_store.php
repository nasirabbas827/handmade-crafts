<?php
// Start session and check if user is logged in as seller
session_start();
include 'config.php';

if (!isset($_SESSION["id"]) || $_SESSION["usertype"] != "seller") {
    header("location: login.php");
    exit;
}

// Check if the seller already has a store
$sql_check_store = "SELECT * FROM stores WHERE SellerID = ?";
$stmt_check_store = mysqli_prepare($conn, $sql_check_store);
mysqli_stmt_bind_param($stmt_check_store, "i", $_SESSION['id']);
mysqli_stmt_execute($stmt_check_store);
$result_check_store = mysqli_stmt_get_result($stmt_check_store);

if (mysqli_num_rows($result_check_store) > 0) {
    header("location: view_store.php");
    exit;
}

// If form is submitted, create store
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $store_name = $_POST['store_name'];
    $description = $_POST['description'];
    $location = $_POST['location'];

    // Insert store into the database
    $sql_insert_store = "INSERT INTO stores (StoreName, Description, Location, SellerID) VALUES (?, ?, ?, ?)";
    $stmt_insert_store = mysqli_prepare($conn, $sql_insert_store);
    mysqli_stmt_bind_param($stmt_insert_store, "sssi", $store_name, $description, $location, $_SESSION['id']);
    mysqli_stmt_execute($stmt_insert_store);
    mysqli_stmt_close($stmt_insert_store);

    header("location: view_store.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Create Store</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">
</head>
<body>
    <?php include 'seller_navbar.php'; ?>

    <div class="container mt-5 mb-5">
    <div class="card mx-auto" style="max-width: 600px;">
    <div class="card-body">
        <h2 class="text-center">Create Store</h2>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group">
                <label>Store Name</label>
                <input type="text" class="form-control" name="store_name" required>
            </div>
            <div class="form-group">
                <label>Description</label>
                <textarea class="form-control" name="description" required></textarea>
            </div>
            <div class="form-group">
                <label>Location</label>
                <input type="text" class="form-control" name="location" required>
            </div>
            <button type="submit" class="btn btn-primary">Create Store</button>
        </form>
    </div>
    </div>
    </div>
</body>
</html>
