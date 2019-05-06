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
$update_err = $date_err = $location_err = $description_err = $category_err = $subcategory_err = $amount_err = $counterpart_err = $shared_err = $key_err = "";
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
	$shared = $_POST['shared'];

	// Update Expense table
	$sql = "INSERT INTO `tbl_fin_expenses` (`date`, `location`, `description`, `subcategory`, `amount`, `shared`) VALUES (?, ?, ?, ?, ?, ?)";

	if($stmt = $mysqli->prepare($sql)){
		$stmt->bind_param("ssssss", $date, $location, $description, $subcategory, $amount, $shared);
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
  <div class="leftcolumn">
    <div class="card">
      <h2>Personal finances</h2>
      <h5>Useless bit of text here..</h5>
      <p>This page provides an overview of personal expenses made to date.</p>
    </div>
    <div class="card">
      <a name="addexpense"></a><h2>Add an expense</h2>
      <?php echo $update_err; ?>
      <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
	      <table>
	        <tr>
            <td><label>Date of expense:</label></td>
            <td><input type="date" name="date"><?php echo $date_err; ?></td>
	        </tr>
	        <tr>
            <td><label>Location:</label></td>
            <td><input type="text" name="location" maxlength="45" size="50"><?php echo $location_err; ?></td>
	        </tr>
	        <tr>
            <td><label>Description:</label></td>
            <td><input type="text" name="description" maxlength="45" size="50"><?php echo $description_err; ?></td>
	        </tr>
	        <tr>
            <td><label>Category:</label></td>
            <td>xxx<?php echo $category_err; ?></td>
	        </tr>
	        <tr>
            <td><label>Subcategory:</label></td>
            <td><select name="subcategory"><?php echo $subcategories; ?></select><?php echo $subcategory_err; ?></td>
	        </tr>
	        <tr>
            <td><label>Amount:</label></td>
            <td><input type="number" name="amount" min="0" step="0.01"><?php echo $amount_err; ?></td>
	        </tr>
					<tr>
						<td><label>Key:</label></td>
						<td><select name="key">
									<option value="2">Debit</option>
									<option value="1">Credit</option>
								</select><?php echo $key_err; ?></td>
					</tr>
					<tr>
						<td><label>Shared:</label></td>
						<td><select name="shared">
									<option value="0">No</option>
									<option value="1">Yes</option>
								</select><?php echo $shared_err; ?></td>
					</tr>
	        <tr>
	        	<td><input type="submit" name="submit" value="Submit expense"></td>
	        </tr>
        </table>
      </form>
    </div>
  </div>
<div class="rightcolumn">
  <?php include 'financeside.php';?>
</div>
</div>

<?php include 'footer.php';?>

</body>
</html>
