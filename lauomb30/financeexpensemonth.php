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
				<div class="col">
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
	</div>
</div>

<?php include 'footer.php';?>

</body>
</html>
