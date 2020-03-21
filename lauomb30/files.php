<?php
// Initialize the session
session_start();

// If session variable is not set it will redirect to login page
if(!isset($_SESSION['username']) || empty($_SESSION['username']) || !in_array("15", $_SESSION['authorizations'])){
  header("location: login.php");
  exit;
}
?>

<!DOCTYPE html>
<html>
<head>
  <?php include 'head.php'; ?>
  <script type="text/javascript" src="js/drag-drop-upload.js"></script>
</head>
<body>

<?php include 'navbar.php';?>

<div class="container-fluid">
  <div class="row">
  <div class="col-md-3">
    <?php include 'filesside.php';?>
  </div>
    <div class="col-md-9">
      <div class="card">
        <h2><i class="fas fa-folder-open"></i> FILES</h2>
      </div>
      <div class="card">
        <h3>Upload a file</h3>
        <p>Just drop a file on the gigantuous crosshair, you cannot miss it!</p>
        <!-- DROP ZONE -->
        <div class="uploader" id="uploader">
          <i class="fas fa-crosshairs"></i>
        </div>

        <!-- STATUS -->
        <div class="upstat" id="upstat"></div>

      </div>
      <div class="card">
        <h3>Download a file</h3>
        <p>Overview of files comes here</p>
      </div>
    </div>
  </div>
</div>

<?php include 'footer.php';?>

</body>
</html>
