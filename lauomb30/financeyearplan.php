<?php
// Initialize the session
session_start();

// If session variable is not set it will redirect to login page
if(!isset($_SESSION['username']) || empty($_SESSION['username'] || in_array("6", $_SESSION['authorizations']))){
	header("location: login.php");
	exit;
}

// Include config file
require_once 'config.php';

// Define variables and initialize with empty values
$year_err = "";

// Pull expenses from db
if(isset($_POST['submit'])) {
	unset($_POST['submit']);

	$year = $_POST['year'];
} else{
	$year = date('Y');
}

$chartdata = array();
$calchartdata = array();
$caltranschartdata = array();

// Pull chart data
$sql = "SELECT `month`,`year`,`key`,`amount` FROM `vw_fin_personal_yearplan` WHERE (`year` = '$year') ORDER BY `month`, `key` DESC";
if($stmt = $mysqli->query($sql)){
	while($row = mysqli_fetch_array($stmt)) {

		// Build multidimensional array
		$chart[$row['month']][$row['key']] = $row['amount'];
	}

	foreach ($chart as $key => $value) {
		$month = date('F', strtotime($year . "-" . $key . "-01"));
		$debit = $value[2] + 0;
		$credit = $value[1] + 0;

		$chartdata[] = "['" . $month . "'," . $debit . "," . $credit . "]";
	}

$data = implode(",", $chartdata);

} else{
	echo "Couldn't fetch chart data. Please try again later.";
}

// Clear variables
$stmt->close();

// Pull data for calendar chart
$sql = "SELECT `date`,`year`,`month`,`key`,`amount` FROM `vw_fin_personal_daily_overview` WHERE (`year` = '$year') ORDER BY `date`, `key` DESC";
if($stmt = $mysqli->query($sql)){
	while($row = mysqli_fetch_array($stmt)) {

		// Build multidimensional array
		$calchart[$row['date']][$row['key']] = $row['amount'];
	}

	foreach ($calchart as $key => $value) {
		$calyear = date("Y", strtotime($key));
		$calmonth = date("m", strtotime($key)) - 1; // Javascript months start at 0 for january
		$calday = date("d", strtotime($key));
		$dayresult = ($value[1] + 0) - ($value[2] + 0);

		$calchartdata[] = "[new Date(" . $calyear . ", " . $calmonth . ", " . $calday . "), " . $dayresult . "]";
	}

$caldata = implode(",", $calchartdata);

} else{
	echo "Couldn't fetch caldendar data. Please try again later.";
}

// Clear variables
$stmt->close();

// Pull data for transactions chart
$sql = "SELECT `date`,`year`,`month`,`transactions` FROM `vw_fin_personal_daily_transactions` WHERE (`year` = '$year') ORDER BY `date`";
if($stmt = $mysqli->query($sql)){
	while($row = mysqli_fetch_array($stmt)) {
		$calyear = date("Y", strtotime($row['date']));
		$calmonth = date("m", strtotime($row['date'])) - 1; // Javascript months start at 0 for january
		$calday = date("d", strtotime($row['date']));

		$caltranschartdata[] = "[new Date(" . $calyear . ", " . $calmonth . ", " . $calday . "), " . $row['transactions'] . "]";
		$maxtransactions[] = $row['transactions'];
	}

$caltransdata = implode(",", $caltranschartdata);

//round up to nearest multiple of 5 for transactions graph
$maxtransactions = max($maxtransactions);
$maxtransactions = ceil($maxtransactions/5) * 5;

} else{
	echo "Couldn't fetch transaction data. Please try again later.";
}

// Clear variables
$stmt->close();

// Pull years
$values = array();
$sql = "SELECT * FROM `vw_fin_years`";
if($stmt = $mysqli->query($sql)){
	while($row = mysqli_fetch_array($stmt)) {
		$sqlyear = $row['year'];
		$values[] = "<option value=\"" . $sqlyear . "\">" . $sqlyear . "</option>";
	}

	$years = implode("",$values);

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
	<?php $title = "LauOmb Webserver - Personal finances";
  include 'head.php'; ?>
	<script type="text/javascript">
		google.charts.load('current', {'packages':['corechart', 'bar', 'calendar']});
		google.charts.setOnLoadCallback(drawChart);
		google.charts.setOnLoadCallback(drawCalendar);
		google.charts.setOnLoadCallback(drawTransactions);

		function drawChart() {
			var data = google.visualization.arrayToDataTable([
				['Month','Expenses','Earnings'],
				<?php echo $data; ?>
			]);

			var options = {
				series: [
				{color: '#3366CC'},
				{color: '#109618'}
			],
				legend: {position: 'top', maxLines: 3}
			};

			var chart = new google.visualization.ColumnChart(document.getElementById('chart'));
			chart.draw(data, options);
		}

		function drawCalendar() {
			var dataTable = new google.visualization.DataTable();
       dataTable.addColumn({ type: 'date', id: 'Date' });
       dataTable.addColumn({ type: 'number', id: 'Result' });
       dataTable.addRows([
          <?php echo $caldata; ?>
        ]);

				var options = {
 			          title: "",
								calendar: {
									cellSize: 20,
									yearLabel: {
										fontSize: 10,
										color: 'white'
									}
								},
								colorAxis: {
									colors:['#3366CC','white','#109618'],
									values:[-1000,0,1000]
								},
 			        };

       var chart = new google.visualization.Calendar(document.getElementById('calendar'));
       chart.draw(dataTable, options);
		}

		function drawTransactions() {
			var dataTable = new google.visualization.DataTable();
       dataTable.addColumn({ type: 'date', id: 'Date' });
       dataTable.addColumn({ type: 'number', id: 'Transactions' });
       dataTable.addRows([
          <?php echo $caltransdata; ?>
        ]);

				var options = {
 			          title: "",
								calendar: {
									cellSize: 20,
									yearLabel: {
										fontSize: 10,
										color: 'white'
									}
								},
								colorAxis: {
									colors:['white','#3366CC'],
									values:[0,<?php echo $maxtransactions; ?>]
								},
 			        };

       var chart = new google.visualization.Calendar(document.getElementById('transactions'));
       chart.draw(dataTable, options);
		}
	</script>
</head>
<body>

<?php include 'header.php';?>

<div class="row">
<div class="col-25">
<?php include 'financeside.php';?>
<?php include 'financeyear.php';?>
</div>
  <div class="col-75">
		<div class="card">
			<h1><i class="far fa-credit-card"></i> PERSONAL FINANCES - <?php echo $year; ?></h1>
		</div>
		<div class="card">
			<h2>Monthly summary</h2>
			<div id="chart" style="z-index: 1; width: 99%; height: 500px; display: inline-block;"></div>
		</div>
		<div class="card">
			<h2>Daily overview</h2>
			<h3>Net result</h3>
			<div id="calendar" style="z-index: 1; width: 99%; height: 250px; display: inline-block;"></div>
			<h3>Transactions</h3>
			<div id="transactions" style="z-index: 1; width: 99%; height: 250px; display: inline-block;"></div>
		</div>
  </div>
</div>

<?php include 'footer.php';?>

</body>
</html>
