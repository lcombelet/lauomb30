<div class="card center">
  <h2>ABOUT YOU</h2>
  <h3><i class="far fa-address-card"></i> <?php echo htmlspecialchars($_SESSION['username']); ?></h3>
  <p class="title"><i class="far fa-paper-plane"></i> <?php echo htmlspecialchars($_SESSION['useremail']); ?></p>
  <p><i class="fas fa-sign-in-alt"></i> <?php echo htmlspecialchars($_SESSION['usercreated_at']); ?></p>
</div>
