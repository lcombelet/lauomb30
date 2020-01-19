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
$photos = array();

// Scan directory and create array of photo files
$path = 'media/images/private/world_tour_highlights';
$folders = explode("/", $path);
$dir = strtoupper(str_replace("_"," ",end($folders))); // Create title for album basedon folder name

$files = preg_grep('~\.(jpeg|jpg)$~', scandir($path)); // Search for only jpg files

$numberOfPhotos = count($files); // Count files in array

$count = 1;
foreach($files as $key=>$value) {
	$preFix = $afterFix = ""; // Empty variables
	$fullFile = $path . "/" . $value; // Create img src value

	if($count%4 == 1) {
		$preFix = "<div class=\"row\">";
	}

	$galleryLink = "<a class=\"col-sm-6 col-lg-3 padding-xs\" href=\"" . $fullFile . "\" data-toggle=\"lightbox\" data-gallery=\"gallery\"><img class=\"img-fluid\" src=\"" . $fullFile . "\" style=\"width:100%;\"></a>";

	if($count%4 == 0) {
		$afterFix = $afterFix . "</div>";
	}

	$photoLink = $preFix . $galleryLink . $afterFix;
	array_push($photos, $photoLink);

	$count++;
}

// Close div in case not a full row is filled with photos
if($count%4 != 1) {
	$closeDiv = "</div>";
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
				<?php echo implode("",$photos) . $closeDiv; ?>

				<!-- Reference to js file containing scripts around picking up a photo to show in the modal -->
				<script type="text/javascript" src="js/photo_modal.js"></script>

			</div>
	  </div>
	</div>
</div>

<?php include 'footer.php';?>

</body>
</html>
