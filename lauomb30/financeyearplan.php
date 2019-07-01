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
		google.charts.load('current', {'packages':['corechart', 'bar']});
		google.charts.setOnLoadCallback(drawChart);

		function drawChart() {
			var data = google.visualization.arrayToDataTable([
				['Month','Expenses','Earnings'],
				<?php echo $data; ?>
			]);

			var options = {
				legend: {position: 'top', maxLines: 3}
			};
			var chart = new google.visualization.ColumnChart(document.getElementById('chart'));
			chart.draw(data, options);
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
			<h1><i class="far fa-credit-card"></i> PERSONAL FINANCES</h1>
		</div>
		<div class="card">
			<h2>Year overview for <?php echo $year; ?></h2>
			<div id="chart" style="z-index: 1; width: 99%; height: 500px; display: inline-block;"></div>
		</div>
  </div>
</div>

<?php include 'footer.php';?>

</body>
</html>
