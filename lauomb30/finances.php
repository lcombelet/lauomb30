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
$update_err = $date_err = $location_err = $description_err = $category_err = $subcategory_err = $amount_err = $counterpart_err = $type_err = $key_err = "";
$values = array();
$balance = array();

// Update database with new expense
if(isset($_POST['submit'])) {
	unset($_POST['submit']);

	$date = $_POST['date'];
	$location = $_POST['location'];
	$description = $_POST['description'];
	$subcategory = $_POST['subcategory'];
	$amount = $_POST['amount'];
	$counterpart = 1;
	$key = $_POST['key'];
	$type = $_POST['type'];

	// Update Expense table
	$sql = "INSERT INTO `tbl_fin_expenses` (`date`, `location`, `description`, `subcategory`, `amount`, `type`) VALUES (?, ?, ?, ?, ?, ?)";

	if($stmt = $mysqli->prepare($sql)){
		$stmt->bind_param("ssssss", $date, $location, $description, $subcategory, $amount, $type);
		$stmt->execute();
	}

	// Pull ID from Expense table for updating Payments table
	$sql = "SELECT * FROM `vw_fin_most_recent_transaction`";

	if($stmt = $mysqli->query($sql)){
		while($row = mysqli_fetch_array($stmt)) {
			$fin_expense_id = $row['id'];
		}
	}

	// Update Payment table
	$sql = "INSERT INTO `tbl_fin_payments` (`fin_expenses_id`, `counterpart`, `key`) VALUES (?, ?, ?)";

	if($stmt = $mysqli->prepare($sql)){
		$stmt->bind_param("sss", $fin_expense_id, $counterpart, $key);
		$stmt->execute();

		$update_err = "<h5>Update complete!<h5>";
	}

	// Close statement
	$stmt->close();
}

// Pull subcategories
$sql = "SELECT * FROM `vw_fin_subcategory`";
if($stmt = $mysqli->query($sql)){
	while($row = mysqli_fetch_array($stmt)) {
		$values[] = "<option value=\"".$row['id']."\">".$row['category']." - ".$row['description']."</option>";
	}

	$subcategories = implode("",$values);

	} else{
	echo "Couldn't fetch subcategories. Please try again later.";
}

// Clear variables
unset($values);
$stmt->close();

// Pull Counterparts
$sql = "SELECT * FROM `vw_fin_counterparts`";
if($stmt = $mysqli->query($sql)){
	while($row = mysqli_fetch_array($stmt)) {
		$values[] = "<option value=\"".$row['id']."\">".$row['name']."</option>";
	}

	$counterparts = implode("",$values);
	unset($values);

	} else{
	echo "Couldn't fetch subcategories. Please try again later.";
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
</head>
<body>

<?php include 'header.php';?>

<div class="row">
<div class="col-25">
<?php include 'financeside.php';?>
</div>
  <div class="col-75">
    <div class="card">
      <h2>Personal finances</h2>
      <h5>Useless bit of text here..</h5>
      <p>This page provides an overview of my personal expenses.</p>
    </div>
		<div class="card">
      <h2>Stuff to work on</h2>
      <ul>
				<li>Yearplan - Total income vs expense/savings (stacked) per month.</li>
				<li>Expense overview - Split expenses and income graphs, change to horizontal bar charts for readability and show side by side.</li>
				<li>Time analysis - Line chart per subcategory on progress of expense/income/savings over time.</li>
			</ul>
    </div>
    <div class="card">
      <a name="addexpense"></a><h2>Add an expense</h2>
      <?php echo $update_err; ?>
			<div class="row">
      	<div class="col-50">
		      <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
						<div class="input-container">
							<i class="far fa-calendar-alt icon"></i>
							<input class="input-field" type="date" name="date"><?php echo $date_err; ?>
						</div>
						<div class="input-container">
							<i class="fas fa-location-arrow icon"></i>
							<input class="input-field" type="text" name="location" placeholder="Location" maxlength="45" size="50"><?php echo $location_err; ?>
						</div>
						<div class="input-container">
							<i class="fas fa-search icon"></i>
							<input class="input-field" type="text" name="description" placeholder="Description" maxlength="45" size="50"><?php echo $description_err; ?>
						</div>
						<div class="input-container">
							<i class="far fa-list-alt icon"></i>
							<select class="input-field" name="subcategory"><?php echo $subcategories; ?></select><?php echo $subcategory_err; ?>
						</div>
					</div>
	      	<div class="col-50">
						<div class="input-container">
							<i class="fas fa-euro-sign icon"></i>
							<input class="input-field" type="number" name="amount" placeholder="Amount" min="0" step="0.01"><?php echo $amount_err; ?>
						</div>
						<div class="input-container">
							<i class="fas fa-balance-scale icon"></i>
							<select class="input-field" name="key">
								<option value="1">Credit</option>
								<option value="2" selected>Debit</option>
							</select><?php echo $key_err; ?>
						</div>
						<div class="input-container">
							<i class="fas fa-chart-bar icon"></i>
							<select class="input-field" name="type">
								<option value="0" selected>Personal</option>
								<option value="1">Shared</option>
								<option value="2">Business</option>
								<option value="3">Savings</option>
							</select><?php echo $type_err; ?>
						</div>
					</div>
				</div>

				<button type="submit">Login</button>

      </form>
    </div>
  </div>
</div>

<?php include 'footer.php';?>

</body>
</html>
