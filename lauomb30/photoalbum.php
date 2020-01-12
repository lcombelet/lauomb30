<?php
// Initialize the session
session_start();

// If session variable is not set it will redirect to login page
if(!isset($_SESSION['username']) || empty($_SESSION['username']) || !in_array("10", $_SESSION['authorizations'])){
	header("location: login.php");
	exit;
}

// Include config file
require_once 'config.php';

// Init variables
$folders = array();
$column1 = array();
$column2 = array();
$column3 = array();
$column4 = array();
$modalData = array();

// Scan directory and create array of photo files
$path = 'media/images/private/test';
$folders = explode("/", $path);
$dir = strtoupper(end($folders));

// Search for only jpg files
$files1 = preg_grep('~\.(jpeg|jpg)$~', scandir($path));

// Count files in array
$numberOfPhotos = count($files1);

// Create four arrays and add photos
$i = 1;
foreach($files1 as $key=>$value) {
	$arrayIndex = ($key-1) % 4; // Correct for array keys (first two elements are non-file, array starts at 0 so only deduct by 1)

	if($arrayIndex === 0) {
		$arrayIndex = 4;
	}

	$fullFile = $path . "/" . $value;

	$photoLink = "<a href=\"#modalPhoto\" data-toggle=\"modal\" data-target=\"#modalPhoto\" alt=\"" . $fullFile . "\" onclick=\"currentSlide(" . $i . ")\"><img src=\"" . $fullFile . "\" style=\"width:100%; margin-top:10px\"></a>";

	array_push(${'column'.$arrayIndex}, $photoLink);

	$modalLink = "<div class=\"mySlides\"><img src=\"" . $fullFile . "\" style=\"width:100%\"></div>";
	array_push($modalData, $modalLink);

	$i++;
} ?>

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
	      <h2><i class="far fa-images"></i> PHOTO ALBUM</h2>
			</div>
			<div class="card">
				<h3><?php echo $dir; ?></h3>
				<h5><?php echo $numberOfPhotos . " photos in this album"; ?></h5>
				<div class="row">
					<div class="col-md-6 col-lg-3">
						<?php echo implode("",$column1); ?>
					</div>
					<div class="col-md-6 col-lg-3">
						<?php echo implode("",$column2); ?>
					</div>
					<div class="col-md-6 col-lg-3">
						<?php echo implode("",$column3); ?>
					</div>
					<div class="col-md-6 col-lg-3">
						<?php echo implode("",$column4); ?>
					</div>
				</div>

				<!-- The Modal -->
				<div class="modal fade" id="modalPhoto">
					<div class="modal-dialog modal-xl">
						<div class="modal-content">

							<!-- Modal Header -->
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal">&times;</button>
							</div>

							<!-- Modal body -->
							<div class="modal-body">
								<?php echo implode("",$modalData); ?>
							</div>
						</div>
					</div>
				</div>

				<!-- Reference to js file containing scripts around picking up a photo to show in the modal -->
				<script type="text/javascript" src="js/photo_modal.js"></script>

			</div>
	  </div>
	</div>
</div>

<?php include 'footer.php';?>

</body>
</html>
