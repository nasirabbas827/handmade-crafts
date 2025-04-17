<?php
// start session and check if user is logged in as a buyer
session_start();
include('config.php');

if (!isset($_SESSION["id"]) || $_SESSION["usertype"] != "buyer") {
    header("location: login.php");
    exit;
}

// Retrieve all categories
$sql_categories = "SELECT id, name FROM categories";
$result_categories = mysqli_query($conn, $sql_categories);

// Retrieve all products with seller information and category name
$sql_products = "SELECT p.*, c.name AS category_name, s.StoreName, s.Location 
                FROM products p 
                JOIN stores s ON p.SellerID = s.SellerID 
                JOIN categories c ON p.CategoryID = c.id 
                WHERE p.status = 'approved'";

// Initialize search variables
$search_query = "";
$category_id = "all";
$product_name = "";
$store_name = "";
$min_price = "";
$max_price = "";

// Construct the WHERE clause based on search parameters
$where_conditions = array();
if (isset($_GET['search'])) {
    $search_query = $_GET['search'];
    if (!empty($search_query)) {
        $where_conditions[] = "(p.ProductName LIKE '%$search_query%' OR s.StoreName LIKE '%$search_query%')";
    }
}

$category_id = $_GET['category'] ?? "all";
if ($category_id != "all") {
    $where_conditions[] = "p.CategoryID = $category_id";
}

if (isset($_GET['product_name'])) {
    $product_name = $_GET['product_name'];
    if (!empty($product_name)) {
        $where_conditions[] = "p.ProductName LIKE '%$product_name%'";
    }
}

if (isset($_GET['store_name'])) {
    $store_name = $_GET['store_name'];
    if (!empty($store_name)) {
        $where_conditions[] = "s.StoreName LIKE '%$store_name%'";
    }
}

if (isset($_GET['min_price'], $_GET['max_price'])) {
    $min_price = $_GET['min_price'];
    $max_price = $_GET['max_price'];
    if (!empty($min_price) && !empty($max_price)) {
        $where_conditions[] = "p.Price BETWEEN $min_price AND $max_price";
    }
}

// Add WHERE clause to SQL query if there are any search conditions
if (!empty($where_conditions)) {
    $sql_products .= " AND " . implode(" AND ", $where_conditions);
}

$result_products = mysqli_query($conn, $sql_products);
?>

<!DOCTYPE html>
<html>

