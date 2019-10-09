<?php
// Initialize the session
session_start();

// If session variable is not set it will redirect to login page
if(!isset($_SESSION['username']) || empty($_SESSION['username']) || !in_array("3", $_SESSION['authorizations'])){
	header("location: login.php");
	exit;
}

// Include config file
require_once 'config.php';
?>

<!DOCTYPE html>
<html>
<head>
	<?php $title = "LauOmb Webserver - Member Profile";
  include 'head.php'; ?>
</head>
<body>

<?php include 'header.php';?>

<div class="row">
<div class="col-25">
<?php include 'memberside.php';?>
<?php include 'aboutyou.php';?>
</div>
  <div class="col-75">
    <div class="card">
      <h1><i class="fas fa-user"></i> MEMBER PROFILE</h1>
    </div>
		<div class="card">
      <h2>Update profile</h2>
      <p>Something here</p>
    </div>
		<div class="card">
      <h2>Reset password</h2>
      <p>Something here</p>
    </div>
  </div>
</div>

<?php include 'footer.php';?>

</body>
</html>
