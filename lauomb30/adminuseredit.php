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
$password = "Welcome2019";
$values = array();

// Reset password
if(isset($_POST['reset'])) {
	unset($_POST['reset']);

	$param_id = $_POST['id'];
	$param_password = password_hash($password, PASSWORD_DEFAULT); // Creates a password hash

	// Update Users table
	$sql = "UPDATE `tbl_users` SET `password`=? WHERE `user_id`=?";

	if($stmt = $mysqli->prepare($sql)){
		$stmt->bind_param("ss", $param_password, $param_id);
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
$sql = "SELECT * FROM `vw_user_users` WHERE `id` = $id";
if($stmt = $mysqli->query($sql)){
	while($row = mysqli_fetch_array($stmt)) {

		$datecreated = date("d-M Y", strtotime($row['created']));
		$reset = "<form action=\"" . htmlspecialchars($_SERVER["PHP_SELF"]). "\" method=\"post\">
										<input type=\"hidden\" name=\"id\" value=\"".$row['id']."\">
										<button class=\"formreset\" type=\"submit\" name=\"reset\"><i class=\"fas fa-key\"></i></button>
					      	</form>";

		$values[] = "<tr><td><b>".$row['username']."</b></td><td>".$row['firstname']." ".$row['lastname']."</td><td>".$row['email']."</td><td>".$datecreated."</td><td>".$reset."</td></tr>";
	}

	$user = implode("",$values);
	unset($values);
	} else{
	$user_err = "Couldn't fetch user data. Please try again later.";
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
			<h2>User data</h2>
			<?php echo $reset_err; ?>
			<div class="col-75">
				<table style="max-width:75%">
					<tr>
						<th>Username</th>
						<th>Name</th>
						<th>Email</th>
						<th>Joined</th>
						<th>Reset</th>
					</tr>
					<?php echo $user_err . $user; ?>
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
		<div class="card">
			<h2>Role Based Access Control</h2>
			<table>
				<tr>
					<th>Something</th>
					<th>Something</th>
				</tr>
				<tr>
					<td>Something</td>
					<td>Something</td>
				</tr>
			</table>
		</div>

  </div>
</div>

<?php include 'footer.php';?>

</body>
</html>
