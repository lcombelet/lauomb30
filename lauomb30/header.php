<div class="header">
  <h1><i class="fas fa-code-branch"></i> LauOmb webserver</h1>
  <p>A hobby gone full-nerd..</p>
</div>

<div class="topnav">
  <a href="index.php"><i class="fas fa-home"></i> Home</a>
  <a href="news.php"><i class="far fa-newspaper"></i> News</a>
  <div class="dropdown">
    <button class="dropbtn"><i class="far fa-list-alt"></i> Dropdown <i class="fa fa-caret-down"></i></button>
    <div class="dropdown-content">
      <a href="link1.php">Link 1</a>
      <a href="link2.php">Link 2</a>
      <a href="link3.php">Link 3</a>
    </div>
  </div>

<?php
if(!isset($_SESSION['username']) || empty($_SESSION['username'])){
  echo "<a href=\"login.php\" style=\"float:right\"><i class=\"fas fa-sign-in-alt\"></i> Login</a>
  <a href=\"register.php\" style=\"float:right\"><i class=\"far fa-plus-square\"></i> Register</a>";
}
else {
  echo "<a href=\"logout.php\" style=\"float:right\"><i class=\"fas fa-sign-out-alt\"></i> Logout</a>";
}?>
</div>
