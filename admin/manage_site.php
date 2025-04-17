<?php
session_start();
include 'config.php';

if (!isset($_SESSION["id"]) || $_SESSION["usertype"] != "admin") {
  header("location: admin_login.php");
  exit;
}

$target_dir = "images/";
$content_id = 1; // We'll always use ID 1 for single-entry content

// Handle content submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $title = mysqli_real_escape_string($conn, $_POST['title']);
  $content = mysqli_real_escape_string($conn, $_POST['content']);
  
  // Handle image upload
  if (isset($_FILES["image"]["name"]) && $_FILES["image"]["name"] != "") {
    $image_name = basename($_FILES["image"]["name"]);
    $target_file = $target_dir . $image_name;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    
    // Validate image type
    $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
    if (in_array($imageFileType, $allowed_types)) {
      if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
        $image_url = $target_file;

        // Check if content exists
        $sql_check = "SELECT * FROM site_content WHERE id = $content_id";
        $result_check = mysqli_query($conn, $sql_check);

        if (mysqli_num_rows($result_check) > 0) {
          $sql_update = "UPDATE site_content 
                         SET title='$title', content='$content', image_url='$image_url', updated_at=NOW() 
                         WHERE id=$content_id";
          $success_message = mysqli_query($conn, $sql_update) ? "Content updated successfully." : "Error updating: " . mysqli_error($conn);
        } else {
          $sql_insert = "INSERT INTO site_content (id, content_type, title, content, image_url, created_at, updated_at)
                         VALUES ($content_id, 'main', '$title', '$content', '$image_url', NOW(), NOW())";
          $success_message = mysqli_query($conn, $sql_insert) ? "Content added successfully." : "Error inserting: " . mysqli_error($conn);
        }
      } else {
        $error_message = "Image upload failed.";
      }
    } else {
      $error_message = "Invalid image file type.";
    }
  } else {
    $error_message = "Image is required.";
  }
}

// Fetch current content
$sql = "SELECT * FROM site_content WHERE id = $content_id";
$result = mysqli_query($conn, $sql);
$current_content = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html>
<head>
  <title>Admin - Site Content</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
  <link rel="stylesheet" href="./css/style.css">

</head>
<body>
<?php include 'admin_navbar.php'; ?>
<div class="container mt-4">
  <div class="card mx-auto" style="max-width: 700px;">
    <div class="card-body">
      <h2>Manage Site Content</h2>

      <?php if (isset($success_message)) : ?>
        <div class="alert alert-success"><?php echo $success_message; ?></div>
      <?php endif; ?>
      <?php if (isset($error_message)) : ?>
        <div class="alert alert-danger"><?php echo $error_message; ?></div>
      <?php endif; ?>

      <form method="post" enctype="multipart/form-data">
        <div class="form-group">
          <label>Title</label>
          <input type="text" name="title" class="form-control" value="<?php echo $current_content['title'] ?? ''; ?>" required>
        </div>
        <div class="form-group">
          <label>Paragraph</label>
          <textarea name="content" class="form-control" rows="4" required><?php echo $current_content['content'] ?? ''; ?></textarea>
        </div>
        <div class="form-group">
          <label>Upload Image</label>
          <input type="file" name="image" class="form-control-file" <?php echo empty($current_content['image_url']) ? 'required' : ''; ?>>
          <?php if (!empty($current_content['image_url'])): ?>
            <img src="<?php echo $current_content['image_url']; ?>" alt="Current Image" class="img-thumbnail mt-2" width="150">
          <?php endif; ?>
        </div>
        <button type="submit" class="btn btn-primary">Save Content</button>
      </form>
    </div>
  </div>
</div>
</body>
</html>
