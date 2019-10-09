<div class="navbar">
<?php
if(!isset($_SESSION['username']) || empty($_SESSION['username'])){
  echo "<div class=\"navbar-index\">
    <a href=\"index.php\"><i class=\"fas fa-terminal\"></i> <b>LAUOMB</b></a>
  </div>
  <div class=\"navbar-login\">
    <a href=\"login.php\"><i class=\"fas fa-sign-in-alt\"></i> Login</a>
  </div>
  <a href=\"register.php\"><i class=\"far fa-plus-square\"></i> Register</a>";
}
else {
  echo "<div class=\"navbar-index\">
    <a href=\"welcome.php\"><i class=\"fas fa-terminal\"></i> <b>LAUOMB</b></a>
  </div>
  <div class=\"dropdown\">
    <button class=\"dropbtn\"><i class=\"far fa-list-alt\"></i> Databases <i class=\"fa fa-caret-down\"></i></button>
    <div class=\"dropdown-content\">";
  if(in_array("5", $_SESSION['authorizations'])){ echo "<a href=\"boerenbridge.php\"><i class=\"fas fa-chess-king\"></i> Boerenbridge</a>"; }
  if(in_array("6", $_SESSION['authorizations'])){ echo "<a href=\"crypto.php\"><i class=\"fab fa-btc\"></i> Crypto currencies</a>"; }
  if(in_array("6", $_SESSION['authorizations'])){ echo "<a href=\"finances.php\"><i class=\"far fa-credit-card\"></i> Personal finances</a>"; }
  if(in_array("6", $_SESSION['authorizations'])){ echo "<a href=\"mortgage.php\"><i class=\"fas fa-home\"></i> Mortgage</a>"; }
  if(in_array("13", $_SESSION['authorizations'])){ echo "<a href=\"shrdfinances.php\"><i class=\"far fa-money-bill-alt\"></i> Shared finances</a>"; }
  echo "</div>
  </div>
  <div class=\"dropdown\">
    <button class=\"dropbtn\"><i class=\"far fa-list-alt\"></i> Media <i class=\"fa fa-caret-down\"></i></button>
    <div class=\"dropdown-content\">";
  if(in_array("9", $_SESSION['authorizations'])){ echo "<a href=\"media.php\"><i class=\"far fa-images\"></i> Album downloads</a>"; }
  if(in_array("10", $_SESSION['authorizations'])){ echo "<a href=\"portugal.php\"><i class=\"fas fa-suitcase\"></i> Portugal 2018</a>"; }
  echo "</div>
  </div>
  <div class=\"navbar-right\">
    <div class=\"dropdown\">
      <button class=\"dropbtn\"><i class=\"far fa-address-card\"></i>   " . htmlspecialchars($_SESSION['username']) . " <i class=\"fa fa-caret-down\"></i></button>
      <div class=\"dropdown-content\">";
        if(in_array("3", $_SESSION['authorizations'])){ echo "<a href=\"memberprofile.php\"><i class=\"far fa-id-badge\"></i> Profile</a>"; }
        echo "<a href=\"logout.php\"><i class=\"fas fa-sign-out-alt\"></i> Logout</a>
      </div>
    </div>
  </div>";
  if(in_array("4", $_SESSION['authorizations'])){ echo "<div class=\"navbar-admin\"><a href=\"admin.php\"><i class=\"fas fa-user\"></i> Admin portal</a></div>"; }
}?>
</div>
