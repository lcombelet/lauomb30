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

// Define variables and initialize with empty values
$permission_err = "";
$values = array();

// Fetch permissions
$sql = "SELECT * FROM `vw_user_roles`";
if($stmt = $mysqli->query($sql)){
	while($row = mysqli_fetch_array($stmt)) {
		$values[] = "<tr><td><b>".$row['role']."</b></td><td>".$row['permission']."</td></tr>";
	}

$permissions = implode("",$values);

	} else{
	$permission_err = "Couldn't fetch permissions. Please try again later.";
}

// Clear variables and close connection
unset($values);
$stmt->close();
?>

<!DOCTYPE html>
<html>
<head>
	<?php include 'head.php'; ?>
</head>
<body>

<?php include 'navbar.php';?>

<div class="container-fluid">
	<div class="row">
	<div class="col-md-3">
	<?php include 'adminside.php';?>
	<?php include 'serverdetails.php';?>
	</div>
	  <div class="col-md-9">
	    <div class="card">
	      <h2><i class="fas fa-user"></i> ADMIN PORTAL</h2>
	    </div>
			<div class="card">
				<h3>Permissions</h3>
				<div class="col-md-9">
					<table class="table table-sm table-striped table-hover" id="myTable">
						<thead class="bg-logreen text-white">
							<tr>
								<th>Role</th>
								<th>Permission</th>
							</tr>
						</thead>
						<tbody>
							<?php echo $permission_err . $permissions; ?>
						</tbody>
					</table>
				</div>
			</div>
	  </div>
	</div>
</div>

<?php include 'footer.php';?>

</body>
</html>
