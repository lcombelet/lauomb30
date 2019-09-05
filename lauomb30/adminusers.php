<?php
// Initialize the session
session_start();

// If session variable is not set it will redirect to login page
if(!isset($_SESSION['username']) || empty($_SESSION['username']) || !in_array("4", $_SESSION['authorizations'])){
	header("location: login.php");
	exit;
}

// Include config file
require_once 'config.php';
?>

<!DOCTYPE html>
<html>
<head>
	<?php $title = "LauOmb Webserver - Admin portal";
  include 'head.php'; ?>
</head>
<body>

<?php include 'header.php';?>

<div class="row">
<div class="col-25">
<?php include 'adminside.php';?>
</div>
  <div class="col-75">
    <div class="card">
      <h1><i class="fas fa-user"></i> ADMIN PORTAL</h1>
    </div>
		<div class="card">
			<h2>User overview</h2>
			<table>
				<tr>
					<th>Username</th>
					<th>First name</th>
					<th>Last name</th>
					<th>Email</th>
					<th>Joined</th>
					<th>Status</th>
				</tr>
				<tr>
					<td>Text</td>
					<td>Text</td>
					<td>Text</td>
					<td>Email</td>
					<td>Date</td>
					<td>Active/ Deactive</td>
				</tr>
			</table>
		</div>
		<div class="card">
			<h2>Activate/ Deactivate user</h2>
			<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" style="max-width:300px">
        <div class="input-container">
          <i class="fas fa-user icon"></i>
          <input class="input-field" type="text" placeholder="Username" name="username" autocomplete="off" value="<?php echo $username; ?>"><?php echo $username_err; ?>
        </div>
				<div class="input-container">
					<i class="fas fa-balance-scale icon"></i>
					<select class="input-field" name="action">
						<option value="1">Activate</option>
						<option value="2" selected>Deactivate</option>
					</select><?php echo $action_err; ?>
				</div>
        <button type="submit" name="activate">Execute</button>
      </form>
		</div>
		<div class="card">
			<h2>Change permissions</h2>
			<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" style="max-width:300px">
        <div class="input-container">
          <i class="fas fa-user icon"></i>
          <input class="input-field" type="text" placeholder="Username" name="username" autocomplete="off" value="<?php echo $username; ?>"><?php echo $username_err; ?>
        </div>
				<div class="input-container">
					<i class="fas fa-balance-scale icon"></i>
					<select class="input-field" name="roleremove">
						<option value="0">Add role</option>
						<option value="1">Some role</option>
					</select><?php echo $action_err; ?>
				</div>
				<div class="input-container">
					<i class="fas fa-balance-scale icon"></i>
					<select class="input-field" name="roleadd">
						<option value="0">Remove role</option>
						<option value="1">Some role</option>
					</select><?php echo $action_err; ?>
				</div>
        <button type="submit" name="permission">Update</button>
      </form>
		</div>
  </div>
</div>

<?php include 'footer.php';?>

</body>
</html>
