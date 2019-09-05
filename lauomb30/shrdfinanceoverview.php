<?php
// Initialize the session
session_start();

// If session variable is not set it will redirect to login page
if(!isset($_SESSION['username']) || empty($_SESSION['username']) || !in_array("13", $_SESSION['authorizations'])){
	header("location: login.php");
	exit;
}

// Include config file
require_once 'config.php';

// Define variables and initialize with empty values
$period_err = "";

// Pull expenses from db
if(isset($_POST['submit'])) {
	unset($_POST['submit']);

	$period = $_POST['period'];
	list($month,$year) = explode(".",$period); // Split POST variable in two separate variables on delimiter "."
} else{
	$month = date('m');
	$year = date('Y');
}

// Pull Expenses
$sql = "SELECT * FROM `vw_fin_expenses` WHERE (`type` = 1 AND month(`date`) = '$month' AND year(`date`) = '$year')";
if($stmt = $mysqli->query($sql)){
	while($row = mysqli_fetch_array($stmt)) {
		if($row['key'] == 1){
			$amount = "(".$row['amount'].")";
		} else{
			$amount = $row['amount'];
		}
		$values[] = "<tr><td>".$row['date']."</td><td>".$row['location']."</td><td>".$row['description']."</td><td>".$row['category']."</td><td>".$row['subcategory']."</td><td>".$amount."</td><td>".$row['name']."</td></tr>";
	}

	$expenses = implode("",$values);

	} else{
	echo "Couldn't fetch expenses. Please try again later.";
}

// Clear variables
unset($values);
$stmt->close();

// Pull chart data
$chart = array();

$sql = "SELECT `category`,`counterpart`,`key`,`amount` FROM `vw_fin_shared_monthly_overview` WHERE (`year` = '$year' AND `month` = '$month') ORDER BY `category`,`counterpart`,`key`";
if($stmt = $mysqli->query($sql)){
	while($row = mysqli_fetch_array($stmt)) {
		// Negate credits
		if($row['key'] == 1){
			$amount = $row['amount'] * -1;
		} else{
			$amount = $row['amount'];
		}
		// Split between counterparts
		$chart[$row['category']][$row['counterpart']] = $amount;
	}

	// Format to match Google Chart format;
	foreach ($chart as $key => $value) {
		if(array_key_exists(1,$value)){
			if(array_key_exists(2,$value)){
				// Laurens and Irina exist
				$values = $value[1] . "," . $value[2];
			} else{
				// Only Laurens exists
				$values = $value[1] . ",0";
			}
		} else{
			// Only Irina exists
			$values = "0," . $value[2];
		}
	  $chartdata = $chartdata . ",['" . $key . "', " . $values . "]";
	}
} else{
	echo "Couldn't fetch chart data. Please try again later.";
}

// Clear variables
$stmt->close();

// Pull months and years
$values = array();
$sql = "SELECT * FROM `vw_fin_periods`";
if($stmt = $mysqli->query($sql)){
	while($row = mysqli_fetch_array($stmt)) {
		$period = $row['month'].".".$row['year'];
		$monthname = date("F", strtotime($row['year'] . "-" . $row['month'] . "-01"));
		$values[] = "<option value=\"".$period."\">".$monthname." ".$row['year']."</option>";
	}

	$periods = implode("",$values);

	} else{
	echo "Couldn't fetch periods. Please try again later.";
}

// Clear variables
unset($values);
$stmt->close();

// Close connection
$mysqli->close();
?>

<!DOCTYPE html>
<html>
<head>
	<?php $title = "LauOmb Webserver - Finance reporting";
  include 'head.php'; ?>
	<script type="text/javascript">
	google.charts.load('current', {'packages':['corechart', 'bar']});
	google.charts.setOnLoadCallback(drawChart);

	function drawChart() {
		var data = google.visualization.arrayToDataTable([
          ['', 'Laurens', 'Irina']
			<?php echo $chartdata; ?>
		]);

		var options = {
			chart: {
				title: ''
			}
		};

		var chart = new google.charts.Bar(document.getElementById('columnchart'));

		chart.draw(data, google.charts.Bar.convertOptions(options));
	}
	</script>
</head>
<body>

<?php include 'header.php';?>

<div class="row">
<div class="col-25">
	<?php include 'shrdfinanceside.php';?>
	<?php include 'financeperiod.php';?>
	<?php include 'social.php';?>
</div>
  <div class="col-75">
		<div class="card">
      <h1><i class="far fa-money-bill-alt"></i> SHARED FINANCES  - <?php echo strtoupper(date('F, Y', strtotime($year . "-" . $month . "-01"))); ?></h1>
    </div>
		<div class="card">
      <h2>Expense breakdown</h2>
      <p><div id="columnchart" style="z-index: 1; width: 99%; height: 500px;"></div></p>
    </div>
    <div class="card">
      <a name="monthlyoverview"></a><h2>Expense overview</h2>
      <table>
      	<tr>
          <th>Date</th>
          <th>Location</th>
          <th>Description</th>
          <th>Category</th>
          <th>Subcategory</th>
          <th>Amount</th>
          <th>Counterpart</th>
        </tr>
      	<?php echo $expenses; ?>
      </table>
    </div>
  </div>
</div>

<?php include 'footer.php';?>

</body>
</html>
