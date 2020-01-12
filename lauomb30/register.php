<?php
// Include config file
require_once 'config.php';

// Define variables and initialize with empty values
$username = $password = $confirm_password = $email = "";
$username_err = $password_err = $confirm_password_err = $email_err = "";

// Processing form data when form is submitted
if(isset($_POST['submit'])) {
	unset($_POST['submit']);

    // Validate username
    if(empty(trim($_POST["username"]))){
        $username_err = "Please enter a username.";
    } else{
        // Prepare a select statement
        $sql = "SELECT user_id FROM tbl_users WHERE username = ?";

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
        $sql = "INSERT INTO `tbl_users` (`username`, `firstname`, `lastname`, `password`, `password_date`, `password_reset`, `email`, `activation_code`, `status`, `editable`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        if($stmt = $mysqli->prepare($sql)){
            // Bind variables to the prepared statement as parameters
            $stmt->bind_param("ssssssssss", $param_username, $param_firstname, $param_lastname, $param_password, $param_date, $param_reset, $param_email, $param_activation_code, $param_status, $param_edit);

            // Set parameters
            $param_username = $username;
						$param_firstname = trim($_POST['firstname']);
						$param_lastname = trim($_POST['lastname']);
            $param_password = password_hash($password, PASSWORD_DEFAULT); // Creates a password hash
						$param_date = date('Y-m-d H:i:s');;
						$param_reset = 0;
      			$param_email = $email;
      			$param_activation_code = $activation_code;
      			$param_status = 0;
						$param_edit = 1;

            // Attempt to execute the prepared statement
            if($stmt->execute()){
                // Redirect to login page
                header("location: index.php");
            } else{
                echo "Something went wrong. Please try again later.";
            }
        }

        // Close statement
        $stmt->close();
    }

    // Close connection
    $mysqli->close();
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
	  <?php include 'aboutme.php';?>
	</div>
	  <div class="col-md-9">
	    <div class="card">
	      <h2>SIGN UP</h2>
	    	<p>Please fill this form to create an account.</p>

	      <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" style="max-width:50%">
	        <div class="row">
	          <div class="col-md-6">
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
	          <div class="col-md-6">
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
	        <button class="formbtn" type= "submit" name="submit">Submit form</button>
	        </div>
	      </form>
	      <p>Already have an account? <a href="login.php">Login here</a>.</p>
	    </div>
	  </div>
	</div>
</div>

<?php include 'footer.php';?>

</body>
</html>
