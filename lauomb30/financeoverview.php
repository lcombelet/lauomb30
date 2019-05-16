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
		if($row['key'] == 1){
			$amount = "(".$row['amount'].")";
		} else{
			$amount = $row['amount'];
		}

		$values[] = "<tr><td>".$row['date']."</td><td>".$row['location']."</td><td>".$row['description']."</td><td>".$row['category']."</td><td>".$row['subcategory']."</td><td>".$amount."</td><td>".ucfirst($row['type_descr'])."</td></tr>";
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

$sql = "SELECT `category`,`counterpart`,`key`,`type`,`amount` FROM `vw_fin_monthly_overview` WHERE (`counterpart`= 1 AND `year` = '$year' AND `month` = '$month') ORDER BY `amount` DESC,`category`,`counterpart`,`key`,`type`";
if($stmt = $mysqli->query($sql)){
	while($row = mysqli_fetch_array($stmt)) {

		// Split expenses into different tables
		switch ($row['type']) {
			case 3: // savings
				$savichart[$row['category']][$row['key']] = $row['amount'];
				break;
			case 2: // business
				$busichart[$row['category']][$row['key']] = $row['amount'];
				break;
			case 1: // shared
				$sharchart[$row['category']][$row['key']] = $row['amount'];
				break;
			default: // personal
				$perschart[$row['category']][$row['key']] = $row['amount'];
		}
	}

	// Format to match Google Chart format;
	foreach ($perschart as $key => $value) {
		if(array_key_exists(1,$value)){
			// Credit exists
			$perscreditdata = $perscreditdata . ",['" . $key . "', " . $value[1] . "]";
			$perscredittotal = $perscredittotal + $value[1];
		}
		if(array_key_exists(2,$value)){
			// Debit exist
			$persdebitdata = $persdebitdata . ",['" . $key . "', " . $value[2] . "]";
			$persdebittotal = $persdebittotal + $value[2];
		}
	}

	// Format to match Google Chart format;
	foreach ($sharchart as $key => $value) {
		if(array_key_exists(1,$value)){
			// Credit exists
			$sharcreditdata = $sharcreditdata . ",['" . $key . "', " . $value[1] . "]";
			$sharcredittotal = $sharcredittotal + $value[1];
		}
		if(array_key_exists(2,$value)){
			// Debit exist
			$shardebitdata = $shardebitdata . ",['" . $key . "', " . $value[2] . "]";
			$shardebittotal = $shardebittotal + $value[2];
		}
	}

	// Format to match Google Chart format;
	foreach ($busichart as $key => $value) {
		if(array_key_exists(1,$value)){
			// Credit exists
			$busicreditdata = $busicreditdata . ",['" . $key . "', " . $value[1] . "]";
			$busicredittotal = $busicredittotal + $value[1];
		}
		if(array_key_exists(2,$value)){
			// Debit exist
			$busidebitdata = $busidebitdata . ",['" . $key . "', " . $value[2] . "]";
			$busidebittotal = $busidebittotal + $value[2];
		}
	}

	// Format to match Google Chart format;
	foreach ($savichart as $key => $value) {
		if(array_key_exists(2,$value)){
			// Debit exist
			$savidebitdata = $savidebitdata . ",['" . $key . "', " . $value[2] . "]";
			$savidebittotal = $savidebittotal + $value[2];
		}
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
	<?php $title = "LauOmb Webserver - Personal finances";
  include 'head.php'; ?>
	<script type="text/javascript">
	google.charts.load('current', {'packages':['corechart', 'bar']});
	google.charts.setOnLoadCallback(drawPersDebit);
	google.charts.setOnLoadCallback(drawPersCredit);
	google.charts.setOnLoadCallback(drawSharDebit);
	google.charts.setOnLoadCallback(drawSharCredit);
	google.charts.setOnLoadCallback(drawBusiDebit);
	google.charts.setOnLoadCallback(drawBusiCredit);
	google.charts.setOnLoadCallback(drawSaviDebit);

	function drawPersDebit() {
        var data = google.visualization.arrayToDataTable([
          ['Category', 'Expenses']
					<?php echo $persdebitdata; ?>
        ]);

      var options = {
				title: 'Expenses - <?php echo $persdebittotal; ?>',
				fontName: 'Karla',
				fontSize: 15,
        titleTextStyle: {
					fontSize: 20
				},
				legend: {
					position: 'top',
					maxLines: 3
				}
      };

        var chart = new google.visualization.PieChart(document.getElementById('persdebit'));
        chart.draw(data, options);
    }

		function drawPersCredit() {
	        var data = google.visualization.arrayToDataTable([
	          ['Category', 'Earnings']
						<?php echo $perscreditdata; ?>
	        ]);

	      var options = {
					title: 'Earnings - <?php echo $perscredittotal; ?>',
					fontName: 'Karla',
					fontSize: 15,
					titleTextStyle: {
						fontSize: 20
					},
					legend: {
						position: 'top',
						maxLines: 3
					}
	      };

	        var chart = new google.visualization.PieChart(document.getElementById('perscredit'));
	        chart.draw(data, options);
	    }

			function drawSharDebit() {
		        var data = google.visualization.arrayToDataTable([
		          ['Category', 'Expenses']
							<?php echo $shardebitdata; ?>
		        ]);

		      var options = {
						title: 'Expenses - <?php echo $shardebittotal; ?>',
						fontName: 'Karla',
						fontSize: 15,
		        titleTextStyle: {
							fontSize: 20
						},
						legend: {
							position: 'top',
							maxLines: 3
						}
		      };

		        var chart = new google.visualization.PieChart(document.getElementById('shardebit'));
		        chart.draw(data, options);
		    }

				function drawSharCredit() {
			        var data = google.visualization.arrayToDataTable([
			          ['Category', 'Earnings']
								<?php echo $sharcreditdata; ?>
			        ]);

			      var options = {
							title: 'Earnings - <?php echo $sharcredittotal; ?>',
							fontName: 'Karla',
							fontSize: 15,
							titleTextStyle: {
								fontSize: 20
							},
							legend: {
								position: 'top',
								maxLines: 3
							}
			      };

			        var chart = new google.visualization.PieChart(document.getElementById('sharcredit'));
			        chart.draw(data, options);
			    }

			function drawBusiDebit() {
						var data = google.visualization.arrayToDataTable([
							['Category', 'Expenses']
							<?php echo $busidebitdata; ?>
						]);

					var options = {
						title: 'Expenses - <?php echo $busidebittotal; ?>',
						fontName: 'Karla',
						fontSize: 15,
						titleTextStyle: {
							fontSize: 20
						},
						legend: {
							position: 'top',
							maxLines: 3
						}
					};

						var chart = new google.visualization.PieChart(document.getElementById('busidebit'));
						chart.draw(data, options);
				}

				function drawBusiCredit() {
							var data = google.visualization.arrayToDataTable([
								['Category', 'Earnings']
								<?php echo $busicreditdata; ?>
							]);

						var options = {
							title: 'Earnings - <?php echo $busicredittotal; ?>',
							fontName: 'Karla',
							fontSize: 15,
							titleTextStyle: {
								fontSize: 20
							},
							legend: {
								position: 'top',
								maxLines: 3
							}
						};

							var chart = new google.visualization.PieChart(document.getElementById('busicredit'));
							chart.draw(data, options);
					}

					function drawSaviDebit() {
								var data = google.visualization.arrayToDataTable([
									['Category', 'Expenses']
									<?php echo $savidebitdata; ?>
								]);

							var options = {
								title: 'Savings - <?php echo $savidebittotal; ?>',
								fontName: 'Karla',
								fontSize: 15,
								titleTextStyle: {
									fontSize: 20
								},
								legend: {
									position: 'top',
									maxLines: 3
								}
							};

								var chart = new google.visualization.PieChart(document.getElementById('savidebit'));
								chart.draw(data, options);
						}
	</script>
</head>
<body>

<?php include 'header.php';?>

<div class="row">
<div class="leftcolumn">
<?php include 'financeside.php';?>
<?php include 'financeperiod.php';?>
</div>
  <div class="rightcolumn">
  	<div class="card">
      <h2>Personal</h2>
			<h5><?php echo date('F, Y', strtotime($year . "-" . $month . "-01")); ?></h5>
      <p><div id="persdebit" style="z-index: 1; width: 49%; height: 500px; display: inline-block;"></div>
			<div id="perscredit" style="z-index: 1; width: 49%; height: 500px; display: inline-block;"></div></p>
		</div>
		<div class="card">
      <h2>Shared</h2>
			<p><div id="shardebit" style="z-index: 1; width: 49%; height: 500px; display: inline-block;"></div>
			<div id="sharcredit" style="z-index: 1; width: 49%; height: 500px; display: inline-block;"></div></p>
		</div>
		<div class="card">
      <h2>Business</h2>
			<p><div id="busidebit" style="z-index: 1; width: 49%; height: 500px; display: inline-block;"></div>
			<div id="busicredit" style="z-index: 1; width: 49%; height: 500px; display: inline-block;"></div></p>
		</div>
		<div class="card">
			<h2>Savings</h2>
			<p><div id="savidebit" style="z-index: 1; width: 49%; height: 500px; display: inline-block;"></div></p>
		</div>
		<div class="card">
			<h2>Expense overview</h2>
			<table>
      	<tr>
          <th>Date</th>
          <th>Location</th>
          <th>Description</th>
          <th>Category</th>
          <th>Subcategory</th>
          <th>Amount</th>
          <th>Type</th>
        </tr>
      	<?php echo $expenses; ?>
      </table>
    </div>
  </div>
</div>

<?php include 'footer.php';?>

</body>
</html>
