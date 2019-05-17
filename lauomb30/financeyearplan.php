<?php
// Initialize the session
session_start();

// If session variable is not set it will redirect to login page
if(!isset($_SESSION['username']) || empty($_SESSION['username'] || in_array("6", $_SESSION['authorizations']))){
	header("location: login.php");
	exit;
}

// Include config file
require_once 'config.php';

// Define variables and initialize with empty values
?>

<!DOCTYPE html>
<html>
<head>
	<?php $title = "LauOmb Webserver - Personal finances";
  include 'head.php'; ?>
</head>
<body>

<?php include 'header.php';?>

<div class="row">
<div class="col-25">
<?php include 'financeside.php';?>
</div>
  <div class="col-75">
    <div class="card">
      <h2>Year overview</h2>
      <h5>Useless bit of text here..</h5>
      <p>More to come here in the future.</p>
    </div>
  </div>
</div>

<?php include 'footer.php';?>

</body>
</html>
