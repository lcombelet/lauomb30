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
								/* Password is correct, start a new session and save the username to the session */
								session_start();

                // Pull authorizations
                $authorizations = array();

                $sql = "SELECT `perm_id` FROM `vw_user_authorizations` WHERE `username` = '$param_username' ORDER BY `perm_id`";
                if($stmt = $mysqli->query($sql)){
                	while($row = mysqli_fetch_array($stmt)) {
                		$authorizations[] = $row['perm_id']; // Multidimensional array required to pull results per counterpart
                	}
                	// Calculations
                	} else{
                	echo "Couldn't fetch authorizations. Please try again later.";
                }

								$_SESSION['username'] = $username;
								$_SESSION['useremail'] = $email;
								$_SESSION['usercreated_at'] = date("d-F Y", strtotime($created_at));
                $_SESSION['authorizations'] = $authorizations;
								header("location: welcome.php");
							} else{
								// Display an error message if password is not valid
								$password_err = 'The password you entered was not valid.';
							}
						} else{
							// Display an error message if email is not verified
							$username_err = 'Account not active, contact the administrator.';
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
  <?php $title= "LauOmb Webserver";
  include 'head.php'; ?>
</head>
<body>

<?php include 'header.php';?>

<div class="row">
  <div class="col-25">
    <?php include 'aboutme.php';?>
  </div>
  <div class="col-75">
    <div class="card">
      <h2>LOGIN</h2>
      <p>Please fill in your credentials to login.</p>
      <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" style="max-width:300px">
        <div class="input-container">
          <i class="fas fa-user-circle icon"></i>
          <input class="input-field" type="text" placeholder="Username" name="username" autocomplete="off" autofocus value="<?php echo $username; ?>"><?php echo $username_err; ?>
        </div>
        <div class="input-container">
          <i class="fas fa-key icon"></i>
          <input class="input-field" type="password" placeholder="Enter Password" name="password"><?php echo $password_err; ?>
        </div>
        <button type="submit">Login</button>
      	<p>Don't have an account? <a href="register.php">Sign up now</a>.</p>
      </form>
    </div>
  </div>

</div>

<?php include 'footer.php';?>

</body>
</html>
