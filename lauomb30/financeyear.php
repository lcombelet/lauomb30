<div class="card">
  <h3><i class="far fa-calendar-alt"></i> Select year</h3>
  <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
  <select class="input-field" name="year"><?php echo $years; ?></select><?php echo $year_err; ?>
  <button type="submit" name="submit">Submit</button>
  </form>
</div>
