<div class="jumbotron jumbotron-fluid text-center bg-logreen" style="margin-bottom:0">
  <div class="container text-white">
    <h1 class="font-weight-bold">LAUOMB WEBSERVER</h1>
    <p>A hobby gone full nerd</p>
  </div>
</div>

<nav class="navbar navbar-expand-sm bg-lodark navbar-dark">
  <a class="navbar-brand" href="index.php"><i class="fas fa-terminal  "></i> HOME</a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#collapsibleNavbar">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse" id="collapsibleNavbar">
    <ul class="navbar-nav">
      <?php
      if(!isset($_SESSION['username']) || empty($_SESSION['username'])){
        echo "
          <li class=\"nav-item\"><a class=\"nav-link\" href=\"login.php\"><i class=\"fas fa-sign-in-alt\"></i> Login</a></li>
          <li class=\"nav-item\"><a class=\"nav-link\" href=\"register.php\"><i class=\"far fa-plus-square\"></i> Register</a></li>" ;
      }
      else {
        echo "
        <li class=\"nav-item dropdown\">
          <a class=\"nav-link dropdown-toggle\" data-toggle=\"dropdown\" href=\"#\"><i class=\"far fa-list-alt\"></i> Databases</a>
          <div class=\"dropdown-menu\">";
        if(in_array("5", $_SESSION['authorizations'])){ echo "<a class=\"dropdown-item\" href=\"boerenbridge.php\"><i class=\"fas fa-chess-king\"></i> Boerenbridge</a>"; }
        if(in_array("6", $_SESSION['authorizations'])){ echo "<a class=\"dropdown-item\" href=\"crypto.php\"><i class=\"fab fa-btc\"></i> Crypto currencies</a>"; }
        if(in_array("6", $_SESSION['authorizations'])){ echo "<a class=\"dropdown-item\" href=\"finances.php\"><i class=\"far fa-credit-card\"></i> Personal finances</a>"; }
        if(in_array("6", $_SESSION['authorizations'])){ echo "<a class=\"dropdown-item\" href=\"mortgage.php\"><i class=\"fas fa-home\"></i> Mortgage</a>"; }
        if(in_array("13", $_SESSION['authorizations'])){ echo "<a class=\"dropdown-item\" href=\"shrdfinances.php\"><i class=\"far fa-money-bill-alt\"></i> Shared finances</a>"; }
        echo "</div>
        </li>

        <li class=\"nav-item dropdown\">
          <a class=\"nav-link dropdown-toggle\" data-toggle=\"dropdown\" href=\"#\"><i class=\"far fa-list-alt\"></i> Media</a>
          <div class=\"dropdown-menu\">";
        if(in_array("9", $_SESSION['authorizations'])){ echo "<a class=\"dropdown-item\" href=\"media.php\"><i class=\"far fa-images\"></i> Album downloads</a>"; }
        if(in_array("10", $_SESSION['authorizations'])){ echo "<a class=\"dropdown-item\" href=\"portugal.php\"><i class=\"fas fa-suitcase\"></i> Portugal 2018</a>"; }
        echo "</div>
        </li>

        <li class=\"nav-item dropdown\">
          <a class=\"nav-link dropdown-toggle\" data-toggle=\"dropdown\" href=\"#\"><i class=\"far fa-address-card\"></i>   " . htmlspecialchars($_SESSION['username']) . " </a>
          <div class=\"dropdown-menu\">";
            if(in_array("3", $_SESSION['authorizations'])){ echo "<a class=\"dropdown-item\" href=\"memberprofile.php\"><i class=\"far fa-id-badge\"></i> Profile</a>"; }
            echo "<a class=\"dropdown-item\" href=\"logout.php\"><i class=\"fas fa-sign-out-alt\"></i> Logout</a>
          </div>
        </li>";

        if(in_array("4", $_SESSION['authorizations'])){ echo "<li class=\"nav-item\"><a class=\"nav-link text-danger\" href=\"admin.php\"><i class=\"fas fa-user\"></i> Admin portal</a></li>"; }
      }?>
    </ul>
  </div>
</nav>
