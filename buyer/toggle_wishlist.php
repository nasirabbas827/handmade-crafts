<?php
session_start();
include('config.php');

if (!isset($_SESSION['id']) || !isset($_POST['product_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['id'];
$product_id = $_POST['product_id'];

// Check if the product is already in wishlist
$check_sql = "SELECT * FROM wishlists WHERE user_id = $user_id AND product_id = $product_id";
$result = mysqli_query($conn, $check_sql);

if (mysqli_num_rows($result) > 0) {
    // Remove from wishlist
    $delete_sql = "DELETE FROM wishlists WHERE user_id = $user_id AND product_id = $product_id";
    mysqli_query($conn, $delete_sql);
} else {
    // Add to wishlist
    $insert_sql = "INSERT INTO wishlists (user_id, product_id) VALUES ($user_id, $product_id)";
    mysqli_query($conn, $insert_sql);
}

header("Location: view_ratings_reviews.php?product_id=$product_id");
exit;
