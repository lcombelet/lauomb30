<!DOCTYPE html>
<html>
<head>
  <?php $title= "LauOmb Webserver";
  include 'head.php'; ?>
</head>
<body>

<?php include 'header.php';?>

<div class="row">
  <div class="leftcolumn">
    <?php include 'aboutme.php';?>
    <?php include 'social.php';?>
  </div>
  <div class="rightcolumn">
    <div class="card">
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
    <div class="card">
      <h2>WELCOME TO MY WEBSITE</h2>
      <h5>A note from the nerd</h5>
      <p>You are looking at the landing page of a project that started way back in 2001 or so, when there was an urge to make an database accessible over the web. Now, <?php $timepassed=date('Y')-2001; echo $timepassed; ?> years later, we are here. As the subtitle so subtly suggests, this website is a hobby gone full-nerd!</p>
      <p>Whilst there are a many nice things that I would love to show you, please go ahead and create an account first. I'd like to know who is looking at my stuff <i class="far fa-smile-wink"></i>.</p>
      <p>After your account has been activated, you can also contact me with feedback or cool stuff that I should also include.</p>
    </div>
  </div>

</div>

<?php include 'footer.php';?>

</body>
</html>
