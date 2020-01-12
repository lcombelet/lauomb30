<?php
// Initialize the session
session_start();

// If session variable is not set it will redirect to login page
if(!isset($_SESSION['username']) || empty($_SESSION['username']) || !in_array("6", $_SESSION['authorizations'])){
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

// Pull expenses
$sql = "SELECT * FROM `vw_fin_expenses` WHERE (`counterpart` = 1 AND month(`date`) = '$month' AND year(`date`) = '$year')";
if($stmt = $mysqli->query($sql)){
	while($row = mysqli_fetch_array($stmt)) {
		// Build table
		$date = date("d-M Y", strtotime($row['date']));

		if($row['key'] == 1){
			$amount = "(".$row['amount'].")";
		} else{
			$amount = $row['amount'];
		}

		$values[] = "<tr><td>".$date."</td><td>".$row['location']."</td><td>".$row['description']."</td><td>".$row['category']."</td><td>".$row['subcategory']."</td><td>".$amount."</td><td>".ucfirst($row['type_descr'])."</td></tr>";
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

$sql = "SELECT `category`,`counterpart`,`key`,`type`,`amount` FROM `vw_fin_personal_monthly_overview` WHERE (`counterpart`= 1 AND `year` = '$year' AND `month` = '$month') ORDER BY `category`,`amount` DESC,`counterpart`,`key`,`type`";
if($stmt = $mysqli->query($sql)){
	while($row = mysqli_fetch_array($stmt)) {

		// Split expenses into different tables
		switch ($row['type']) {
			case 2: // business
				$busichart[$row['category']][$row['key']] = $row['amount'];
				break;
			default: // personal, shared, savings
				$perschart[$row['category']][$row['key']] = $row['amount'];
		}
	}

	// Personal expenses;
	$personalcredittotal = $personaldebittotal = 0; // Initialize empty columns to draw chart

	foreach ($perschart as $key => $value) {
		$personallabel = $personallabel . ",'" . $key . "'";
		if(array_key_exists(1,$value)){
			// Credit exists
			$personalcredit = $personalcredit . "," . $value[1];
			$personalcredittotal = $personalcredittotal + $value[1];
		}
		else{
			$personalcredit = $personalcredit . "," . '0';
		}
		if(array_key_exists(2,$value)){
			// Debit exist
			$personaldebit = $personaldebit . "," . $value[2];
			$personaldebittotal = $personaldebittotal + $value[2];
		}
		else{
			$personaldebit = $personaldebit . "," . '0';
		}
	}

$personaldata = "['Category'" . $personallabel . "],['Expenses'" . $personaldebit . "],['Earnings'" . $personalcredit . "]";

// Business expenses;
$busicredittotal = $busidebittotal = 0; // Initialize empty columns to draw chart

foreach ($busichart as $key => $value) {
	$busilabel = $busilabel . ",'" . $key . "'";
	if(array_key_exists(1,$value)){
		// Credit exists
		$busicredit = $busicredit . "," . $value[1];
		$busicredittotal = $busicredittotal + $value[1];
	}
	else{
		$busicredit = $busicredit . "," . '0';
	}
	if(array_key_exists(2,$value)){
		// Debit exist
		$busidebit = $busidebit . "," . $value[2];
		$busidebittotal = $busidebittotal + $value[2];
	}
	else{
		$busidebit = $busidebit . "," . '0';
	}
}

$busidata = "['Category'" . $busilabel . "],['Expenses'" . $busidebit . "],['Earnings'" . $busicredit . "]";

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
	<?php include 'head.php'; ?>

	<script type="text/javascript">
	google.charts.load('current', {'packages':['corechart', 'bar']});
	google.charts.setOnLoadCallback(drawOverview);
	google.charts.setOnLoadCallback(drawPersonal);
	google.charts.setOnLoadCallback(drawBusiness);

	function drawOverview() {
		var data = google.visualization.arrayToDataTable([
			['Category','Expenses','Earnings'],
			['Personal',<?php echo $personaldebittotal; ?>,<?php echo $personalcredittotal; ?>],
			['Business',<?php echo $busidebittotal; ?>,<?php echo $busicredittotal; ?>]
		]);

		var options = {
			series: [
			{color: '#3366CC'},
			{color: '#109618'}
		],
			legend: {position: 'top', maxLines: 3}
		};
		var chart = new google.visualization.ColumnChart(document.getElementById('overview'));
		chart.draw(data, options);
	}

	function drawPersonal() {
		var data = google.visualization.arrayToDataTable([
			<?php echo $personaldata; ?>
		]);

		var options = {
			isStacked: 'percent',
			legend: {position: 'top', maxLines: 3},
			hAxis: {
				minValue: 0,
				ticks: [0, .25, .5, .75, 1]
			}
		};
		var chart = new google.visualization.BarChart(document.getElementById('personal'));
		chart.draw(data, options);
	}

	function drawBusiness() {
		var data = google.visualization.arrayToDataTable([
			<?php echo $busidata; ?>
		]);

		var options = {
			isStacked: 'percent',
			legend: {position: 'top', maxLines: 3},
			hAxis: {
				minValue: 0,
				ticks: [0, .25, .5, .75, 1]
			}
		};
		var chart = new google.visualization.BarChart(document.getElementById('business'));
		chart.draw(data, options);
	}
	</script>
</head>
<body>

<?php include 'navbar.php';?>

<div class="container-fluid">
	<div class="row">
	<div class="col-md-3">
		<?php include 'financeside.php';?>
		<?php include 'financeperiod.php';?>
	</div>
	  <div class="col-md-9">
			<div class="card">
	      <h2><i class="far fa-credit-card"></i> <?php echo strtoupper(date('F Y', strtotime($year . "-" . $month . "-01"))); ?></h2>
	    </div>
			<div class="row">
				<div class="col-lg-6">
					<div class="card">
			      <h3>Total expenses</h3>
			      <div id="overview" style="z-index: 1; width: 99%; height: 400px; display: inline-block;"></div>
					</div>
				</div>
				<div class="col-lg-6">
					<div class="card">
			      <h3>Something</h3>
			      <p>Something else</p>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-lg-6">
					<div class="card">
			      <h3>Breakdown personal expenses</h3>
						<div id="personal" style="z-index: 1; width: 99%; height: auto; display: inline-block;"></div>
						<p>Total expenses: <?php echo $personaldebittotal; ?><br />
						<p>Total earnings: <?php echo $personalcredittotal; ?></p>
					</div>
				</div>
				<div class="col-lg-6">
					<div class="card">
						<h3>Breakdown business expenses</h3>
						<div id="business" style="z-index: 1; width: 99%; height: auto; display: inline-block;"></div>
						<p>Total expenses: <?php echo $busidebittotal; ?><br />
						<p>Total earnings: <?php echo $busicredittotal; ?></p>
					</div>
				</div>
			</div>
			<div class="card">
				<h3>Expense overview</h3>
				<table class="table table-sm table-striped table-hover">
					<thead class="bg-logreen text-white">
		      	<tr>
		          <th>Date</th>
		          <th>Location</th>
		          <th>Description</th>
		          <th>Category</th>
		          <th>Subcategory</th>
		          <th>Amount</th>
		          <th>Type</th>
		        </tr>
					</thead>
					<tbody>
	      		<?php echo $expenses; ?>
					</tbody>
	      </table>
	    </div>
	  </div>
	</div>
</div>

<?php include 'footer.php';?>

</body>
</html>
