<div class="card">
  <h3 class="text-center text-logreen font-weight-bold"><i class="far fa-compass"></i> PHOTO ALBUMS</h3>
  <ul>
    <li><a href="media.php">Home</a></li>
  </ul>
  <?php if(in_array("10", $_SESSION['authorizations'])){ ?>
    <ul>
      <li><a href="portugal.php">Portugal 2019 Diary</a></li>
      <li><a href="photoalbum.php">World tour highlights</a></li>
      <li><a href="/media/images/private/2018_Wedding_RJ.zip">Wedding Rebecca and Jeroen</a></li>
    </ul>
  <?php }
  if(in_array("9", $_SESSION['authorizations'])){ ?>
    <ul>
      <li><a href="/media/images/shared/2019_Ardennen.zip">Ardennen 2019</li>
      <li><a href="/media/images/shared/2018_Rabbit_hill.zip">Rabbit Hill 2018</li>
    </ul>
  <?php } ?>
</div>
