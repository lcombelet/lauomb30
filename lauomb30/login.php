<?php
// Include config file
require_once 'config.php';

// Define variables and initialize with empty values
$username = $password = "";
$username_err = $password_err = "";

// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){

    // Check if username is empty
    if(empty(trim($_POST["username"]))){
        $username_err = 'Please enter username.';
    } else{
        $username = trim($_POST["username"]);
    }

    // Check if password is empty
    if(empty(trim($_POST['password']))){
        $password_err = 'Please enter your password.';
    } else{
        $password = trim($_POST['password']);
    }

    // Validate credentials
    if(empty($username_err) && empty($password_err)){
        // Prepare a select statement
        $sql = "SELECT username, password, email, created_at, status FROM tbl_users WHERE username = ?";

        if($stmt = $mysqli->prepare($sql)){
            // Bind variables to the prepared statement as parameters
            $stmt->bind_param("s", $param_username);

            // Set parameters
            $param_username = $username;

            // Attempt to execute the prepared statement
            if($stmt->execute()){
                // Store result
                $stmt->store_result();

                // Check if username exists and email address is verified, then verify password
                if($stmt->num_rows == 1){
                    // Bind result variables
                    $stmt->bind_result($username, $hashed_password, $email, $created_at, $status);
                    if($stmt->fetch()){
						if($status == 1){
						/* Account is active, continue to validate the password */
							if(password_verify($password, $hashed_password)){
								/* Password is correct, so start a new session and
								save the username to the session */
								session_start();
								$_SESSION['username'] = $username;
								$_SESSION['useremail'] = $email;
								$_SESSION['usercreated_at'] = date("d-F Y", strtotime($created_at));
								header("location: welcome.php");
							} else{
								// Display an error message if password is not valid
								$password_err = 'The password you entered was not valid.';
							}
						} else{
							// Display an error message if email is not verified
							$username_err = 'Verify your email address by clicking  the link in your mailbox.';
						}
                    }
                } else{
                    // Display an error message if username doesn't exist
                    $username_err = 'No account found with that username.';
                }
            } else{
                echo "Oops! Something went wrong. Please try again later.";
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
  <?php include 'head.php';?>
</head>
<body>

<?php include 'header.php';?>

<div class="row">
  <div class="leftcolumn">
    <div class="card">
      <h2>LOGIN</h2>
        <p>Please fill in your credentials to login.</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
			<table>
			<tr><td><label>Username</label></td><td><input type="text" name="username" value="<?php echo $username; ?>"><?php echo $username_err; ?></td></tr>
			<tr><td><label>Password</label></td><td><input type="password" name="password"><?php echo $password_err; ?></td></tr>
			<tr><td><input type="submit" value="Login"></td></tr>
			</table>
			<p>Don't have an account? <a href="register.php">Sign up now</a>.</p>
        </form>
    </div>
  </div>
  <div class="rightcolumn">
    <?php include 'aboutme.php';?>
    <?php include 'popular.php';?>
    <?php include 'social.php';?>
  </div>
</div>

<?php include 'footer.php';?>

</body>
</html>
