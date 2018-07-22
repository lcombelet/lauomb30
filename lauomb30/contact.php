<?php
// Include config file
require_once 'config.php';

//Define variables and initialize with empty values
$first_name = $last_name = $email_from = $type = $comments = "";
$first_name_err = $last_name_err = $email_from_err = $type_err = $comments_err = "";

//Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){

  //Validate firstname
  if(empty(trim($_POST["first_name"]))){
    $first_name_err = "Please enter your first name.";
  } else{
    $first_name = trim($_POST['first_name']);
  }

  //Validate lastname
  if(empty(trim($_POST["last_name"]))){
    $last_name_err = "Please enter your last name.";
  } else{
    $last_name = trim($_POST['last_name']);
  }

  //Validate email
  if(empty(trim($_POST['email_from']))){
    $email_from_err = "Please enter your email address.";
  } elseif(filter_var(trim($_POST['email_from']), FILTER_VALIDATE_EMAIL)) {
    $email_from = trim($_POST['email_from']);
  } else {
    $email_from_err = "Not a valid email address.";
  }

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
  if(empty($first_name_err) && empty($last_name_err) && empty($email_from_err) && empty($type_err) && empty($comments_err)){
    //create sql tables and query, add date, time, ipaddress, etc. fields to sql table for sorting and filtering
    //then run everything into sql, provide some sort of trigger to admin user to notify of comments left (use right hand side of the screen after login)
    //upon successful entry to db, uncomment next line to thank visitor for feedback

    //echo "Thank you for leaving a comment!";

    //Reset all variables to empty so the page shows empty again
    $first_name = $last_name = $email_from = $type = $comments = "";
  } else{
    echo "Something went wrong. Please try again later.";
  }
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
      <h2>CONTACT FORM</h2>
      <h5>Leave me a comment, or improvement</h5>
      <p>Please fill in all fields and click Submit. Much appreciated!</p>
      <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
      <table>
        <tr><td><label>First name</label></td><td><input type="text" name="first_name" value="<?php echo $first_name; ?>"><?php echo $first_name_err; ?></td></tr>
        <tr><td><label>Last name</label></td><td><input type="text" name="last_name" value="<?php echo $last_name; ?>"><?php echo $last_name_err; ?></td></tr>
        <tr><td><label>Email</label></td><td><input type="text" name="email_from" value="<?php echo $email_from; ?>"><?php echo $email_from_err; ?></td></tr>
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
    <?php include 'aboutme.php';?>
    <?php include 'social.php';?>
  </div>
</div>

<?php include 'footer.php';?>

</body>
</html>
