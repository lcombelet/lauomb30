<div class="headercontainer">
  <div class="header">
    <h1><i class="fas fa-code-branch"></i> LauOmb webserver</h1>
    <p>A hobby gone full-nerd..</p>
  </div>

  <div class="topnav">
    <a href="index.php"><i class="fas fa-home"></i> Home</a>

  <?php
  if(!isset($_SESSION['username']) || empty($_SESSION['username'])){
    echo "<a href=\"login.php\" style=\"float:right\"><i class=\"fas fa-sign-in-alt\"></i> Login</a>
    <a href=\"register.php\" style=\"float:right\"><i class=\"far fa-plus-square\"></i> Register</a>";
  }
  else {
    echo "<div class=\"dropdown\">
      <button class=\"dropbtn\"><i class=\"far fa-list-alt\"></i> Databases <i class=\"fa fa-caret-down\"></i></button>
      <div class=\"dropdown-content\">";
    if(in_array("5", $_SESSION['authorizations'])){ echo "<a href=\"boerenbridge.php\"><i class=\"fas fa-chess-king\"></i> Boerenbridge</a>"; }
    if(in_array("6", $_SESSION['authorizations'])){ echo "<a href=\"finances.php\"><i class=\"far fa-credit-card\"></i> Personal finances</a>"; }
    if(in_array("13", $_SESSION['authorizations'])){ echo "<a href=\"shrdfinances.php\"><i class=\"far fa-money-bill-alt\"></i> Shared finances</a>"; }
    echo "</div>
    </div>
    <a href=\"portugal.php\"><i class=\"fas fa-suitcase\"></i> Portugal diary</a>
    <a href=\"media.php\"><i class=\"far fa-images\"></i> Media</a>
    <a href=\"logout.php\" style=\"float:right\"><i class=\"fas fa-sign-out-alt\"></i> Logout</a>
    <a href=\"admin.php\" style=\"float:right\"><i class=\"far fa-user\"></i> Admin stuff</a>";
  }?>
  </div>
</div>
