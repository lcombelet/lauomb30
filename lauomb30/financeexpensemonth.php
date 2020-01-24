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
						<div class="input-container">
							<i class="fas fa-search icon"></i>
							<input class="input-field" type="text" id="myInput" onkeyup="searchTable()" placeholder="Search expenses.."></p>
						</div>

						<table class="table table-sm table-striped table-hover" id="myTable">
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

<script>
function searchTable() {
  // Declare variables
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("myInput");
  filter = input.value.toUpperCase();
  table = document.getElementById("myTable");
  tr = table.getElementsByTagName("tr");

  // Loop through all table rows, and hide those who don't match the search query
  for (i = 0; i < tr.length; i++) {
    td = tr[i].getElementsByTagName("td")[1];
    if (td) {
      txtValue = td.textContent || td.innerText;
      if (txtValue.toUpperCase().indexOf(filter) > -1) {
        tr[i].style.display = "";
      } else {
        tr[i].style.display = "none";
      }
    }
  }
}
</script>

<?php include 'footer.php';?>

</body>
</html>
