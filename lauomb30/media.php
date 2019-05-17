<?php
// Initialize the session
session_start();

// If session variable is not set it will redirect to login page
if(!isset($_SESSION['username']) || empty($_SESSION['username'] || in_array("8", $_SESSION['authorizations']))){
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
	<?php $title = "LauOmb Webserver - Media";
  include 'head.php'; ?>
</head>
<body>

<?php include 'header.php';?>

<div class="row">
<div class="col-25">
<?php include 'mediaside.php';?>
</div>
  <div class="col-75">
    <div class="card">
      <h2>Let's bring some pictures into the show</h2>
      <h5>Because reading is only so so..</h5>
			<div class="slideshow-container">

        <!-- Full-width images with number and caption text -->
        <div class="mySlides fade">
          <img src="/media/images/carousel/carousel1.jpg" style="width:100%">
          <div class="text">China</div>
        </div>

        <div class="mySlides fade">
          <img src="/media/images/carousel/carousel2.jpg" style="width:100%">
          <div class="text">Australia</div>
        </div>

        <div class="mySlides fade">
          <img src="/media/images/carousel/carousel3.jpg" style="width:100%">
          <div class="text">New Zealand</div>
        </div>

        <!-- Next and previous buttons -->
        <a class="prev" onclick="plusSlides(-1)">&#10094;</a>
        <a class="next" onclick="plusSlides(1)">&#10095;</a>
      </div>
      <br>

      <!-- The dots/circles -->
      <div style="text-align:center">
        <span class="dot" onclick="currentSlide(1)"></span>
        <span class="dot" onclick="currentSlide(2)"></span>
        <span class="dot" onclick="currentSlide(3)"></span>
      </div>

      <script>
      var slideIndex = 0;
      showSlides();

      function showSlides() {
        var i;
        var slides = document.getElementsByClassName("mySlides");
        var dots = document.getElementsByClassName("dot");
        for (i = 0; i < slides.length; i++) {
          slides[i].style.display = "none";
        }
        slideIndex++;
        if (slideIndex > slides.length) {slideIndex = 1}
        for (i = 0; i < dots.length; i++) {
          dots[i].className = dots[i].className.replace(" active", "");
        }
        slides[slideIndex-1].style.display = "block";
        dots[slideIndex-1].className += " active";
        setTimeout(showSlides, 5000); // Change image every 2 seconds
      }
      </script>
    </div>
  </div>
</div>

<?php include 'footer.php';?>

</body>
</html>
