<?php
// Start session and check if user is logged in as seller
session_start();
include 'config.php';

if (!isset($_SESSION["id"]) || $_SESSION["usertype"] != "seller") {
    header("location: ../login.php");
    exit;
}

$seller_id = $_SESSION["id"];

// Fetch seller's store information
$store_query = "SELECT * FROM stores WHERE SellerID = $seller_id";
$store_result = mysqli_query($conn, $store_query);
$store = mysqli_fetch_assoc($store_result);

// Fetch latest custom requests
$custom_requests_query = "SELECT cr.*, u.username as customer_name 
                         FROM custom_requests cr 
                         JOIN users u ON cr.customer_id = u.id 
                         WHERE cr.seller_id = $seller_id 
                         ORDER BY cr.created_at DESC 
                         LIMIT 5";
$custom_requests_result = mysqli_query($conn, $custom_requests_query);

// Fetch latest orders
$orders_query = "SELECT o.*, u.username 
                FROM orders o 
                JOIN order_items oi ON o.OrderID = oi.OrderID 
                JOIN products p ON oi.ProductID = p.ProductID 
                JOIN users u ON o.UserID = u.id
                WHERE p.SellerID = $seller_id 
                GROUP BY o.OrderID 
                ORDER BY o.OrderTime DESC 
                LIMIT 5";
$orders_result = mysqli_query($conn, $orders_query);

// Fetch latest products
$products_query = "SELECT * FROM products WHERE SellerID = $seller_id ORDER BY Timestamp DESC LIMIT 5";
$products_result = mysqli_query($conn, $products_query);

// Count total products, orders, and custom requests
$total_products_query = "SELECT COUNT(*) as total FROM products WHERE SellerID = $seller_id";
$total_products_result = mysqli_query($conn, $total_products_query);
$total_products = mysqli_fetch_assoc($total_products_result)['total'];

$total_orders_query = "SELECT COUNT(DISTINCT o.OrderID) as total 
                      FROM orders o 
                      JOIN order_items oi ON o.OrderID = oi.OrderID 
                      JOIN products p ON oi.ProductID = p.ProductID 
                      WHERE p.SellerID = $seller_id";
$total_orders_result = mysqli_query($conn, $total_orders_query);
$total_orders = mysqli_fetch_assoc($total_orders_result)['total'];

$total_requests_query = "SELECT COUNT(*) as total FROM custom_requests WHERE seller_id = $seller_id";
$total_requests_result = mysqli_query($conn, $total_requests_query);
$total_requests = mysqli_fetch_assoc($total_requests_result)['total'];

// Get pending orders count
$pending_orders_query = "SELECT COUNT(DISTINCT o.OrderID) as total 
                        FROM orders o 
                        JOIN order_items oi ON o.OrderID = oi.OrderID 
                        JOIN products p ON oi.ProductID = p.ProductID 
                        WHERE p.SellerID = $seller_id AND o.OrderStatus = 'Pending'";
$pending_orders_result = mysqli_query($conn, $pending_orders_query);
$pending_orders = mysqli_fetch_assoc($pending_orders_result)['total'];

// Get pending custom requests count
$pending_requests_query = "SELECT COUNT(*) as total FROM custom_requests WHERE seller_id = $seller_id AND status = 'pending'";
$pending_requests_result = mysqli_query($conn, $pending_requests_query);
$pending_requests = mysqli_fetch_assoc($pending_requests_result)['total'];
?>

