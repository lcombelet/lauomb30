<?php
// Initialize the session
session_start();

// If session variable is not set it will redirect to login page
if(!isset($_SESSION['username']) || empty($_SESSION['username']) || !in_array("5", $_SESSION['authorizations'])){
  header("location: login.php");
  exit;
}

// Include config file
require_once 'config.php';

// Define variables and initialize with empty values
$location_err = $gamedate_err = $dealer_err = $card_err = $playername_err = $round_err = $game_err = "";
$values = array();
$players = array();
$chartLegendArray = array();

if(isset($_POST['endGame'])) {
  //calculate cumulative sum for each player
  foreach($_SESSION['players'] as $key=>$value) {
    //pull POST data
    $planned = $_POST['p_'.$value];
    $actual = $_POST['a_'.$value];

    //update db with scores
    $sql = "INSERT INTO `tbl_bridge_scores` (`round_id`, `player_id`, `planned`, `actual`) VALUES (?, ?, ?, ?)";

    if($stmt = $mysqli->prepare($sql)){
      $stmt->bind_param("ssss", $_SESSION['round'], $value, $planned, $actual);
      $stmt->execute();
    } else{
      echo "Couldn't update score.";
    }

    // Clear variables
    $stmt->close();

    //calculate score
    if($actual == $planned){
      $score = 10 + $actual;
    } else{
      $score = 0;
    }

    //update player score array
    array_push($_SESSION['playerscore'.$value], $score);

    //unset variables
    unset($planned, $actual, $score);

    //calculate cumulative score array for chart
    ${'cumulative'.$value} = array();
    $runningSum = 0;

    foreach($_SESSION['playerscore'.$value] as $number) {
      $runningSum += $number;
      ${'cumulative'.$value}[] = $runningSum;
    }

    //create player name array for chart
    $sql = "SELECT * FROM `vw_bridge_players` WHERE `id` = $value";
    if($stmt = $mysqli->query($sql)){
      while($row = mysqli_fetch_array($stmt)) {
        $playername = $row['firstname']." ".$row['lastname'];
        $chartLegendArray[] = "'" . $playername . "'";
      }
    }

    //update final score for side table
    ${'playerfinal'.$value} = array_sum($_SESSION['playerscore'.$value]);
    ${'playerfinal'.$value} = "<tr><td>" . $playername . "</td><td>" . ${'playerfinal'.$value} . "</td></tr>";

    unset($playername);

    // Clear variables
    $stmt->close();
  }

  $chartLegendData = implode(",", $chartLegendArray);
  $chartLegendData = "['Round'," . $chartLegendData . "]";

  //create array containing all rounds played
  $chartDataArray = array();
  $scoreArray = array();
  $x = 1;

  while($x <= $_SESSION['roundcount']) {
    $y = $x - 1;

    foreach($_SESSION['players'] as $key=>$value) {
      $scoreArray[] = ${'cumulative'.$value}[$y];
    }

    $scoreData = implode(",", $scoreArray);
    unset($scoreArray);

    $chartDataArray[] = "['" . $x . "'," . $scoreData . "]";
    $x++;
  }

  //implode array to format data table for google chart
  $chartData = implode(",", $chartDataArray);
  $chartData = $chartLegendData . "," . $chartData;

  //close round in db
  $sql = "UPDATE `tbl_bridge_rounds` SET `completed`=? WHERE (`game_id`=? AND `round`=?)";

  if($stmt = $mysqli->prepare($sql)){
    $stmt->bind_param("sss", $param_completed, $param_game_id, $param_round_id);

    // Bind parameters
    $param_completed = 1;
    $param_game_id = $_SESSION['game_id'];
    $param_round_id = $_SESSION['round'];

    $stmt->execute();
    $round_err = "Round " . $_SESSION['round'] . " updated!";
  } else{
    $round_err = "Error updating round " . $_SESSION['round'] . "! ";
  }

  // Clear variables
  $stmt->close();

  //close game in db
  $sql = "UPDATE `tbl_bridge_games` SET `completed`=? WHERE `id`=?";

  if($stmt = $mysqli->prepare($sql)){
    $stmt->bind_param("ss", $param_completed, $param_game_id);

    // Bind parameters
    $param_completed = 1;
    $param_game_id = $_SESSION['game_id'];

    $stmt->execute();
    $game_err = "Game " . $_SESSION['game_id'] . " finished! ";
  } else{
    $game_err = "Error closing the game! ";
  }

  // Clear variables
  $stmt->close();

  //clean up db
}
?>

