<?php
// Initialize the session
session_start();

// If session variable is not set it will redirect to login page
if(!isset($_SESSION['username']) || empty($_SESSION['username']) || !in_array("5", $_SESSION['authorizations'])){
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
      <h1><i class="fas fa-chess-king"></i> BOERENBRIDGE</h1>
    </div>
    <div class="card">
      <h2>This is a work in progress</h2>
      <h5>Don't try anything stupid, the world will end if you do...</h5>
    </div>
    <?php if(isset($_POST['endGame'])) {
      //update score previous round
      //summarize score and show analytics
      //clean up db
      ?>
      <div class="card">
        <h2>Step 5. All finished!</h2>
        <p>xxx</p>
      </div>

    <?php } elseif(isset($_POST['continueGame'])) {
      //update score previous round
      //load current round details (dealer, etc)
      ?>
      <div class="card">
        <h2>Step 4. Continue playing</h2>
        <p>xxx</p>
      </div>

    <?php } elseif(isset($_POST['startGame'])) {
      //create all rounds and assign dealer to each round
      //create score arrays for each player
      ?>
      <div class="card">
        <h2>Step 3. Let's begin!</h2>
        <p>xxx</p>
      </div>

    <?php } elseif(isset($_POST['submitPlayers'])) {
      $_SESSION['players'] = $_POST['players'];
      $_SESSION['cards'] = $_POST['cards'];
      //create new game, load gameid from db and store in session variable

      echo $_SESSION['players'];
      echo $_SESSION['cards']; ?>

      <div class="card">
        <h2>Step 2. Select your players and first dealer</h2>
        <h5>Start with the person who drew the highest card, continue clockwise.</h5>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" style="max-width:300px">
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
    			<tr><td><input type="submit" name="startGame" value="Start the game"></td></tr>
        </table>
        </form>
      </div>

    <?php } else { ?>
      <div class="card">
        <h2>Step 1. Select the number of players and cards</h2>
        <h5>The maximum number of players is eight, the maximum number of cards is automatically adjusted.</h5>
        <div class="row">
        	<div class="col-50">
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
              <div class="input-container">
                <i class="fas fa-users icon"></i>
                <div class="slidecontainer">
                  <input type="range" min="2" max="8" value="2" step="1" class="slider" id="playerRange" name="players">
                </div>
              </div>
              <div class="input-container">
                Value:&nbsp;<span id="playerValue"></span>
              </div>
            </div>
            <div class="col-50">
              <div class="input-container">
                <i class="far fa-hand-pointer icon"></i>
                <div class="slidecontainer">
                  <input type="range" min="4" max="25" value="4" step="1" class="slider" id="cardRange" name="cards">
                </div>
              </div>
              <div class="input-container">
                Value:&nbsp;<span id="cardValue"></span>
              </div>
            </div>
          </div>
          <button type="submit" name="submitPlayers" value="submit">Select players</button>
        </form>

        <script>
          var playerSlider = document.getElementById("playerRange");
          var playerSliderOutput = document.getElementById("playerValue");
          var cardSlider = document.getElementById("cardRange");
          var cardSliderOutput = document.getElementById("cardValue");

          playerSliderOutput.innerHTML = playerSlider.value;
          cardSliderOutput.innerHTML = cardSlider.value;

          playerSlider.oninput = function() {
            playerSliderOutput.innerHTML = this.value;

            var x = document.getElementById("cardRange").max = Math.floor(51 / this.value);
          }

          cardSlider.oninput = function() {
            cardSliderOutput.innerHTML = this.value;
          }
        </script>

      </div>
    <?php } ?>
  </div>
</div>

<?php include 'footer.php';?>

</body>
</html>
