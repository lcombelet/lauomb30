<?php
// Initialize the session
session_start();

// If session variable is not set it will redirect to login page
if(!isset($_SESSION['username']) || empty($_SESSION['username']) || !in_array("8", $_SESSION['authorizations'])){
	header("location: login.php");
	exit;
}

// Include config file
require_once 'config.php';

// Define variables and initialize with empty values
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
	<?php include 'mediaside.php';?>
	<?php include 'social.php';?>
	</div>
	  <div class="col-md-9">
			<div class="card">
	      <h1><i class="far fa-images"></i> ALBUM DOWNLOADS</h1>
			</div>
			<?php
			if(in_array("10", $_SESSION['authorizations'])){ // Private albums
			?>
				<div class="card">
		      <h2>Private photo albums</h2>
					<p>Click on the photo to download the album</p>
					<div class="row">
						<div class="col-md-3">
							<a href="/media/images/private/2018_Wedding_RJ.zip" download="2018_Wedding_RJ">
								<div class="imgcontainer">
									<img src="/media/images/private/2018_Wedding_RJ.jpg" style="width:100%">
									<div class="caption">Wedding Rebecca and Jeroen 2018</div>
								</div>
							</a>
					  </div>
						<div class="col-md-3">
							&nbsp
					  </div>
						<div class="col-md-3">
							&nbsp
					  </div>
						<div class="col-md-3">
							&nbsp
					  </div>
					</div>
		    </div>
			<?php
			; } // End of private albums
			if(in_array("9", $_SESSION['authorizations'])){ // Shared albums
			?>
				<div class="card">
					<h2>Shared photo albums</h2>
					<p>Click on the photo to download the album. Contact me for the password to open the archive.</p>
					<div class="row">
						<div class="col-md-3">
							<a href="/media/images/shared/2019_Ardennen.zip" download="2019_Ardennen">
								<div class="imgcontainer">
									<img src="/media/images/shared/2019_Ardennen.jpg" style="width:100%">
									<div class="caption">Ardennen 2019</div>
								</div>
							</a>
					  </div>
					  <div class="col-md-3">
							<a href="/media/images/shared/2018_Rabbit_hill.zip" download="2018_Rabbit_hill">
								<div class="imgcontainer">
									<img src="/media/images/shared/2018_Rabbit_hill.jpg" style="width:100%">
									<div class="caption">Rabbit Hill 2018</div>
								</div>
							</a>
					  </div>
					  <div class="col-md-3">
							<a href="/media/images/shared/2019_Eline_newborn.zip" download="2019_Eline_newborn">
								<div class="imgcontainer">
									<img src="/media/images/shared/2019_Eline_newborn.jpg" style="width:100%">
									<div class="caption">Newborn shoot Eline 2019</div>
								</div>
							</a>
					  </div>
						<div class="col-md-3">
							<a href="/media/images/shared/2019_Daan_newborn.zip" download="2019_Daan_newborn">
								<div class="imgcontainer">
									<img src="/media/images/shared/2019_Daan_newborn.jpg" style="width:100%">
									<div class="caption">Newborn shoot Daan 2019</div>
								</div>
							</a>
						</div>
					</div>
		    </div>
			<?php
			; } // End of shared albums
			?>
	  </div>
	</div>
</div>

<?php include 'footer.php';?>

</body>
</html>
