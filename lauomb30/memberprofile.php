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

// Define variables and initialize with empty values
$reset_err = $current_password_err = $new_password_err = $confirm_password_err = "";

// Reset password
if(isset($_POST['change'])) {
	unset($_POST['change']);

	// Validate old password
	if(empty(trim($_POST['current_password']))){
		$current_password_err = "Please enter your password.";
	} else{
		$current_password = trim($_POST['current_password']);

		// Validate password
		$sql = "SELECT password FROM tbl_users WHERE username = ?";
		if($stmt = $mysqli->prepare($sql)){
			$stmt->bind_param("s", $param_username);

			// Bind parameters
			$param_username = $_SESSION['username'];

			if($stmt->execute()){;
				$stmt->store_result();
				$stmt->bind_result($hashed_password);
				if($stmt->fetch()){
					if(password_verify($current_password, $hashed_password)){
						// Password is correct, proceed to validate new password
						if(empty(trim($_POST['new_password']))){
							$new_password_err = "Please enter a new password.";
						} elseif(strlen(trim($_POST['new_password'])) < 6){
							$new_password_err = "Password must have at least 6 characters.";
						} else{
							$new_password = trim($_POST['new_password']);
							if($current_password == $new_password){
								$new_password_err = 'Password cannot be the same.';
							}
						}

						// Validate confirm password
						if(empty(trim($_POST["confirm_password"]))){
							$confirm_password_err = 'Please confirm password.';
						} else{
							$confirm_password = trim($_POST['confirm_password']);
							if($new_password != $confirm_password){
								$confirm_password_err = 'Password did not match.';
							}
						}

						// Update database
						if(empty($new_password_err) && empty($confirm_password_err)){

							// Update Users table
							$sql = "UPDATE `tbl_users` SET `password`=?, `password_date`=?, `password_reset`=? WHERE `username`=?";

							if($stmt = $mysqli->prepare($sql)){
								$stmt->bind_param("ssss", $param_password, $param_date, $param_reset, $param_username);

								// Bind parameters
								$param_password = password_hash($new_password, PASSWORD_DEFAULT); // Creates a password hash
								$param_date = date('Y-m-d H:i:s');
								$param_reset = 0;
								$param_username = $_SESSION['username'];

								$stmt->execute();
								$reset_err = "Password updated!";
							} else{
								$reset_err = "Error updating password!";
							}
						}
					} else{
						$current_password_err = 'Wrong password';
					}
				}
			} else{
					echo "Oops! Something went wrong. Please try again later.";
			}
		}
	}
	// Close connection
	$stmt->close();
}
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
      <h2>Reset password</h2>
      <p><?php echo $reset_err; ?></p>
			<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" style="max-width:300px">
				<div class="input-container">
          <i class="fas fa-key icon"></i>
          <input class="input-field" type="password" placeholder="Current password" name="current_password"><?php echo $current_password_err; ?>
        </div>
        <div class="input-container">
          <i class="fas fa-key icon"></i>
          <input class="input-field" type="password" placeholder="Enter new password" name="new_password"><?php echo $new_password_err; ?>
        </div>
				<div class="input-container">
          <i class="fas fa-key icon"></i>
          <input class="input-field" type="password" placeholder="Repeat new password" name="confirm_password"><?php echo $confirm_password_err; ?>
        </div>
        <button type="submit" name="change">Change password</button>
    </div>
		<div class="card">
			<h2>Delete profile</h2>
			<p><i class="far fa-sad-tear"></i> We need to talk...</p>
		</div>
  </div>
</div>

<?php include 'footer.php';?>

</body>
</html>
