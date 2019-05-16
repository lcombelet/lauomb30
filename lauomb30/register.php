<?php
// Include config file
require_once 'config.php';

// Define variables and initialize with empty values
$username = $password = $confirm_password = $email = "";
$username_err = $password_err = $confirm_password_err = $email_err = "";

// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){

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
        $sql = "INSERT INTO tbl_users (username, password, email, activation_code, status) VALUES (?, ?, ?, ?, ?)";

        if($stmt = $mysqli->prepare($sql)){
            // Bind variables to the prepared statement as parameters
            $stmt->bind_param("sssss", $param_username, $param_password, $param_email, $param_activation_code, $param_status);

            // Set parameters
            $param_username = $username;
            $param_password = password_hash($password, PASSWORD_DEFAULT); // Creates a password hash
      			$param_email = $email;
      			$param_activation_code = $activation_code;
      			$param_status = 0;

            // Attempt to execute the prepared statement
            if($stmt->execute()){
                // Redirect to login page
                header("location: login.php");
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
  <?php $title= "LauOmb Webserver";
  include 'head.php'; ?>
</head>
<body>

<?php include 'header.php';?>

<div class="row">
<div class="leftcolumn">
  <?php include 'aboutme.php';?>
  <?php include 'social.php';?>
</div>
  <div class="rightcolumn">
    <div class="card">
      <h2>SIGN UP</h2>
	<p>Please fill this form to create an account.</p>
	<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
		<table>
		<tr><td><label>Username</label></td><td><input type="text" name="username" value="<?php echo $username; ?>"><?php echo $username_err; ?></td></tr>
		<tr><td><label>Email</label></td><td><input type="text" name="email" value="<?php echo $email; ?>"><?php echo $email_err; ?></td></tr>
		<tr><td><label>Password</label></td><td><input type="password" name="password" value="<?php echo $password; ?>"><?php echo $password_err; ?></td></tr>
		<tr><td><label>Confirm Password</label></td><td><input type="password" name="confirm_password" value="<?php echo $confirm_password; ?>"><?php echo $confirm_password_err; ?></td></tr>
		<tr><td><input type="submit" value="Submit"><input type="reset" value="Reset"></td></tr>
		</table>
	<p>Already have an account? <a href="login.php">Login here</a>.</p>
	</form>
    </div>
  </div>
</div>

<?php include 'footer.php';?>

</body>
</html>
