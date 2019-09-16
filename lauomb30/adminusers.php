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
$username = $firstname = $lastname = $password = $confirm_password = $email = "";
$username_err = $firstname_err = $lastname_err = $password_err = $confirm_password_err = $email_err = $activate_err = $create_err = "";
$values = array();

// Activate or deactivate user
if(isset($_POST['activate'])) {
	unset($_POST['activate']);

	$id = $_POST['id'];
	$status = $_POST['status'];

	// Update Users table
	$sql = "UPDATE `tbl_users` SET `status`=? WHERE `user_id`=?";

	if($stmt = $mysqli->prepare($sql)){
		$stmt->bind_param("ss", $status, $id);
		$stmt->execute();

		$activate_err = "<h5>Update complete!</h5>";
	}

	// Close statement
	$stmt->close();
}

// Create new account
if(isset($_POST['create'])) {
	unset($_POST['create']);

    // Validate username
    if(empty(trim($_POST["username"]))){
        $username_err = "Please enter a username.";
    } else{
        // Prepare a select statement
        $sql = "SELECT `user_id` FROM `tbl_users` WHERE `username` = ?";

        if($stmt = $mysqli->prepare($sql)){
            // Bind variables to the prepared statement as parameters
            $stmt->bind_param("s", $param_username);

            // Set parameters
            $param_username = trim($_POST["username"]);

            // Attempt to execute the prepared statement
            if($stmt->execute()){
                // store result
                $stmt->store_result();

                if($stmt->num_rows == 1){
                    $username_err = "This username is already taken.";
                } else{
                    $username = trim($_POST["username"]);
                }
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }
        }

        // Close statement
        $stmt->close();
    }

    // Validate password
    if(empty(trim($_POST['password']))){
        $password_err = "Please enter a password.";
    } elseif(strlen(trim($_POST['password'])) < 6){
        $password_err = "Password must have at least 6 characters.";
    } else{
        $password = trim($_POST['password']);
    }

    // Validate confirm password
    if(empty(trim($_POST["confirm_password"]))){
        $confirm_password_err = 'Please confirm password.';
    } else{
        $confirm_password = trim($_POST['confirm_password']);
        if($password != $confirm_password){
            $confirm_password_err = 'Password did not match.';
        }
    }

    // Validate email and generate validation code
    if(empty(trim($_POST['email']))){
        $email_err = "Please enter your email address.";
    } elseif(filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL)) {
		$email = trim($_POST['email']);
		$activation_code = md5($email.time());
	} else {
		$email_err = "Not a valid email address.";
    }

    // Check input errors before inserting in database
    if(empty($username_err) && empty($password_err) && empty($confirm_password_err) && empty($email_err)){

        // Prepare an insert statement
        $sql = "INSERT INTO `tbl_users` (`username`, `firstname`, `lastname`, `password`, `email`, `activation_code`, `status`, `editable`) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

        if($stmt = $mysqli->prepare($sql)){
            // Bind variables to the prepared statement as parameters
            $stmt->bind_param("ssssssss", $param_username, $param_firstname, $param_lastname, $param_password, $param_email, $param_activation_code, $param_status, $param_edit);

            // Set parameters
            $param_username = $username;
						$param_firstname = trim($_POST['firstname']);
						$param_lastname = trim($_POST['lastname']);
            $param_password = password_hash($password, PASSWORD_DEFAULT); // Creates a password hash
      			$param_email = $email;
      			$param_activation_code = $activation_code;
      			$param_status = 0;
						$param_edit = 1;

            // Attempt to execute the prepared statement
            if($stmt->execute()){
                $create_err = "<h5>User created!</h5>";
            } else{
                echo "Something went wrong. Please try again later.";
            }
        }

        // Close statement
        $stmt->close();
    }
}

// Pull users
$sql = "SELECT * FROM `vw_user_users`";
if($stmt = $mysqli->query($sql)){
	while($row = mysqli_fetch_array($stmt)) {

		switch ($row['status']) {
	   case 0:
       $status = "Inactive";
			 $action = 1;
			 $label = "Activate";
       break;
	   case 1:
       $status = "Active";
			 $action = 0;
			 $label = "Deactivate";
       break;
		}

		$datecreated = date("d-M Y", strtotime($row['created']));
		$updateform = "<form action=\"" . htmlspecialchars($_SERVER["PHP_SELF"]). "\" method=\"post\">
										<input type=\"hidden\" name=\"id\" value=\"".$row['id']."\">
										<input type=\"hidden\" name=\"status\" value=\"".$action."\">
										<button class=\"formupdate\" type=\"submit\" name=\"activate\">".$label."</button>
					      	</form>";
		$maintainaccount = "<form action=\"adminrbac.php\" method=\"post\">
										<input type=\"hidden\" name=\"id\" value=\"".$row['id']."\">
										<button class=\"formreset\" type=\"submit\" name=\"maintain\">Manage</button>
					      	</form>";

		$values[] = "<tr><td><b>".$row['username']."</b></td><td>".$row['firstname']." ".$row['lastname']."</td><td>".$row['email']."</td><td>".$datecreated."</td><td>".$status."</td><td>".$updateform."</td><td>".$maintainaccount."</td></tr>";
	}

$users = implode("",$values);

	} else{
	echo "Couldn't fetch users. Please try again later.";
}

// Clear variables
unset($values);
$stmt->close();
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
			<h2>User overview</h2>
			<?php echo $activate_err; ?>
			<div class="col-75">
				<table style="max-width:75%">
					<tr>
						<th>Username</th>
						<th>Name</th>
						<th>Email</th>
						<th>Joined</th>
						<th>Status</th>
						<th>Change status</th>
						<th>Maintenance</th>
					</tr>
					<?php echo $users; ?>
				</table>
			</div>
		</div>
		<div class="card">
			<h2>Create user</h2>
			<?php echo $create_err; ?>
				<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" style="max-width:50%">
					<div class="row">
						<div class="col-50">
							<div class="input-container">
								<i class="fas fa-user-circle icon"></i>
								<input class="input-field" type="text" placeholder="Username" name="username" autofocus autocomplete="off" value="<?php echo $username; ?>"><?php echo $username_err; ?>
							</div>
							<div class="input-container">
								<i class="far fa-user icon"></i>
								<input class="input-field" type="text" placeholder="First name" name="firstname" autocomplete="off" value="<?php echo $firstname; ?>"><?php echo $firstname_err; ?>
							</div>
							<div class="input-container">
								<i class="fas fa-user icon"></i>
								<input class="input-field" type="text" placeholder="Last name" name="lastname" autocomplete="off" value="<?php echo $lastname; ?>"><?php echo $lastname_err; ?>
							</div>
						</div>
						<div class="col-50">
							<div class="input-container">
								<i class="fas fa-at icon"></i>
								<input class="input-field" type="text" placeholder="Email" name="email" autocomplete="off" value="<?php echo $email; ?>"><?php echo $email_err; ?>
							</div>
							<div class="input-container">
								<i class="fas fa-key icon"></i>
								<input class="input-field" type="password" placeholder="Choose password" name="password" autocomplete="off" value="<?php echo $password; ?>"><?php echo $password_err; ?>
							</div>
							<div class="input-container">
								<i class="fas fa-key icon"></i>
								<input class="input-field" type="password" placeholder="Confirm password" name="confirm_password" autocomplete="off" value="<?php echo $confirm_password; ?>"><?php echo $confirm_password_err; ?>
							</div>
						</div>
					<button type= "submit" name="create">Create user</button>
				</div>
			</form>
    </div>
  </div>
</div>

<?php include 'footer.php';?>

</body>
</html>
