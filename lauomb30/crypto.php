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
$update_err = $date_err = $location_err = $description_err = $category_err = $currency_err = $amount_err = $counterpart_err = $curramount_err = $key_err = "";
$values = array();
$balance = array();

// Update database with new expense
if(isset($_POST['submit'])) {
	unset($_POST['submit']);

	$date = $_POST['date'];
	$location = $_POST['location'];
	$description = $_POST['description'];
	$amount = $_POST['amount'];
	$key = $_POST['key'];
	$currency = $_POST['currency'];
	$curramount = $_POST['curramount'];
	$counterpart = 1;
	$subcategory = 59;
	$type = 0;

	// Negate amount in case of sell
	if($key == 1){
		$curramount = $curramount * -1;
	}

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

	// Update Crypto table
	$sql = "INSERT INTO `tbl_crypto_transactions` (`fin_expenses_id`, `tbl_crypto_id`, `amount`) VALUES (?, ?, ?)";

	if($stmt = $mysqli->prepare($sql)){
		$stmt->bind_param("sss", $fin_expense_id, $currency, $curramount);
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

// Pull currencies
$sql = "SELECT * FROM `vw_crypto_currencies`";
if($stmt = $mysqli->query($sql)){
	while($row = mysqli_fetch_array($stmt)) {
		$values[] = "<option value=\"".$row['id']."\">".$row['code']." - ".$row['description']."</option>";
	}

	$currencies = implode("",$values);
	unset($values);

	} else{
	echo "Couldn't fetch currencies. Please try again later.";
}

// Clear variables
unset($values);
$stmt->close();

// Pull locations
$sql = "SELECT * FROM `vw_fin_locations`";
if($stmt = $mysqli->query($sql)){
	while($row = mysqli_fetch_array($stmt)) {
		$values[] = "<option value=\"".$row['location']."\">";
	}

	$locations = implode("",$values);
	unset($values);

	} else{
	echo "Couldn't fetch locations. Please try again later.";
}

// Clear variables
unset($values);
$stmt->close();

// Pull descriptions
$sql = "SELECT * FROM `vw_fin_descriptions`";
if($stmt = $mysqli->query($sql)){
	while($row = mysqli_fetch_array($stmt)) {
		$values[] = "<option value=\"".$row['description']."\">";
	}

	$descriptions = implode("",$values);
	unset($values);

	} else{
	echo "Couldn't fetch descriptions. Please try again later.";
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
	<?php $title = "LauOmb Webserver - Crypto currencies";
  include 'head.php'; ?>
</head>
<body>

<?php include 'header.php';?>

<div class="row">
<div class="col-25">
<?php include 'cryptoside.php';?>
</div>
  <div class="col-75">
    <div class="card">
      <h1><i class="fab fa-btc"></i> CRYPTO CURRENCIES</h1>
    </div>
		<div class="card">
      <h2>Stuff to work on</h2>
      <ul>
				<li>Current value of portfolio, connect to Coinbase API</li>
				<li>Historical transaction overview</li>
			</ul>
    </div>
		<div class="card">
      <h2>Current portfolio</h2>
      <p>Stuff here.</p>
    </div>
		<div class="card">
      <a name="addtransaction"></a><h2>Add a transaction</h2>
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
							<input class="input-field" list="locations" name="location" placeholder="Location" autocomplete="off" maxlength="45" size="50">
								<datalist id="locations">
									<?php echo $locations; ?>
								</datalist>
							<?php echo $location_err; ?>
						</div>
						<div class="input-container">
							<i class="fas fa-search icon"></i>
							<input class="input-field" list="descriptions" name="description" placeholder="Description" autocomplete="off" maxlength="45" size="50">
								<datalist id="descriptions">
									<?php echo $descriptions; ?>
								</datalist>
							<?php echo $description_err; ?>
						</div>
						<div class="input-container">
							<i class="far fa-list-alt icon"></i>
							<select class="input-field" name="currency"><?php echo $currencies; ?></select><?php echo $currency_err; ?>
						</div>
					</div>
	      	<div class="col-50">
						<div class="input-container">
							<i class="fas fa-euro-sign icon"></i>
							<input class="input-field" type="number" name="curramount" placeholder="Amount" min="0" step="0.00000001"><?php echo $curramount_err; ?>
						</div>
						<div class="input-container">
							<i class="fas fa-euro-sign icon"></i>
							<input class="input-field" type="number" name="amount" placeholder="Value" min="0" step="0.01"><?php echo $amount_err; ?>
						</div>
						<div class="input-container">
							<i class="fas fa-balance-scale icon"></i>
							<select class="input-field" name="key">
								<option value="1">Sell crypto</option>
								<option value="2" selected>Buy crypto</option>
							</select><?php echo $key_err; ?>
						</div>
					</div>
				</div>

				<button type="submit" name="submit" value="submit">Submit transaction</button>

      </form>
    </div>
  </div>
</div>

<?php include 'footer.php';?>

</body>
</html>
