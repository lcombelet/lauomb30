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
$values = array();
$balance = array();

// Pull balance from db
$sql = "SELECT * FROM `vw_fin_shared_balance`";
if($stmt = $mysqli->query($sql)){
	while($row = mysqli_fetch_array($stmt)) {
		$balance[$row['counterpart']][$row['key']] = $row['amount']; // Multidimensional array required to pull results per counterpart
	}
	// Calculations
	$total_lau = $balance[1][2] - $balance[1][1];
	$total_iri = $balance[2][2] - $balance[2][1];
	$balance_total = $total_lau + $total_iri;
	$balance_split = round(($balance_total / 2),2);
	$diff_lau = $balance_split - $total_lau;
	$diff_iri = $balance_split - $total_iri;
	$diff_total = abs(round(($diff_lau + $diff_iri),2));

	$payment = min(abs($diff_lau),abs($diff_iri));

	if($diff_lau < 0){
		$debtor = "Irina";
		$creditor = "Laurens";
	} else{
		$debtor = "Laurens";
		$creditor = "Irina";
	}
	} else{
	echo "Couldn't fetch balances. Please try again later.";
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
<div class="col-25">
	<?php include 'shrdfinanceside.php';?>
	<?php include 'social.php';?>
</div>
  <div class="col-75">
		<div class="card">
			<h2>Reimbursement</h2>
			<?php echo "<p>" . $debtor; ?> to pay <?php echo $creditor . " " . $payment . ".</p>"; ?>
		</div>
	  <div class="card">
      <h2>Total balance</h2>
      <h5>Since the beginning of time</h5>
      <table>
        <tr>
          <th>Name</th>
          <th>Expenses</th>
          <th>Reimbursements</th>
          <th>Total</th>
          <th>Balance</th>
          <th>Difference</th>
        </tr>
				<tr>
          <td>Laurens</td>
          <td><?php echo $balance[1][2]; ?></td>
          <td><?php echo $balance[1][1]; ?></td>
          <td><?php echo $total_lau; ?></td>
          <td><?php echo $balance_split; ?></td>
          <td><?php echo $diff_lau; ?></td>
        </tr>
        <tr>
          <td>Irina</td>
          <td><?php echo $balance[2][2]; ?></td>
          <td><?php echo $balance[2][1]; ?></td>
          <td><?php echo $total_iri; ?></td>
          <td><?php echo $balance_split; ?></td>
          <td><?php echo $diff_iri; ?></td>
        </tr>
        <tr>
        	<td>Total</td>
          <td><?php echo ($balance[1][2]+$balance[2][2]); ?></td>
          <td><?php echo ($balance[1][1]+$balance[2][1]); ?></td>
          <td><?php echo $balance_total; ?></td>
          <td><?php echo ($balance_split * 2); ?></td>
          <td><?php echo $diff_total; ?></td>
				</tr>
      </table>
    </div>
  </div>
</div>

<?php include 'footer.php';?>

</body>
</html>
