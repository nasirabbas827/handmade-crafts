<!-- Navigation Bar -->
<nav class="navbar navbar-expand-md bg-dark navbar-dark">
    <a class="navbar-brand" href="seller_home.php">Seller Dashboard</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#collapsibleNavbar">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="collapsibleNavbar">
      <ul class="navbar-nav ml-auto">
      <li class="nav-item">
          <a class="nav-link" href="update_profile.php"><?php echo $_SESSION["email"]; ?></a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="add_store.php">Store</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="seller_product.php">My Products</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="orders.php">Orders</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="seller_custom_requests.php">Custom Requests</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="view_reviews.php">Reviews</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="logout.php">Logout</a>
        </li>
      </ul>
    </div>
  </nav>