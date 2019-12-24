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
			<h2>User details</h2>
			<?php echo $reset_err; ?>
			<div class="col-75">
				<?php echo $user_err; ?>
				<table style="max-width:75%">
					<tr>
						<th>Username</th>
						<th>Name</th>
						<th>Email</th>
						<th>Joined</th>
					</tr>
					<tr>
						<td><b><?php echo $username; ?></b></td>
						<td><?php echo $firstname . " " . $lastname; ?></td>
						<td><?php echo $email; ?></td>
						<td><?php echo $datecreated; ?></td>
					</tr>
				</table>
			</div>
		</div>
		<div class="card">
			<div class="col-75">
				<h2>Profile details</h2>
				<table style="max-width:50%">
					<tr>
						<th>Password age</th>
						<th>Type</th>
						<th>Reset</th>
					</tr>
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
				</table>
			</div>
		</div>
		<div class="card">
			<h2>Assigned roles</h2>
			<?php echo $activate_err; ?>
			<div class="col-75">
				<table style="max-width:25%">
					<tr>
						<th>Role</th>
					</tr>
					<?php echo $role_err . $roles; ?>
				</table>
			</div>
		</div>
  </div>
</div>

<?php include 'footer.php';?>

</body>
</html>
