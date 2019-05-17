<?php
// Initialize the session
session_start();

// If session variable is not set it will redirect to login page
if(!isset($_SESSION['username']) || empty($_SESSION['username'] || in_array("5", $_SESSION['authorizations']))){
  header("location: login.php");
  exit;
}
?>

<!DOCTYPE html>
<html>
<head>
  <?php $title = "LauOmb Webserver - Boerenbridge database";
  include 'head.php'; ?>
</head>
<body>

<?php include 'header.php';?>

<div class="row">
<div class="col-25">
  <?php include 'boerenbridgeside.php';?>
</div>
  <div class="col-75">
    <div class="card">
      <h2>Boerenbridge database</h2>
      <p>On this page I have built an application that you can use to record the scores for the game called 'Boerenbridge'.</p>
    </div>
    <?php if(isset($_POST['submitNames'])) { ?>
      <div class="card">
        <h2>xxx</h2>
        <p>xxx</p>
      </div>
    <?php } elseif(isset($_POST['submitPlayers']) || isset($_POST['addPlayer'])) {
      if(isset($_POST['submitPlayers'])) {
        $_SESSION['players'] = $_POST['players'];
      }
      if(isset($_POST['addPlayer'])) {
        //add player to db query
      } ?>
      <div class="card">
        <h2>Who is playing this game?</h2>
        <h5>Note: if the name of a player is not available, register that person first before selecting the players!</h5>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <table>
          <?php
            //pull playernames from db and store in datalist that can be pulled in html

            $x = 1;
            while($x <= $_SESSION['players']) {
          ?>
          <tr><td><label>Player <?php echo $x; ?>:</label></td><td><input list="players" name="player<?php echo $x; ?>">
            <datalist id="players">
              <option value="Coen">
              <option value="Els">
              <option value="Jeroen">
              <option value="Laurens">
              <option value="Rebecca">
            </datalist><?php echo $player_err; ?></td></tr>
            <?php
              $x++;
            }
            ?>
    			<tr><td><input type="submit" name="submitNames" value="Start the game"></td></tr>
        </table>
        </form>
      </div>
      <div class="card">
        <h2>Add a new player</h2>
        <h5>Input the first and last name and click the button to add the person.</h5>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <table>
          <tr><td><label>First name:</label></td><td><input type="text" name="firstname"><?php echo $firstname_err; ?></td></tr>
          <tr><td><label>Last name:</label></td><td><input type="text" name="lastname"><?php echo $lastname_err; ?></td></tr>
    			<tr><td><input type="submit" name="addPlayer" value="Add player"></td></tr>
        </table>
        </form>
      </div>
    <?php } else { ?>
      <div class="card">
        <h2>How many players are going to play?</h2>
        <h5>Note: the maximum number of players is eight.</h5>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <table>
          <tr><td><label>Players:</label></td><td><input type="range" id="players" name="players" min="2" max="8" step="1" onchange="updatePlayers()"></td><td id="functionResult"></td></tr>
          <tr><td><input type="submit" name="submitPlayers" value="Select players"></td></tr>
        </table>
      </div>
    <?php } ?>
  </div>
</div>

<?php include 'footer.php';?>

</body>
</html>
