<div class="header">
  <h1><i class="fas fa-database"></i> LauOmb webserver</h1>
  <p>A hobby gone full-nerd..</p>
</div>

<div class="topnav">
<a href="index.php">Home</a>
  <a href="news.php">News</a>
  <div class="dropdown">
    <button class="dropbtn">Dropdown
      <i class="fa fa-caret-down"></i>
    </button>
    <div class="dropdown-content">
      <a href="link1.php">Link 1</a>
      <a href="link2.php">Link 2</a>
      <a href="link3.php">Link 3</a>
    </div>
  </div>

<?php
if(!isset($_SESSION['username']) || empty($_SESSION['username'])){
  echo "<a href=\"login.php\" style=\"float:right\">Login</a>
  <a href=\"register.php\" style=\"float:right\">Register</a>";
}
else {
  echo "<a href=\"logout.php\" style=\"float:right\">Logout</a>";
}?>
</div>
