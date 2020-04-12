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

		$update_err = "<h5>Update complete!</h5>";
	}

	// Close statement
	$stmt->close();
}

//Pull categories
$sql = "SELECT * FROM `tbl_fin_category`";
if($stmt = $mysqli->query($sql)){
	while($row = mysqli_fetch_array($stmt)) {
		$values[] = "<option value=\"".$row['id']."\">".$row['description']."</option>";
	}

	$categories = implode("",$values);

	} else{
	echo "Couldn't fetch categories. Please try again later.";
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

// Pull Locations
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

// Pull Descriptions
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
	<?php include 'head.php'; ?>
	<script>
	$(document).ready(function(){
	    $('#category').on('change', function(){
	        var categoryID = $(this).val();
	        if(categoryID){
	            $.ajax({
	                type:'POST',
	                url:'finance_ajaxData.php',
	                data:'category_id='+categoryID,
	                success:function(html){
	                    $('#subcategory').html(html);
	                }
	            });
	        }else{
	            $('#subcategory').html('<option value="">Select category first</option>');
	        }
	    });
	});
	</script>
</head>
<body>

<?php include 'navbar.php';?>

<div class="container-fluid">
	<div class="row">
	<div class="col-md-3">
	<?php include 'financeside.php';?>
	</div>
	  <div class="col-md-9">
	    <div class="card">
	      <h2><i class="far fa-credit-card"></i> PERSONAL FINANCES</h2>
	    </div>
			<div class="card">
	      <h3>Stuff to work on</h3>
	      <ul>
					<li>Yearly overview, show graph for entire year and not only for the months in which data is available.</li>
					<li>Time analysis - Line chart per subcategory on progress of expense/income/savings over time.</li>
				</ul>
	    </div>
	    <div class="card">
	      <h3>Add an expense</h3>
	      <?php echo $update_err; ?>
				<div class="row">
	      	<div class="col-sm-6 col-lg-4">
			      <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
							<div class="input-container">
								<i class="far fa-calendar-alt icon"></i>
								<input class="input-field" type="date" name="date" autofocus><?php echo $date_err; ?>
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
								<i class="fas fa-list icon"></i>
								<select class="input-field" id="category" name="category"><option value="">Select category</option><?php echo $categories; ?></select><?php echo $category_err; ?>
							</div>
							<div class="input-container">
								<i class="far fa-list-alt icon"></i>
								<select class="input-field" id="subcategory" name="subcategory"><option value="">Select category first</option></select><?php echo $subcategory_err; ?>
							</div>
						</div>
		      	<div class="col-sm-6 col-lg-4">
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
									<option value="2">Business</option>
									<option value="0" selected>Personal</option>
									<option value="3">Savings</option>
									<option value="1">Shared</option>
								</select><?php echo $type_err; ?>
							</div>

							<button class="formbtn" type="submit" name="submit" value="submit">Submit expense</button>
						</div>
					</div>
	      </form>
	    </div>
	  </div>
	</div>
</div>

<?php include 'footer.php';?>

</body>
</html>
