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

	// Pull Expenses
	$sql = "SELECT * FROM `vw_fin_expenses` WHERE (month(`date`) = '$month' AND year(`date`) = '$year')";
	if($stmt = $mysqli->query($sql)){
		while($row = mysqli_fetch_array($stmt)) {
			if($row['key'] == "Credit"){
				$amount = "(".$row['amount'].")";
			} else{
				$amount = $row['amount'];
			}
			$values[] = "<tr><td>".$row['date']."</td><td>".$row['location']."</td><td>".$row['description']."</td><td>".$row['category']."</td><td>".$row['subcategory']."</td><td>".$amount."</td><td>".$row['name']."</td></tr>";
		}

		$expenses = implode("",$values);
		unset($values);

		} else{
		echo "Couldn't fetch expenses. Please try again later.";
	}

// Pull months and years
$values = array();
$sql = "SELECT * FROM vw_fin_periods";
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
	<?php $title = "LauOmb Webserver - Finance reporting";
    include 'head.php'; ?>
</head>
<body>

<?php include 'header.php';?>

<div class="row">
    <div class="leftcolumn">
    	<div class="card">
            <h2>Select period</h2>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <table>
                <tr>
                    <td><label>Period:</label></td>
                    <td><select name="period"><?php echo $periods; ?></select><?php echo $period_err; ?></td>
                </tr>
                <tr>
                	<td><input type="submit" name="submit" value="Submit"></td>
                </tr>
            </table>
            </form>
        </div>
        <div class="card">
            <a name="monthlyoverview"></a><h2>Expense overview</h2>
            <h5><?php echo date('F, Y', strtotime($year . "-" . $month . "-01")); ?></h5>
            <table>
            	<tr>
                    <th>Date</th>
                    <th>Location</th>
                    <th>Description</th>
                    <th>Category</th>
                    <th>Subcategory</th>
                    <th>Amount</th>
                    <th>Paid by</th>
                </tr>
                <?php echo $expenses; ?>
            </table>
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
