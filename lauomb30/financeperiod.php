<div class="card">
  <h3 class="text-center text-logreen font-weight-bold"><i class="far fa-calendar-alt"></i> Select period</h3>
  <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
    <select class="input-field" name="period"><?php echo $periods; ?></select><?php echo $period_err; ?>
  <button class="formbtn" type="submit" name="submit">Submit</button>
  </form>
</div>
