<?php
// Initialize the session
session_start();

// If session variable is not set it will redirect to login page
if(!isset($_SESSION['username']) || empty($_SESSION['username'])){
  header("location: login.php");
  exit;
}

//Define variables and initialize with empty values
$type = $comments = "";
$type_err = $comments_err = "";

//Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){

  //Validate type
  if(empty(trim($_POST["type"]))){
    $type_err = "Please select a type from the list.";
  } else{
    $type = trim($_POST['type']);
  }

  //Validate comment
  if(empty(trim($_POST["comments"]))){
    $comments_err = "Please enter your comments.";
  } elseif(strlen(trim($_POST['comments'])) < 6){
    $comments_err = "Your comments do not appear to be valid.";
  } else{
    $comments = trim($_POST['comments']);
  }

  // Check input errors before storing in database
  if(empty($type_err) && empty($comments_err)){
    // Prepare an insert statement
    $sql = "INSERT INTO tbl_feedback (username, feedbackdate, feedbacktime, ipaddress, type, comments) VALUES (?, ?, ?, ?, ?, ?)";

    if($stmt = $mysqli->prepare($sql)){
        // Bind variables to the prepared statement as parameters
        $stmt->bind_param("ssssss", $param_username, $param_date, $param_time, $param_ipaddress, $param_type, $param_comments);

        // Set parameters
        $param_username = $_SESSION['username'];
        $param_date = date();
        $param_time = time();
        $param_ipaddress = $_SERVER['REMOTE_ADDR'];
        $param_type = $type;
        $param_comments = $comments;

        // Attempt to execute the prepared statement
        if($stmt->execute()){
            // Success
            echo "Success";
        } else{
            echo "Could not execute query. Please try again later";
        }
    }

    // Close statement
    $stmt->close();

    //Reset all variables to empty so the page shows empty again
    $type = $comments = "";
  } else{
    echo "Something went wrong. Please try again later.";
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
      <h2>HI <b><?php echo strtoupper(htmlspecialchars($_SESSION['username'])); ?></b>!</h2>
      <h5> Welcome to my site.</h5>
      <p>Some text here.</p>
    </div>
    <div class="card">
      <h2>CONTACT FORM</h2>
      <h5>Leave me a comment, or improvement</h5>
      <p>Please fill in all fields and click Submit. Much appreciated!</p>
      <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
      <table>
        <tr><td><label>Type</label></td><td><input list="type" name="type" value="<?php echo $type; ?>">
          <datalist id="type">
            <option value="Feedback">
            <option value="Improvement">
            <option value="Question">
            <option value="Other">
          </datalist><?php echo $type_err; ?></td></tr>
        <tr><td><label>Comments</label></td><td><textarea  name="comments" maxlength="1000" cols="25" rows="6"></textarea><?php echo $comments_err; ?></td></tr>
        <tr><td><input type="submit" value="Submit"></td></tr>
      </table>
      </form>
    </div>
  </div>
  <div class="rightcolumn">
    <?php include 'aboutyou.php';?>
    <?php include 'popular.php';?>
    <?php include 'social.php';?>
  </div>
</div>

<?php include 'footer.php';?>

</body>
</html>