<!DOCTYPE html>
<html>
<head>
  <title>Seller Dashboard</title>
  <!-- Add Bootstrap CSS -->
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
  <!-- Add Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
  <link rel="stylesheet" href="./css/style.css">
  <style>
    .dashboard-card {
      border-radius: 10px;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
      transition: transform 0.3s ease;
      height: 100%;
    }
    .dashboard-card:hover {
      transform: translateY(-5px);
    }
    .stat-card {
      border-left: 4px solid;
      border-radius: 8px;
    }
    .bg-gradient-primary {
      background: linear-gradient(to right, #4e73df, #224abe);
      color: white;
    }
    .bg-gradient-success {
      background: linear-gradient(to right, #1cc88a, #13855c);
      color: white;
    }
    .bg-gradient-info {
      background: linear-gradient(to right, #36b9cc, #258391);
      color: white;
    }
    .bg-gradient-warning {
      background: linear-gradient(to right, #f6c23e, #dda20a);
      color: white;
    }
    .sidebar {
      background-color: #4e73df;
      background-image: linear-gradient(180deg, #4e73df 10%, #224abe 100%);
      background-size: cover;
      min-height: 100vh;
    }
    .sidebar .nav-link {
      color: rgba(255, 255, 255, 0.8);
      padding: 1rem;
      font-weight: 500;
    }
    .sidebar .nav-link:hover {
      color: #fff;
      background-color: rgba(255, 255, 255, 0.1);
    }
    .sidebar .nav-link.active {
      color: #fff;
      font-weight: 700;
      background-color: rgba(255, 255, 255, 0.2);
    }
    .sidebar .nav-link i {
      margin-right: 0.5rem;
    }
    .table-responsive {
      border-radius: 8px;
      overflow: hidden;
    }
    .badge-status {
      padding: 0.5em 0.75em;
      border-radius: 30px;
    }
    .store-card {
      background-size: cover;
      background-position: center;
      color: white;
      position: relative;
      z-index: 1;
      border-radius: 10px;
      overflow: hidden;
    }
    .store-card::before {
      content: "";
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0, 0, 0, 0.5);
      z-index: -1;
    }
    .section-title {
      position: relative;
      padding-bottom: 10px;
      margin-bottom: 20px;
    }
    .section-title::after {
      content: "";
      position: absolute;
      bottom: 0;
      left: 0;
      width: 50px;
      height: 3px;
      background-color: #4e73df;
    }
  </style>
</head>
<body>

<?php include('seller_navbar.php'); ?>

<div class="container-fluid">
  <div class="row">
    <!-- Sidebar -->
    <div class="col-md-3 col-lg-2 d-md-block sidebar collapse">
      <div class="position-sticky pt-3">
        <ul class="nav flex-column">
          <li class="nav-item">
            <a class="nav-link active" href="seller_home.php">
              <i class="fas fa-tachometer-alt"></i> Dashboard
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="seller_product.php">
              <i class="fas fa-shopping-bag"></i> My Products
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="orders.php">
              <i class="fas fa-receipt"></i> Orders
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="seller_custom_requests.php">
              <i class="fas fa-clipboard-list"></i> Custom Requests
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="update_profile.php">
              <i class="fas fa-user-cog"></i> Profile Settings
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="logout.php">
              <i class="fas fa-sign-out-alt"></i> Logout
            </a>
          </li>
        </ul>
      </div>
    </div>

    <!-- Main Content -->
    <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
      <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Seller Dashboard</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
          <div class="btn-group me-2">
            <button type="button" class="btn btn-sm btn-outline-primary" onclick="window.location.href='seller_product.php'">
              <i class="fas fa-plus"></i> Add New Product
            </button>
          </div>
        </div>
      </div>

      <!-- Store Information -->
      <?php if ($store): ?>
      <div class="row mb-4">
        <div class="col-12">
          <div class="store-card dashboard-card p-4" style="background-image: url('https://images.unsplash.com/photo-1472851294608-062f824d29cc?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80');">
            <h3><?php echo $store['StoreName']; ?></h3>
            <p><?php echo $store['Description']; ?></p>
            <p><i class="fas fa-map-marker-alt"></i> <?php echo $store['Location']; ?></p>
            <a href="view_store.php" class="btn btn-light btn-sm"><i class="fas fa-edit"></i> Edit Store</a>
          </div>
        </div>
      </div>
      <?php else: ?>
      <div class="row mb-4">
        <div class="col-12">
          <div class="alert alert-info">
            <h4>You haven't set up your store yet!</h4>
            <p>Create your store to start selling products.</p>
            <a href="create_store.php" class="btn btn-primary">Create Store</a>
          </div>
        </div>
      </div>
      <?php endif; ?>

      <!-- Stats Cards -->
      <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
          <div class="card border-left-primary shadow h-100 py-2 dashboard-card">
            <div class="card-body">
              <div class="row no-gutters align-items-center">
                <div class="col mr-2">
                  <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Products</div>
                  <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $total_products; ?></div>
                </div>
                <div class="col-auto">
                  <i class="fas fa-shopping-bag fa-2x text-gray-300"></i>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
          <div class="card border-left-success shadow h-100 py-2 dashboard-card">
            <div class="card-body">
              <div class="row no-gutters align-items-center">
                <div class="col mr-2">
                  <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Orders</div>
                  <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $total_orders; ?></div>
                </div>
                <div class="col-auto">
                  <i class="fas fa-receipt fa-2x text-gray-300"></i>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
          <div class="card border-left-info shadow h-100 py-2 dashboard-card">
            <div class="card-body">
              <div class="row no-gutters align-items-center">
                <div class="col mr-2">
                  <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Pending Orders</div>
                  <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $pending_orders; ?></div>
                </div>
                <div class="col-auto">
                  <i class="fas fa-clock fa-2x text-gray-300"></i>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
          <div class="card border-left-warning shadow h-100 py-2 dashboard-card">
            <div class="card-body">
              <div class="row no-gutters align-items-center">
                <div class="col mr-2">
                  <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Pending Requests</div>
                  <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $pending_requests; ?></div>
                </div>
                <div class="col-auto">
                  <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="row">
        <!-- Latest Custom Requests -->
        <div class="col-lg-6 mb-4">
          <div class="card shadow mb-4 dashboard-card">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
              <h6 class="m-0 font-weight-bold text-primary">Latest Custom Requests</h6>
              <a href="seller_custom_requests.php" class="btn btn-sm btn-primary">View All</a>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-striped">
                  <thead>
                    <tr>
                      <th>Title</th>
                      <th>Customer</th>
                      <th>Status</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php if (mysqli_num_rows($custom_requests_result) > 0): ?>
                      <?php while ($request = mysqli_fetch_assoc($custom_requests_result)): ?>
                        <tr>
                          <td><?php echo $request['title']; ?></td>
                          <td><?php echo $request['customer_name']; ?></td>
                          <td>
                            <?php 
                              $status_class = '';
                              switch($request['status']) {
                                case 'pending':
                                  $status_class = 'badge-warning';
                                  break;
                                case 'accepted':
                                  $status_class = 'badge-success';
                                  break;
                                case 'declined':
                                  $status_class = 'badge-danger';
                                  break;
                                case 'in progress':
                                  $status_class = 'badge-info';
                                  break;
                                case 'completed':
                                  $status_class = 'badge-primary';
                                  break;
                              }
                            ?>
                            <span class="badge <?php echo $status_class; ?> badge-status"><?php echo ucfirst($request['status']); ?></span>
                          </td>
                          <td>
                            <a href="seller_custom_requests.php" class="btn btn-sm btn-info">
                              <i class="fas fa-eye"></i>
                            </a>
                          </td>
                        </tr>
                      <?php endwhile; ?>
                    <?php else: ?>
                      <tr>
                        <td colspan="4" class="text-center">No custom requests found</td>
                      </tr>
                    <?php endif; ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>

        <!-- Latest Orders -->
        <div class="col-lg-6 mb-4">
          <div class="card shadow mb-4 dashboard-card">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
              <h6 class="m-0 font-weight-bold text-primary">Latest Orders</h6>
              <a href="orders.php" class="btn btn-sm btn-primary">View All</a>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-striped">
                  <thead>
                    <tr>
                      <th>Order ID</th>
                      <th>Customer</th>
                      <th>Total</th>
                      <th>Status</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php if (mysqli_num_rows($orders_result) > 0): ?>
                      <?php while ($order = mysqli_fetch_assoc($orders_result)): ?>
                        <tr>
                          <td>#<?php echo $order['OrderID']; ?></td>
                          <td><?php echo $order['username']; ?></td>
                          <td>$<?php echo number_format($order['TotalPrice'], 2); ?></td>
                          <td>
                            <?php 
                              $status_class = '';
                              switch($order['OrderStatus']) {
                                case 'Pending':
                                  $status_class = 'badge-warning';
                                  break;
                                case 'Processing':
                                  $status_class = 'badge-info';
                                  break;
                                case 'Shipped':
                                  $status_class = 'badge-primary';
                                  break;
                                case 'Delivered':
                                  $status_class = 'badge-success';
                                  break;
                                case 'Cancelled':
                                  $status_class = 'badge-danger';
                                  break;
                              }
                            ?>
                            <span class="badge <?php echo $status_class; ?> badge-status"><?php echo $order['OrderStatus']; ?></span>
                          </td>
                          <td>
                            <a href="view_order.php?id=<?php echo $order['OrderID']; ?>" class="btn btn-sm btn-info">
                              <i class="fas fa-eye"></i>
                            </a>
                          </td>
                        </tr>
                      <?php endwhile; ?>
                    <?php else: ?>
                      <tr>
                        <td colspan="5" class="text-center">No orders found</td>
                      </tr>
                    <?php endif; ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Latest Products -->
      <div class="row">
        <div class="col-12">
          <div class="card shadow mb-4 dashboard-card">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
              <h6 class="m-0 font-weight-bold text-primary">Latest Products</h6>
              <a href="seller_product.php" class="btn btn-sm btn-primary">View All</a>
            </div>
            <div class="card-body">
              <div class="row">
                <?php if (mysqli_num_rows($products_result) > 0): ?>
                  <?php while ($product = mysqli_fetch_assoc($products_result)): ?>
                    <div class="col-md-4 mb-4">
                      <div class="card h-100 dashboard-card">
                        <img src="products_images/<?php echo $product['ImageURL'] ? $product['ImageURL'] : 'https://via.placeholder.com/150'; ?>" class="card-img-top" alt="<?php echo $product['ProductName']; ?>" style="height: 200px; object-fit: cover;">
                        <div class="card-body">
                          <h5 class="card-title"><?php echo $product['ProductName']; ?></h5>
                          <p class="card-text text-muted"><?php echo substr($product['Description'], 0, 100) . '...'; ?></p>
                          <div class="d-flex justify-content-between align-items-center">
                            <span class="font-weight-bold text-primary">$<?php echo number_format($product['Price'], 2); ?></span>
                            <span class="badge badge-<?php echo $product['status'] == 'approved' ? 'success' : ($product['status'] == 'pending' ? 'warning' : 'danger'); ?> badge-status">
                              <?php echo ucfirst($product['status']); ?>
                            </span>
                          </div>
                        </div>
                        <div class="card-footer bg-transparent">
                          <div class="d-flex justify-content-between">
                            <small class="text-muted">Stock: <?php echo $product['StockQuantity']; ?></small>
                            <a href="edit_product.php?id=<?php echo $product['ProductID']; ?>" class="btn btn-sm btn-outline-primary">
                              <i class="fas fa-edit"></i> Edit
                            </a>
                          </div>
                        </div>
                      </div>
                    </div>
                  <?php endwhile; ?>
                <?php else: ?>
                  <div class="col-12">
                    <div class="alert alert-info">
                      <p>You haven't added any products yet. <a href="add_product.php">Add your first product</a></p>
                    </div>
                  </div>
                <?php endif; ?>
              </div>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>
</div>

<!-- Add Bootstrap JS and dependencies -->
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
</body>
</html>

<?php mysqli_close($conn); ?>