<?php
// Initialize the session
session_start();

// If session variable is not set it will redirect to login page
if(!isset($_SESSION['username']) || empty($_SESSION['username'])){
  header("location: login.php");
  exit;
}
?>

<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" type="text/css" href="/assets/stylesheet.css">
<link rel="stylesheet" type="text/css" href="http://fonts.googleapis.com/css?family=Karla">
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.1.1/css/all.css" integrity="sha384-O8whS3fhG2OnA5Kas0Y9l3cfpmYjapjI0E4theH4iuMD+pLhbf6JI0jIMfYcK3yZ" crossorigin="anonymous">
</head>
<body>

<?php include 'header.php';?>

<div class="row">
  <div class="leftcolumn">
    <div class="card">
      <h2>HI <b><?php echo strtoupper(htmlspecialchars($_SESSION['username'])); ?></b>!</h2>
      <h5> Welcome to my site.</h5>
      <div class="fakeimg" style="height:200px;">Image</div>
      <p>Some text..</p>
      <p>Sunt in culpa qui officia deserunt mollit anim id est laborum consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco.</p>
    </div>
    <div class="card">
      <h2>TITLE HEADING</h2>
      <h5>Title description, Sep 2, 2017</h5>
      <div class="fakeimg" style="height:200px;">Image</div>
      <p>Some text..</p>
      <p>Sunt in culpa qui officia deserunt mollit anim id est laborum consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco.</p>
    </div>
  </div>
  <div class="rightcolumn">
    <div class="card">
      <h2>ABOUT YOU</h2>
	  <table>
	  <tr><td>Username:</td><td><?php echo htmlspecialchars($_SESSION['username']); ?></td></tr>
	  <tr><td>Registered to:</td><td><?php echo htmlspecialchars($_SESSION['useremail']); ?></td></tr>
	  <tr><td>Member since:</td><td><?php echo htmlspecialchars($_SESSION['usercreated_at']); ?></td></tr>
	  </table>
	  <p><a href="logout.php">Sign Out</a></p>
    </div>
    <div class="card">
      <h3>POPULAIR POSTS</h3>
      <div class="fakeimg"><p>Image</p></div>
      <div class="fakeimg"><p>Image</p></div>
      <div class="fakeimg"><p>Image</p></div>
    </div>
    <div class="card">
      <h3>FOLLOW ME</h3>
      <p>Some text..</p>
    </div>
  </div>
</div>

<?php include 'footer.php';?>

</body>
</html>
