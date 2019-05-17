<div class="card">
  <h2><i class="far fa-address-card"></i> ABOUT YOU</h2>
  <h3><i class="far fa-user"></i> <?php echo htmlspecialchars($_SESSION['username']); ?></h3>
  <p class="title"><?php echo htmlspecialchars($_SESSION['useremail']); ?></p>
  <p>Member since: <?php echo htmlspecialchars($_SESSION['usercreated_at']); ?></p>
</div>
