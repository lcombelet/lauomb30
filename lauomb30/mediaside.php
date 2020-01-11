<div class="card">
  <h3 class="text-center text-logreen font-weight-bold"><i class="far fa-compass"></i> PHOTO ALBUMS</h3>
  <ul>
    <li><a href="media.php">Home</a></li>
    <?php if(in_array("10", $_SESSION['authorizations'])){ echo "<li><a href=\"portugal.php\">Portugal 2019 Diary</a></li>"; } ?>
  </ul>
</div>
