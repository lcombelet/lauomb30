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
$reset_err = $user_err = $role_err = "";
$param_id = "";
$password = "Welcome2019";
$values = array();

// Reset password
if(isset($_POST['reset'])) {
	unset($_POST['reset']);

	// Update Users table
	$sql = "UPDATE `tbl_users` SET `password`=?, `password_date`=?, `password_reset`=? WHERE `user_id`=?";

	if($stmt = $mysqli->prepare($sql)){
		$stmt->bind_param("ssss", $param_password, $param_date, $param_reset, $param_id);

		// Bind parameters
		$param_id = $_POST['id'];
		$param_password = password_hash($password, PASSWORD_DEFAULT); // Creates a password hash
		$param_date = date('Y-m-d H:i:s');
		$param_reset = 1;

		$stmt->execute();
		$reset_err = "Success! New password: " . $password;
	} else{
		$reset_err = "Error updating password!";
	}
}

// Maintain user
if(isset($_POST['maintain'])) {
	unset($_POST['maintain']);

	$id = $_POST['id'];

//Fetch user data
$sql = "SELECT `username`, `firstname`, `lastname`, `email`, `created`, `passworddate`, `reset`, `editable`, `status` FROM `vw_user_users` WHERE `id` = ?";
if($stmt = $mysqli->prepare($sql)){
	$stmt->bind_param("s", $param_id);

	$param_id = $id;

	if($stmt->execute()){
		$stmt->store_result();

		if($stmt->num_rows == 1){
			$stmt->bind_result($username, $firstname, $lastname, $email, $created, $passworddate, $reset, $editable, $status);
			if($stmt->fetch()){
				$datecreated = date("d-M Y", strtotime($created));
				$datepassword = date("d-M Y", strtotime($passworddate));

				switch ($reset) {
					case 0:
						$passwordreset = "<i class=\"far fa-user\"></i>";
						break;
					case 1:
						$passwordreset = "<i class=\"fas fa-desktop\"></i>";
						break;
				}
			}
		} else{
			// Display an error message if username doesn't exist
			$user_err = "Couldn't fetch user data. Please try again later.";
		}
	} else{
		echo "Oops! Something went wrong. Please try again later.";
	}
}

// Fetch permissions
	$sql = "SELECT DISTINCT `role` FROM `vw_user_authorizations` WHERE `user_id` = $id";
	if($stmt = $mysqli->query($sql)){
		while($row = mysqli_fetch_array($stmt)) {
			$values[] = "<tr><td><b>".$row['role']."</b></td></tr>";
		}

		$roles = implode("",$values);
	} else{
		$role_err = "Couldn't fetch roles. Please try again later.";
	}

	// Clear variables and close connection
	unset($values);
	$stmt->close();
}
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
				<h3>User details</h3>
				<?php echo $reset_err; ?>
				<div class="col-md-9">
					<?php echo $user_err; ?>
					<table class="table table-sm table-striped table-hover">
						<thead class="bg-logreen text-white">
							<tr>
								<th>Username</th>
								<th>Name</th>
								<th>Email</th>
								<th>Joined</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td><b><?php echo $username; ?></b></td>
								<td><?php echo $firstname . " " . $lastname; ?></td>
								<td><?php echo $email; ?></td>
								<td><?php echo $datecreated; ?></td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
			<div class="card">
				<div class="col-md-3">
					<h3>Profile details</h3>
					<table class="table table-sm table-striped table-hover">
						<thead class="bg-logreen text-white">
							<tr>
								<th>Password age</th>
								<th>Type</th>
								<th>Reset</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td><?php echo $datepassword; ?></td>
								<td align="center"><?php echo $passwordreset; ?></td>
								<td>
									<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
									<input type="hidden" name="id" value="<?php echo $id; ?>">
									<button class="formreset" type="submit" name="reset"><i class="fas fa-redo-alt"></i></button>
					      	</form>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
			<div class="card">
				<h3>Assigned roles</h3>
				<?php echo $activate_err; ?>
				<div class="col-md-3">
					<table class="table table-sm table-striped table-hover">
						<thead class="bg-logreen text-white">
							<tr>
								<th>Role</th>
							</tr>
						</thead>
						<tboy>
							<?php echo $role_err . $roles; ?>
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
