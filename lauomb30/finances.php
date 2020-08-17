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

	// Get POST data
	$date = $_POST['date'];
	$counterpart = $_POST['counterpart'];
	$client_type = 2;
	$description = $_POST['description'];
	$subcategory = $_POST['subcategory'];
	$amount = $_POST['amount'];
	$key = $_POST['key'];
	$type = $_POST['type'];

	// Determine if Counterpart already exists
	$sql = "SELECT `clt_id` FROM `tbl_fin_clients` WHERE `clt_name` LIKE '".$counterpart."%' LIMIT 1";

	if ($stmt = $mysqli->prepare($sql)){
		if($stmt->execute()){
			$stmt->store_result();

			$stmt->bind_result($client);
			$stmt->fetch();

			if ($stmt->num_rows == 1){
				// Counterpart exists
				} else{
				// Add client to db
				$sql= "INSERT INTO `tbl_fin_clients` (`clt_name`, `clt_client_type`) VALUES (?, ?)";

				if($stmt = $mysqli->prepare($sql)){
					$stmt->bind_param("ss", $counterpart, $client_type);
					$stmt->execute();

					$client = $stmt->insert_id;
				}

				// Close statement
				$stmt->close();
			}
		}
	}

	// Close statement
	$stmt->close();

	// Determine sold from and sold to
	if($key==1){
		$soldfrom = $client;
		$soldto = 39;
	} else{
		$soldfrom = 39;
		$soldto = $client;
	}

	// Update Expense table
	$sql = "INSERT INTO `tbl_fin_expenses` (`exp_date`, `exp_sold_from`, `exp_sold_to`, `exp_description`, `exp_category`, `exp_amount`, `exp_type`) VALUES (?, ?, ?, ?, ?, ?, ?)";

	if($stmt = $mysqli->prepare($sql)){
		$stmt->bind_param("sssssss", $date, $soldfrom, $soldto, $description, $subcategory, $amount, $type);
		$stmt->execute();

		$update_id = $stmt->insert_id;

		$update_err = "<h5>Update completed (".$update_id.")</h5>";
	} else{
		$update_err = "<h5>Error updating database</h5>";
	}

	// Close statement
	$stmt->close();
}

//Pull categories
$sql = "SELECT * FROM `tbl_fin_category` ORDER BY `description`";
if($stmt = $mysqli->query($sql)){
	while($row = mysqli_fetch_array($stmt)) {
		$values[] = "<option value=\"".$row['id']."\">".$row['description']."</option>";
	}

	$categories = implode("",$values);

	} else{
	echo "Couldn't fetch categories. Please try again later.";
}

// Clear variables
unset($values);
$stmt->close();

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

// Pull clients
$sql = "SELECT * FROM `tbl_fin_clients` ORDER BY `clt_name` ASC";
if($stmt = $mysqli->query($sql)){
	while($row = mysqli_fetch_array($stmt)) {
		$values[] = "<option value=\"".$row['clt_name']."\">";
	}

	$counterpart = implode("",$values);

	} else{
	echo "Couldn't fetch counterpart. Please try again later.";
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
								<input class="input-field" list="counterpart" name="counterpart" placeholder="Counterpart" autocomplete="off" maxlength="45" size="50">
									<datalist id="counterpart">
										<?php echo $counterpart; ?>
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
									<option value="1" selected>Personal</option>
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
