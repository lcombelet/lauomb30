<div class="header">
  <a href="index.php"><h1><i class="fas fa-code-branch"></i> LauOmb Webserver</h1></a>
  <p>A hobby gone full-nerd..</p>
</div>
<div class="navbar">
<?php
if(!isset($_SESSION['username']) || empty($_SESSION['username'])){
  echo "<a href=\"index.php\"><i class=\"fas fa-home\"></i> Home</a>
  <div class=\"navbar-right\">
    <a href=\"login.php\"><i class=\"fas fa-sign-in-alt\"></i> Login</a>
  </div>
  <a href=\"register.php\"><i class=\"far fa-plus-square\"></i> Register</a>";
}
else {
  echo "<a href=\"welcome.php\"><i class=\"fas fa-home\"></i> Home</a>
  <div class=\"dropdown\">
    <button class=\"dropbtn\"><i class=\"far fa-list-alt\"></i> Databases <i class=\"fa fa-caret-down\"></i></button>
    <div class=\"dropdown-content\">";
  if(in_array("5", $_SESSION['authorizations'])){ echo "<a href=\"boerenbridge.php\"><i class=\"fas fa-chess-king\"></i> Boerenbridge</a>"; }
  if(in_array("6", $_SESSION['authorizations'])){ echo "<a href=\"finances.php\"><i class=\"far fa-credit-card\"></i> Personal finances</a>"; }
  if(in_array("13", $_SESSION['authorizations'])){ echo "<a href=\"shrdfinances.php\"><i class=\"far fa-money-bill-alt\"></i> Shared finances</a>"; }
  echo "</div>
  </div>
  <a href=\"portugal.php\"><i class=\"fas fa-suitcase\"></i> Portugal diary</a>
  <a href=\"media.php\"><i class=\"far fa-images\"></i> Media</a>
  <div class=\"navbar-right\">
    <a href=\"logout.php\"><i class=\"fas fa-sign-out-alt\"></i> Logout</a>
  </div>";
  if(in_array("5", $_SESSION['authorizations'])){ echo "<div class=\"navbar-admin\"><a href=\"admin.php\"><i class=\"fas fa-user\"></i> Admin portal</a></div>"; }
}?>
</div>
