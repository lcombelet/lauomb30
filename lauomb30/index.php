<!DOCTYPE html>
<html>
<head>
  <?php $title= "LauOmb Webserver";
  include 'head.php'; ?>
</head>
<body>

<?php include 'header.php';?>

<div class="row">
  <div class="col-25">
    <?php include 'aboutme.php';?>
  </div>
  <div class="col-75">
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
