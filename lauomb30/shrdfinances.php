<?php
// Initialize the session
session_start();

// If session variable is not set it will redirect to login page
if(!isset($_SESSION['username']) || empty($_SESSION['username'] || in_array("13", $_SESSION['authorizations']))){
	header("location: login.php");
	exit;
}

// Include config file
require_once 'config.php';

// Define variables and initialize with empty values
$update_err = $date_err = $location_err = $description_err = $category_err = $subcategory_err = $amount_err = $counterpart_err = $reimbursement_err = "";
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
	$counterpart = $_POST['counterpart'];
	$reimbursement = $_POST['reimbursement'];
	$type = 1;
	$key = 2;

	// Update expense table
	$sql = "INSERT INTO `tbl_fin_expenses` (`date`, `location`, `description`, `subcategory`, `amount`, `type`) VALUES (?, ?, ?, ?, ?, ?)";

	if($stmt = $mysqli->prepare($sql)){
		$stmt->bind_param("ssssss", $date, $location, $description, $subcategory, $amount, $type);
		$stmt->execute();
	}

	// Pull ID from expense table for updating Payments table
	$sql = "SELECT * FROM `vw_fin_most_recent_transaction`";

	if($stmt = $mysqli->query($sql)){
		while($row = mysqli_fetch_array($stmt)) {
			$fin_expense_id = $row['id'];
		}
	}

	// Update payment table
	$sql = "INSERT INTO `tbl_fin_payments` (`fin_expenses_id`, `counterpart`, `key`) VALUES (?, ?, ?)";

	if($stmt = $mysqli->prepare($sql)){
		$stmt->bind_param("sss", $fin_expense_id, $counterpart, $key);
		$stmt->execute();

		$update_err = "<h5>Update complete!<h5>";
	}

	// Update payment table again in case of reimbursement
	if($reimbursement == 1){
		// Switch counterpart
		if($counterpart == 1){
			$counterpart = 2;
		} else{
			$counterpart = 1;
		}

		$key = 1; // Switch posting key

		$sql = "INSERT INTO `tbl_fin_payments` (`fin_expenses_id`, `counterpart`, `key`) VALUES (?, ?, ?)";

		if($stmt = $mysqli->prepare($sql)){
			$stmt->bind_param("sss", $fin_expense_id, $counterpart, $key);
			$stmt->execute();

			$update_err = "<h5>Reimbursement added!<h5>";
		}
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
	<?php $title = "LauOmb Webserver - Shared finances";
  include 'head.php'; ?>
</head>
<body>

<?php include 'header.php';?>

<div class="row">
  <div class="leftcolumn">
    <div class="card">
      <h2>Shared finances</h2>
      <h5>Useless bit of text here..</h5>
      <p>This page provides an overview of shared expenses made to date, and calculates open balances.</p>
    </div>
		<div class="card">
      <h2>Stuff to work on</h2>
      <ul>
				<li>Yearplan - Overview of total expense per month per counterpart.</li>
			</ul>
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
            <td><label>Paid by:</label></td>
            <td><select name="counterpart"><?php echo $counterparts; ?></select><?php echo $counterpart_err; ?></td>
	        </tr>
					<tr>
						<td><label>Reimbursement:</label></td>
						<td><select name="key">
									<option value="0" selected>No</option>
									<option value="1">Yes</option>
								</select><?php echo $reimbursement_err; ?></td>
					</tr>
	        <tr>
	        	<td><input type="submit" name="submit" value="Submit expense"></td>
	        </tr>
        </table>
      </form>
    </div>
  </div>
<div class="rightcolumn">
  <?php include 'shrdfinanceside.php';?>
  <?php include 'social.php';?>
</div>
</div>

<?php include 'footer.php';?>

</body>
</html>
