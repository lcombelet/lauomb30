<div class="card">
  <h3><i class="far fa-calendar-alt"></i> Select period</h3>
  <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
  <select class="input-field" name="period"><?php echo $periods; ?></select><?php echo $period_err; ?>
  <button type="submit" name="submit">Submit</button>
  </form>
</div>
