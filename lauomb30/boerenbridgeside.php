<div class="card">
  <h3><i class="far fa-chart-bar"></i> GAME STATISTICS</h3>
  <?php if(isset($_POST['endGame'])) {
    //stuff here
    echo "<h4>Game finished!</h4>";
    //loop over player array
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

      //calculate score
      if($actual == $planned){
        $score = 10 + $actual;
      } else{
        $score = 0;
      }

      //update player score array
      array_push($_SESSION['playerscore'.$value], $score);

      //update total score
      $_SESSION['[playertotal'.$value] = array_sum($_SESSION['playerscore'.$value]);

      //unset variables
      unset($planned, $actual);

      // Clear variables
      $stmt->close();
    }

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

    //close game in db
    $sql = "UPDATE `tbl_bridge_games` SET `completed`=? WHERE `game_id`=?";

    if($stmt = $mysqli->prepare($sql)){
      $stmt->bind_param("ss", $param_completed, $param_game_id);

      // Bind parameters
      $param_completed = 1;
      $param_game_id = $_SESSION['game_id'];

      $stmt->execute();
      $game_err = "Game " . $_SESSION['game_id'] . " finished!";
    } else{
      $game_err = "Error closing the game!";
    }

  } elseif(isset($_POST['continueGame'])) {
    //fix header
    echo "<h4>Score tracker</h4>";
    echo "<table>";
    echo "<tr><th>Player</th><th>Score</th></tr>";

    //loop over player array
    foreach($_SESSION['players'] as $key=>$value) {
      //pull POST data
      $planned = $_POST['p_'.$value];
      $actual = $_POST['a_'.$value];

      //pull player name
      $sql = "SELECT * FROM `vw_bridge_players` WHERE `id` = $value";
      if($stmt = $mysqli->query($sql)){
        while($row = mysqli_fetch_array($stmt)) {
          $playername = $row['firstname']." ".$row['lastname'];
        }
      } else{
        echo "Couldn't fetch player name. Please try again later.";
      }

      //update db with scores
      $sql = "INSERT INTO `tbl_bridge_scores` (`round_id`, `player_id`, `planned`, `actual`) VALUES (?, ?, ?, ?)";

      if($stmt = $mysqli->prepare($sql)){
        $stmt->bind_param("ssss", $_SESSION['round'], $value, $planned, $actual);
        $stmt->execute();
      } else{
        echo "Couldn't update score.";
      }

      //calculate score
      if($actual == $planned){
        $score = 10 + $actual;
      } else{
        $score = 0;
      }

      //update player score array
      array_push($_SESSION['playerscore'.$value], $score);
      //print_r($_SESSION['playerscore'.$value]);

      //update total score
      $_SESSION['[playertotal'.$value] = array_sum($_SESSION['playerscore'.$value]);
      echo "<tr><td>";
      echo $playername;
      echo "</td><td>";
      echo $_SESSION['[playertotal'.$value];
      echo "</td></tr>";

      //unset variables
      unset($playername, $planned, $actual);

      // Clear variables
      $stmt->close();
    }

    //close table
    echo "</table>";

    //close round in db
    $sql = "UPDATE `tbl_bridge_rounds` SET `completed`=? WHERE (`game_id`=? AND `round`=?)";

    if($stmt = $mysqli->prepare($sql)){
      $stmt->bind_param("sss", $param_completed, $param_game_id, $param_round_id);

      // Bind parameters
      $param_completed = 1;
      $param_game_id = $_SESSION['game_id'];
      $param_round_id = $_SESSION['round'];

      $stmt->execute();
      $round_err = "Round " . $_SESSION['round'] . " updated! ";
    } else{
      $round_err = "Error updating round " . $_SESSION['round'] . "! ";
    }

    //load new round details (dealer, etc)
    $_SESSION['round']++;

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
  } else{ ?>
    <h4>Game has not started yet, scores will show up here.</h4>
  <?php } ?>
</div>