<!DOCTYPE html>
<html>
<head>
  <?php include 'head.php'; ?>

  <script type="text/javascript">
    google.charts.load('current', {'packages':['corechart']});
    google.charts.setOnLoadCallback(drawChart);

    function drawChart() {
      var data = google.visualization.arrayToDataTable([
        <?php echo $chartData; ?>
      ]);

      var options = {
        hAxis: {
          title: 'Round'
        },
      vAxis: {
          title: 'Score'
        },
        curveType: 'function',
        pointSize: 6,
        legend: { position: 'top' }
      };

      var chart = new google.visualization.LineChart(document.getElementById('curve_chart'));

      chart.draw(data, options);
    }
  </script>
</head>
<body>

<?php include 'navbar.php';?>

<div class="container-fluid">
  <div class="row">
  <div class="col-md-3">
    <?php include 'boerenbridgescore.php';?>
    <?php include 'boerenbridgeside.php';?>
  </div>
    <div class="col-md-9">
      <div class="card">
        <h2><i class="fas fa-chess-king"></i> BOERENBRIDGE</h2>
      </div>

      <?php if(isset($_POST['endGame'])) {
        unset($_POST['endGame']);
        ?>

        <div class="card">
          <h3>Step 5. All finished!</h3>
          <p><?php echo $game_err . $round_err; ?></p>
    			<div id="curve_chart" style="z-index: 1; width: 99%; height: 500px; display: inline-block;"></div>
        </div>

        <?php
        // Close connection
        $mysqli->close();

      } elseif(isset($_POST['continueGame'])) {
        unset($_POST['continueGame']);
        ?>

        <div class="card">
          <h3>Step 4. Continue playing</h3>
          <p><?php echo $round_err; ?>Round <?php echo $_SESSION['round']; ?>, <?php echo $dealer_err . $card_err; ?></p>
          <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post"  style="max-width:800px">
            <?php
            //loop over player array
            foreach($_SESSION['players'] as $key=>$value) {

              //pull name from db
              $sql = "SELECT * FROM `vw_bridge_players` WHERE `id` = $value";
              if($stmt = $mysqli->query($sql)){
              	while($row = mysqli_fetch_array($stmt)) {
              		$playername = $row['firstname']." ".$row['lastname'];
              	}
            	} else{
              	echo "Couldn't fetch player name. Please try again later.";
              }

              // Clear variables
              $stmt->close();
              ?>

              <div class="row">
                <h4><?php echo $playername; ?></h4>
              </div>
              <div class="row">
              	<div class="col-sm-6 col-lg-4">
                  <div class="input-container">
                    <i class="far fa-lightbulb icon"></i>
                    <input class="input-field" type="number" name="p_<?php echo $value; ?>" min="0" step="1">
                  </div>
                </div>
                <div class="col-sm-6 col-lg-4">
                  <div class="input-container">
                    <i class="far fa-check-square icon"></i>
                    <input class="input-field" type="number" name="a_<?php echo $value; ?>" min="0" step="1">
                  </div>
                </div>
              </div>
              <?php
            }

            if($_SESSION['round'] < $_SESSION['roundcount']){
              echo "<button class=\"formbtn\" type=\"submit\" name=\"continueGame\" value=\"submit\">Continue</button>";
            } else{
              echo "<button class=\"formbtn\" type=\"submit\" name=\"endGame\" value=\"submit\">Finish game</button>";
            } ?>
          </form>
        </div>

        <?php
        // Close connection
        $mysqli->close();

      } elseif(isset($_POST['startGame'])) {
        unset($_POST['startGame']);

        //create array of all players and score array per player
        $x = 1;
        while($x <= $_SESSION['playercount']) {
          $players[$x] = $_POST['player'.$x];
          $_SESSION['playerscore'.$_POST['player'.$x]] = array();
          $x++;
        }

        //store in session variable
        $_SESSION['players'] = $players;

        //create all rounds and assign dealer to each round
        $_SESSION['roundcount'] = 2 * $_SESSION['cardcount'];
        $x = 1;

        while($x <= $_SESSION['roundcount']) {
          //amount of cards for each round
          if($x <= $_SESSION['cardcount']){
            $cards = $x;
          } else{
            $cards = $_SESSION['cardcount'] - ($x - $_SESSION['cardcount'] - 1);
          }

          //dealer per round
          if($x <= $_SESSION['playercount']){
            $dealernumber = $x;
          } else{
            //calculate the module of x for playercount
            $dealernumber = $x % $_SESSION['playercount'];
          }

          //correct for modulo 0 calculation
          if($dealernumber == 0){
            $dealernumber = $_SESSION['playercount'];
          }

          $dealer = $_SESSION['players'][$dealernumber];
          $completed = 0;

          //update db
          $sql = "INSERT INTO `tbl_bridge_rounds` (`game_id`, `round`, `cards`, `dealer`, `completed`) VALUES (?, ?, ?, ?, ?)";

          if($stmt = $mysqli->prepare($sql)){
            $stmt->bind_param("sssss", $_SESSION['game_id'], $x, $cards, $dealer, $completed);
            $stmt->execute();
          }

          $x++;
        }

        //pull data for first round from db
        $_SESSION['round'] = 1;

        $sql = "SELECT * FROM `tbl_bridge_rounds` WHERE (`game_id` = '{$_SESSION['game_id']}' AND `round` = '{$_SESSION['round']}')";
        if($stmt = $mysqli->query($sql)){
        	while($row = mysqli_fetch_array($stmt)) {
        		$dealer = $row['dealer'];
            $card_err = $row['cards'] . " cards.";

            //pull dealer name from db
            $sql = "SELECT * FROM `vw_bridge_players` WHERE `id` = $dealer";
            if($stmt = $mysqli->query($sql)){
              while($row = mysqli_fetch_array($stmt)) {
                $dealer_err = $row['firstname'] . " is dealing ";
              }
            } else{
              $dealer_err = "Couldn't fetch dealer name.";
            }

          }
      	} else{
        	echo "Couldn't fetch data. Please try again later.";
        }

        // Clear variables
        $stmt->close();
        ?>

        <div class="card">
          <h3>Step 3. Let's begin!</h3>
          <p>Round <?php echo $_SESSION['round']; ?>, <?php echo $dealer_err . $card_err; ?></p>
          <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post"  style="max-width:800px">
            <?php
            //loop over player array
            foreach($_SESSION['players'] as $key=>$value) {

              //pull name from db
              $sql = "SELECT * FROM `vw_bridge_players` WHERE `id` = $value";
              if($stmt = $mysqli->query($sql)){
              	while($row = mysqli_fetch_array($stmt)) {
              		$playername = $row['firstname']." ".$row['lastname'];
              	}
            	} else{
              	echo "Couldn't fetch player name. Please try again later.";
              }

              // Clear variables
              $stmt->close();
              ?>

              <div class="row">
                <h4><?php echo $playername; ?></h4>
              </div>
              <div class="row">
              	<div class="col-sm-6 col-lg-4">
                  <div class="input-container">
                    <i class="far fa-lightbulb icon"></i>
                    <input class="input-field" type="number" name="p_<?php echo $value; ?>" min="0" step="1">
                  </div>
                </div>
                <div class="col-sm-6 col-lg-4">
                  <div class="input-container">
                    <i class="far fa-check-square icon"></i>
                    <input class="input-field" type="number" name="a_<?php echo $value; ?>" min="0" step="1">
                  </div>
                </div>
              </div>
              <?php }

            // Close connection
            $mysqli->close();
            ?>

            <button class="formbtn" type="submit" name="continueGame" value="submit">Continue</button>

          </form>
        </div>

      <?php } elseif(isset($_POST['submitPlayers'])) {
        unset($_POST['submitPlayers']);

        $_SESSION['gamedate'] = $_POST['gamedate'];
        $location = $_POST['location'];
        $_SESSION['playercount'] = $_POST['playercount'];
        $_SESSION['cardcount'] = $_POST['cardcount'];
        $completed = 0;

        //create new game, load gameid from db and store in session variable
        $sql = "INSERT INTO `tbl_bridge_games` (`date`, `location`, `players`, `cards`, `completed`) VALUES (?, ?, ?, ?, ?)";

        if($stmt = $mysqli->prepare($sql)){
          $stmt->bind_param("sssss", $_SESSION['gamedate'], $location, $_SESSION['playercount'], $_SESSION['cardcount'], $completed);
          $stmt->execute();
        }

        // Pull game_id from game table and store in session variable
        $sql = "SELECT * FROM `vw_bridge_most_recent_game`";

        if($stmt = $mysqli->query($sql)){
          while($row = mysqli_fetch_array($stmt)) {
            $_SESSION['game_id'] = $row['id'];
          }
        }

        // Pull playernames from db
        $sql = "SELECT * FROM `vw_bridge_players`";
        if($stmt = $mysqli->query($sql)){
        	while($row = mysqli_fetch_array($stmt)) {
        		$values[] = "<option value=\"".$row['id']."\">".$row['firstname']." ".$row['lastname']."</option>";
        	}

        	$playernames = implode("",$values);

        	} else{
        	echo "Couldn't fetch playernames. Please try again later.";
        }

        // Clear variables
        unset($values);
        $stmt->close();

        // Close connection
        $mysqli->close();
        ?>

        <div class="card">
          <h3>Step 2. Select your players</h3>
          <p>Start with the person who drew the highest card, continue clockwise.</p>
          <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" style="max-width:300px">
            <?php
              //loop over players
              $x = 1;
              while($x <= $_SESSION['playercount']) {
            ?>
            <div class="input-container">
              <i class="fas fa-user-tag icon"></i>
              <select class="input-field" name="player<?php echo $x; ?>"><option disabled selected value>Select player <?php echo $x; ?></option><?php echo $playernames; ?></select><?php echo $playername_err; ?>
            </div>
            <?php
              $x++;
            }
            ?>

            <button class="formbtn" type="submit" name="startGame" value="submit">Start the game</button>
          </form>
        </div>

      <?php } else { ?>
        <div class="card">
          <h3>Step 1. Select the number of players and cards</h3>
          <p>The maximum number of players is eight, the maximum number of cards is automatically adjusted.</p>
          <div class="row">
          	<div class="col-sm-6 col-lg-4">
              <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
              <div class="input-container">
  							<i class="far fa-calendar-alt icon"></i>
  							<input class="input-field" type="date" name="gamedate" autofocus><?php echo $gamedate_err; ?>
  						</div>
              <div class="input-container">
                <i class="fas fa-location-arrow icon"></i>
                <input class="input-field" type="text" name="location" placeholder="Location" autocomplete="off" maxlength="45" size="50"><?php echo $location_err; ?>
              </div>
              <button class="formbtn" type="submit" name="submitPlayers" value="submit">Continue to next step</button>
            </div>
            <div class="col-sm-6 col-lg-4">
              <div class="input-container">
                <i class="fas fa-users icon"></i>
                <div class="slidecontainer">
                  <input type="range" min="2" max="8" value="2" step="1" class="slider" id="playerRange" name="playercount">
                </div>
                Players:&nbsp;<span id="playerValue"></span>
              </div>
              <div class="input-container">
                <i class="far fa-hand-pointer icon"></i>
                <div class="slidecontainer">
                  <input type="range" min="4" max="25" value="4" step="1" class="slider" id="cardRange" name="cardcount">
                </div>
                Cards:&nbsp;<span id="cardValue"></span>
              </div>
            </div>
          </div>
          </form>

          <!-- Reference to js file containing scripts around updating sliders for players and cards -->
          <script type="text/javascript" src="js/bridge_body.js"></script>

        </div>
      <?php } ?>
    </div>
  </div>
</div>

<?php include 'footer.php';?>

</body>
</html>
