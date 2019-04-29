<!doctype html>
<html lang="en">
  <head>
    <?php include 'bootstrap_head.php';?>
  </head>

  <body>
    <header>
      <?php include 'bootstrap_header.php';?>
    </header>

    <main role="main">
      <div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel">
        <ol class="carousel-indicators">
          <li data-target="#carouselExampleIndicators" data-slide-to="0" class="active"></li>
          <li data-target="#carouselExampleIndicators" data-slide-to="1"></li>
          <li data-target="#carouselExampleIndicators" data-slide-to="2"></li>
        </ol>
      <div class="carousel-inner">
        <div class="carousel-item active">
          <img class="first-slide" src="/media/carousel1.jpg" alt="First slide">
          <div class="container">
            <div class="carousel-caption text-left">
              <h1>Do not follow the crowd.</h1>
              <p>Cras justo odio, dapibus ac facilisis in, egestas eget quam. Donec id elit non mi porta gravida at eget metus. Nullam id dolor id nibh ultricies vehicula ut id elit.</p>
            </div>
          </div>
        </div>
        <div class="carousel-item">
          <img class="second-slide" src="/media/carousel2.jpg" alt="Second slide">
          <div class="container">
            <div class="carousel-caption text-left">
              <h1>Create your own path.</h1>
              <p>Cras justo odio, dapibus ac facilisis in, egestas eget quam. Donec id elit non mi porta gravida at eget metus. Nullam id dolor id nibh ultricies vehicula ut id elit.</p>
            </div>
          </div>
        </div>
        <div class="carousel-item">
          <img class="third-slide" src="/media/carousel3.jpg" alt="Third slide">
          <div class="container">
            <div class="carousel-caption text-left">
              <h1>One more for good measure.</h1>
              <p>Cras justo odio, dapibus ac facilisis in, egestas eget quam. Donec id elit non mi porta gravida at eget metus. Nullam id dolor id nibh ultricies vehicula ut id elit.</p>
            </div>
          </div>
        </div>
      </div>
      <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="sr-only">Previous</span>
      </a>
      <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="sr-only">Next</span>
      </a>
    </div>

    <div class="container marketing">
      <!-- START THE FEATURETTES -->
      <hr class="featurette-divider">

      <div class="row featurette">
        <h2 class="featurette-heading">Welcome to my website - <span class="text-muted">a note from the nerd</span></h2>
        <p class="lead">You are looking at the landing page of a project that started back in 2000, when there was an urge to make an database accessible over the web. Now, <?php $years = date('Y')-2001; echo $years; ?> years later, we are here. As the subtitle so subtly suggests, this website is a hobby gone full-nerd!</p>
        <p class="lead">Whilst there are a many nice things that I would love to show you, please go ahead and create an account first. I'd like to know who is looking at my stuff <i class="far fa-smile-wink"></i>.</p>
      </div>

      <hr class="featurette-divider">
    </div><!-- /.container -->

    <footer class="container">
      <?php include 'bootstrap_footer.php';?>
    </footer>
  </main>

  <?php include 'bootstrap_scripts.php';?>
  </body>
</html>
