<?php
// Initialize the session
session_start();

// If session variable is not set it will redirect to login page
if(!isset($_SESSION['username']) || empty($_SESSION['username']) || !in_array("4", $_SESSION['authorizations'])){
	header("location: login.php");
	exit;
}

// Include config file
require_once 'config.php';
?>

<!DOCTYPE html>
<html>
<head>
	<?php $title = "LauOmb Webserver - Admin portal";
  include 'head.php'; ?>
</head>
<body>

<?php include 'header.php';?>

<div class="row">
<div class="col-25">
<?php include 'adminside.php';?>
<?php include 'serverdetails.php';?>
</div>
  <div class="col-75">
    <div class="card">
      <h1><i class="fas fa-user"></i> ADMIN PORTAL</h1>
    </div>
		<div class="card">
      <h2>Stuff to work on</h2>
      <ul>
				<li>User management.</li>
				<li>RBAC.</li>
			</ul>
			<?php print_r($_SESSION['authorizations']); ?>
    </div>
  </div>
</div>

<?php include 'footer.php';?>

</body>
</html>
