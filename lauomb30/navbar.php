<div class="jumbotron jumbotron-fluid text-center bg-white" style="margin-bottom:0">
  <div class="container">
    <h1 class="text-logreen font-weight-bold d-inline-block">LAUOMB </h1>&nbsp;<h1 class="text-lodark d-inline-block">WEBSERVER</h1>
    <p class="text-lodark">A hobby gone full nerd</p>
  </div>
</div>

<nav class="navbar navbar-expand-sm bg-lodark navbar-dark">
  <a class="navbar-brand" href="index.php"><i class="fas fa-terminal  "></i> HOME</a>
  <ul class="navbar-nav">
    <?php
    if(isset($_SESSION['username']) || !empty($_SESSION['username'])){
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
      </li>"; } ?>
  </ul>
  <ul class="navbar-nav ml-auto">
    <?php
    if(!isset($_SESSION['username']) || empty($_SESSION['username'])){
      ?>

      <!-- Button to Open the Modal -->
      <li class="nav-item"><a class="nav-link" href="#myModal" data-toggle="modal" data-target="#myModal">Login</a></li>

      <!-- The Modal -->
      <div class="modal fade" id="myModal">
        <div class="modal-dialog modal-sm">
          <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header">
              <h4 class="modal-title">LOGIN</h4>
              <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <!-- Modal body -->
            <div class="modal-body">
              <form action="login.php" method="post">
                <div class="input-container">
                  <i class="fas fa-user-circle icon"></i>
                  <input class="input-field" type="text" placeholder="Username" name="username" autocomplete="off" autofocus value="<?php echo $username; ?>"><?php echo $username_err; ?>
                </div>
                <div class="input-container">
                  <i class="fas fa-key icon"></i>
                  <input class="input-field" type="password" placeholder="Enter Password" name="password"><?php echo $password_err; ?>
                </div>
                <button class="formbtn" type="submit">Login</button>
              	<p>Don't have an account? <a href="register.php">Sign up now</a>.</p>
              </form>
            </div>
          </div>
        </div>
      </div>

        <?php echo "<li class=\"nav-item\"><a class=\"nav-link\" href=\"register.php\"><i class=\"far fa-plus-square\"></i> Register</a></li>" ;
    }
    else {
      if(in_array("4", $_SESSION['authorizations'])){ echo "<li class=\"nav-item\"><a class=\"nav-link text-danger\" href=\"admin.php\"><i class=\"fas fa-user\"></i> Admin portal</a></li>"; }

      if(in_array("3", $_SESSION['authorizations'])){ echo "<li class=\"nav-item\"><a class=\"nav-link\" href=\"memberprofile.php\"><i class=\"far fa-address-card\"></i> " . htmlspecialchars($_SESSION['username']) . "</a></li>"; }

      echo "<li class=\"nav-item\"><a class=\"nav-link\" href=\"logout.php\"><i class=\"fas fa-sign-out-alt\"></i> Logout</a></li>";
    }?>
  </ul>
</nav>

<script>
// Every time a modal is shown, if it has an autofocus element, focus on it.
	$('.modal').on('shown.bs.modal', function() {
	  $(this).find('[autofocus]').focus();
	});
</script>
