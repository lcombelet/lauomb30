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
  <?php $title = "LauOmb Webserver - Boerenbridge database";
  include 'head.php'; ?>
</head>
<body>

<?php include 'header.php';?>

<div class="row">
  <div class="leftcolumn">
    <div class="card">
      <h2>Boerenbridge database</h2>
      <h5>On this page I have built an application that you can use to record the scores for the game called 'Boerenbridge'.</h5>
    </div>
    <?php if(isset($_POST['submitNames'])) { ?>
      <div class="card">
        <h2>xxx</h2>
        <p>xxx</p>
        </form>
      </div>
    <?php } elseif(isset($_POST['submitPlayers'])) { ?>
      <div class="card">
        <h2>Who is playing this game?</h2>
        <p>Note: the maximum number of players is eight.</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <table>
          <tr><td><label>Player 1</label></td><td><input list="players" name="player1" value="select">
            <datalist id="players">
              <option value="Coen">
              <option value="Els">
              <option value="Jeroen">
              <option value="Laurens">
              <option value="Rebecca">
            </datalist><?php echo $type_err; ?></td></tr>
    			<tr><td><input type="submitNames" value="Start the game"></td></tr>
        </table>
        </form>
      </div>
    <?php } else { ?>
      <div class="card">
        <h2>How many players are going to play?</h2>
        <p>Note: the maximum number of players is eight.</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <table>
          <tr><td><label>Players</label></td><td><input type="range" name="players" min="2" max="8" step="1" onchange="updatePlayers()"></td><p id="functionResult"></p></tr>
    			<tr><td><input type="submitPlayers" value="Select players"></td></tr>
        </table>
      </div>
    <?php } ?>
  </div>
  <div class="rightcolumn">
    <?php include 'boerenbridgeside.php';?>
    <?php include 'social.php';?>
  </div>
</div>

<?php include 'footer.php';?>

</body>
</html>
