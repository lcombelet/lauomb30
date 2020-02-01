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
$username = $firstname = $lastname = $password = $confirm_password = $email = $editable = $ghost = "";
$username_err = $firstname_err = $lastname_err = $password_err = $confirm_password_err = $email_err = $activate_err = $create_err = $editable_err = "";
$password = "Welcome2019";
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
	} else{
			$activate_err = "Something went wrong. Please try again later.";
	}

	// Close statement
	$stmt->close();
}

// Block account
if(isset($_POST['block'])) {
	unset($_POST['block']);

	$id = $_POST['id'];

	// Update Blocked table
	$sql = "INSERT INTO `tbl_users_unknown` SELECT * FROM `tbl_users` WHERE `user_id`=?";

	if($stmt = $mysqli->prepare($sql)){
		$stmt->bind_param("s", $id);
		$stmt->execute();
	} else{
			$activate_err = "Could not add user to blacklist.";
	}

// Remove from user table when block is success
	if(empty($active_err)){
		$sql = "DELETE FROM `tbl_users` WHERE `user_id`=?";

		if($stmt = $mysqli->prepare($sql)){
			$stmt->bind_param("s", $id);
			$stmt->execute();

			$active_err = "User blocked.";
		} else{
				$activate_err = "Could not delete user.";
		}
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
    if(empty($username_err) && empty($email_err)){

        // Prepare an insert statement
        $sql = "INSERT INTO `tbl_users` (`username`, `firstname`, `lastname`, `password`, `password_date`, `password_reset`, `email`, `activation_code`, `status`, `editable`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        if($stmt = $mysqli->prepare($sql)){
            // Bind variables to the prepared statement as parameters
            $stmt->bind_param("ssssssssss", $param_username, $param_firstname, $param_lastname, $param_password, $param_date, $param_reset, $param_email, $param_activation_code, $param_status, $param_edit);

            // Set parameters
            $param_username = $username;
						$param_firstname = trim($_POST['firstname']);
						$param_lastname = trim($_POST['lastname']);
            $param_password = password_hash($password, PASSWORD_DEFAULT); // Creates a password hash
						$param_date = date('Y-m-d H:i:s');
						$param_reset = 1;
      			$param_email = $email;
      			$param_activation_code = $activation_code;
      			$param_status = 0;
						$param_edit = $_POST['editable'];

            // Attempt to execute the prepared statement
            if($stmt->execute()){
                $create_err = "Success! New password: " . $password;
            } else{
                $create_err = "Something went wrong. Please try again later.";
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

		$ghost = "";

		if($row['editable'] == 0){
			$status = "<i class=\"fas fa-user-secret\"></i>";
			$maintainaccount = "";
			$blockaccount = "";
	 	} else{
				switch ($row['status']) {
					case 0:
						$action = 1;
						$label = "<i class=\"fas fa-toggle-off\"></i>";
						break;
					case 1:
						$action = 0;
						$label = "<i class=\"fas fa-toggle-on\"></i>";
						break;
				}

				$status = "<form action=\"" . htmlspecialchars($_SERVER["PHP_SELF"]). "\" method=\"post\">
					<input type=\"hidden\" name=\"id\" value=\"".$row['id']."\">
					<input type=\"hidden\" name=\"status\" value=\"".$action."\">
					<button class=\"formupdate\" type=\"submit\" name=\"activate\">".$label."</button>
					</form>";

				$maintainaccount = "<form action=\"adminuseredit.php\" method=\"post\">
					<input type=\"hidden\" name=\"id\" value=\"".$row['id']."\">
					<button class=\"formupdate\" type=\"submit\" name=\"maintain\"><i class=\"far fa-edit\"></i></button>
					</form>";

					$blockaccount = "<form action=\"" . htmlspecialchars($_SERVER["PHP_SELF"]). "\" method=\"post\">
						<input type=\"hidden\" name=\"id\" value=\"".$row['id']."\">
						<button class=\"formupdate\" type=\"submit\" name=\"block\"><i class=\"fas fa-ban\"></i></button>
						</form>";
	 		}

		$datecreated = date("d-M Y", strtotime($row['created']));

		$values[] = "<tr><td><b>".$row['username']."</b></td><td>".$row['firstname']." ".$row['lastname']."</td><td>".$row['email']."</td><td>".$datecreated."</td><td align=\"center\">".$status."</td><td align=\"center\">".$maintainaccount."</td><td align=\"center\">".$blockaccount."</td></tr>";
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
				<h3>User overview</h3>
				<?php echo $activate_err; ?>
				<div class="col-md-9">
					<table class="table table-sm table-striped table-hover" id="myTable">
						<thead class="bg-logreen text-white">
							<tr>
								<th>Username</th>
								<th>Name</th>
								<th>Email</th>
								<th>Joined</th>
								<th>Status</th>
								<th>Edit</th>
								<th>Ban</th>
							</tr>
						</thead>
						<tbody>
							<?php echo $users; ?>
						</tbody>
					</table>
				</div>
			</div>
			<div class="card">
				<h3>Create user</h3>
				<?php echo $create_err; ?>
				<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" style="max-width:50%">
					<div class="row">
						<div class="col-sm-6 col-lg-4">
							<div class="input-container">
								<i class="far fa-user icon"></i>
								<input class="input-field" type="text" placeholder="First name" name="firstname" autofocus autocomplete="off" value="<?php echo $firstname; ?>"><?php echo $firstname_err; ?>
							</div>
							<div class="input-container">
								<i class="fas fa-user icon"></i>
								<input class="input-field" type="text" placeholder="Last name" name="lastname" autocomplete="off" value="<?php echo $lastname; ?>"><?php echo $lastname_err; ?>
							</div>
							<div class="input-container">
								<i class="fas fa-at icon"></i>
								<input class="input-field" type="text" placeholder="Email" name="email" autocomplete="off" value="<?php echo $email; ?>"><?php echo $email_err; ?>
							</div>
						</div>
						<div class="col-sm-6 col-lg-4">
							<div class="input-container">
								<i class="fas fa-user-circle icon"></i>
								<input class="input-field" type="text" placeholder="Username" name="username" autocomplete="off" value="<?php echo $username; ?>"><?php echo $username_err; ?>
							</div>
							<div class="input-container">
								<i class="fas fa-balance-scale icon"></i>
								<select class="input-field" name="editable">
									<option value="0">Hidden account</option>
									<option value="1" selected>Normal account</option>
								</select><?php echo $editable_err; ?>
							</div>
							<button class="formbtn" type= "submit" name="create">Create user</button>
						</div>
					</div>
				</form>
	    </div>
	  </div>
	</div>
</div>

<?php include 'footer.php';?>

</body>
</html>