<head>
    <title>Buyer Home Page</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">
    <style>
        .card {
            transition: transform 0.3s ease-in-out;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .card:hover {
            transform: scale(1.05);
        }

        .search-bar {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .search-bar .form-control,
        .search-bar .custom-select,
        .search-bar .btn {
            margin-bottom: 10px;
        }
    </style>
</head>

<body>
    <?php include('buyer_navbar.php'); ?>

    <div class="container mt-5">
        <h2 class="mb-4">New Arrivals</h2>
        <div id="new-arrivals-carousel" class="carousel slide" data-ride="carousel">
            <div class="carousel-inner">
                <?php
                // Retrieve latest added products
                $sql_new_arrivals = "SELECT * FROM products ORDER BY Timestamp DESC LIMIT 5";
                $result_new_arrivals = mysqli_query($conn, $sql_new_arrivals);
                $products_count = mysqli_num_rows($result_new_arrivals);
                $active = true; // Set the first item as active

                // Display products in carousel format
                for ($i = 0; $i < $products_count; $i += 2) {
                    ?>
                    <div class="carousel-item <?php echo $active ? 'active' : ''; ?>">
                        <div class="row">
                            <?php
                            for ($j = $i; $j < min($i + 3, $products_count); $j++) {
                                mysqli_data_seek($result_new_arrivals, $j);
                                $row_new_arrival = mysqli_fetch_assoc($result_new_arrivals);
                                ?>
                                <div class="col-md-4">
                                    <div class="card" style="height: 100%;">
                                        <img class="card-img-top" src="../seller/products_images/<?php echo $row_new_arrival['ImageURL']; ?>" alt="Product Image" height="200px">
                                        <div class="card-body">
                                            <h5 class="card-title"><?php echo $row_new_arrival['ProductName']; ?></h5>
                                            <p class="card-text">RS: <?php echo number_format($row_new_arrival['Price'], 2); ?></p>
                                        </div>
                                    </div>
                                </div>
                                <?php
                            }
                            ?>
                        </div>
                    </div>
                    <?php
                    $active = false; // Set active to false after the first item
                }
                ?>
            </div>
            <a class="carousel-control-prev" href="#new-arrivals-carousel" role="button" data-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="sr-only">Previous</span>
            </a>
            <a class="carousel-control-next" href="#new-arrivals-carousel" role="button" data-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="sr-only">Next</span>
            </a>
        </div>
    </div>

    <div class="container mt-5">
        <h2 class="mb-4">Available Products</h2>

        <div class="search-bar">
            <form method="GET" action="">
                <div class="row">
                    <div class="col-md-3">
                        <input type="text" class="form-control" placeholder="Search by product/store name" name="search" value="<?php echo $search_query; ?>">
                    </div>
                    <div class="col-md-3">
                        <select class="custom-select" name="category">
                            <option value="all" <?php if ($category_id == "all") echo "selected"; ?>>All Categories</option>
                            <?php while ($row_category = mysqli_fetch_assoc($result_categories)) : ?>
                                <option value="<?php echo $row_category['id']; ?>" <?php if ($row_category['id'] == $category_id) echo "selected"; ?>><?php echo $row_category['name']; ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <input type="text" class="form-control" placeholder="Product name" name="product_name" value="<?php echo $product_name; ?>">
                    </div>
                    <div class="col-md-2">
                        <input type="text" class="form-control" placeholder="Store name" name="store_name" value="<?php echo $store_name; ?>">
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-2">
                        <input type="number" class="form-control" placeholder="Min price" name="min_price" value="<?php echo $min_price; ?>">
                    </div>
                    <div class="col-md-2">
                        <input type="number" class="form-control" placeholder="Max price" name="max_price" value="<?php echo $max_price; ?>">
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-outline-secondary" type="submit">Search</button>
                    </div>
                </div>
            </form>
        </div>

        <?php if (mysqli_num_rows($result_products) > 0) : ?>
            <div class="row">
                <?php while ($row = mysqli_fetch_assoc($result_products)) : ?>
                    <div class="col-md-4 mb-4 d-flex">
                        <div class="card flex-fill d-flex flex-column">
                            <img class="card-img-top" src="../seller/products_images/<?php echo $row['ImageURL']; ?>" alt="Product Image" height="200px" width="100%">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo $row['ProductName']; ?></h5>
                                <p class="card-text">Category: <?php echo $row['category_name']; ?></p>
                                <p class="card-text">Description: <?php echo $row['Description']; ?></p>
                                <p class="card-text">Quantity Available: <?php echo $row['StockQuantity']; ?></p>
                                <p class="card-text"><strong>Price:</strong> RS: <?php echo number_format($row['Price'], 2); ?></p>

                                <form action="add_to_cart.php" method="post" class="add-to-cart-form">
                                    <input type="hidden" name="product_id" value="<?php echo $row['ProductID']; ?>">
                                    <input type="hidden" class="max-quantity" value="<?php echo $row['StockQuantity']; ?>">
                                    <div class="form-group">
                                        <label for="quantity">Quantity:</label>
                                        <input type="number" class="form-control quantity" name="quantity" value="1" min="1">
                                    </div>
                                    <button type="submit" class="btn btn-primary add-to-cart-btn" name="add_to_cart">Add to Cart</button>
                                    <a href="view_ratings_reviews.php?product_id=<?php echo $row['ProductID']; ?>" class="btn btn-primary">Reviews</a>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else : ?>
            <div class="alert alert-info" role="alert">No products found matching the search criteria.</div>
        <?php endif; ?>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const forms = document.querySelectorAll('.add-to-cart-form');
            forms.forEach(form => {
                form.addEventListener('submit', function(event) {
                    const quantityInput = form.querySelector('.quantity');
                    const maxQuantity = parseInt(form.querySelector('.max-quantity').value);
                    const enteredQuantity = parseInt(quantityInput.value);
                    if (enteredQuantity > maxQuantity) {
                        event.preventDefault(); // Prevent form submission
                        alert(`Quantity exceeds available stock (${maxQuantity}) for this product.`);
                    }
                });
            });
        });
    </script>

<div class="container mt-5">
    <h2 class="mb-4">Announcements</h2>
    <div class="row">
        <?php
        // Retrieve all active announcements
        $sql_announcements = "SELECT * FROM announcements WHERE status = 'active' ORDER BY created_at DESC";
        $result_announcements = mysqli_query($conn, $sql_announcements);
        
        if (mysqli_num_rows($result_announcements) > 0) :
            while ($row_announcement = mysqli_fetch_assoc($result_announcements)) :
        ?>
                <div class="col-md-4 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($row_announcement['title']); ?></h5>
                            <p class="card-text"><?php echo nl2br(htmlspecialchars($row_announcement['content'])); ?></p>
                            <p class="card-text"><small class="text-muted">Posted on: <?php echo date('F j, Y', strtotime($row_announcement['created_at'])); ?></small></p>
                        </div>
                    </div>
                </div>
        <?php
            endwhile;
        else :
        ?>
            <div class="col-12">
                <div class="alert alert-info" role="alert">No announcements available at the moment.</div>
            </div>
        <?php
        endif;
        ?>
    </div>
</div>


    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>

  <footer class="footer bg-dark text-white py-4 mt-5">
  <div class="container">
    
    <div class="row mt-3">
      <div class="col">
        <p class="text-center mb-0">&copy; 2025 Your Online HandCraft Haven Project. All rights reserved.</p>
      </div>
    </div>
  </div>
</footer>

  <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rGIO3CjEm4C4jXCDAjz3fOxEqGzX6s8EcddP3p6Mv9O+frC6f2" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJcmw3gZ/Fl7EynXDobJ14zKPF3/P6E8F81Gqn6f4U5sok/Q5gRV2" crossorigin="anonymous"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+b0WYbCr" crossorigin="anonymous"></script>
</body>
</html>

<?php
// Close the database connection
$conn->close();
?>