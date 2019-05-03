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
    <div class="leftcolumn">
			<div class="card">
					<h2>Reporting</h2>
					<h5>Useless bit of text here..</h5>
					<p>bla bla bla.</p>
			</div>
    </div>
<div class="rightcolumn">
    <?php include 'financeside.php';?>
    <?php include 'social.php';?>
</div>
</div>

<?php include 'footer.php';?>

</body>
</html>
