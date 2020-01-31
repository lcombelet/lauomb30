<div class="card center">
  <h3 class="text-center text-logreen font-weight-bold"><i class="fas fa-server"></i> SERVER DETAILS</h3>
  <table class="table table-sm table-striped table-hover">
    <tr><td><b>Name</b></td><td><?php echo $_SERVER['SERVER_NAME']; ?></td></tr>
    <tr><td><b>Software</b></td><td><?php echo $_SERVER['SERVER_SOFTWARE']; ?></td></tr>
    <tr><td><b>Protocol</b></td><td><?php echo $_SERVER['SERVER_PROTOCOL']; ?></td></tr>
    <tr><td><b>Gateway</b></td><td><?php echo $_SERVER['GATEWAY_INTERFACE']; ?></td></tr>
    <tr><td><b>Date</b></td><td><?php echo date('d-M Y'); ?></td></tr>
    <tr><td><b>Time</b></td><td><?php echo date('H:i:s'); ?></td></tr>
  </table>
  <h3 class="text-center text-logreen font-weight-bold"><i class="fas fa-desktop"></i> CLIENT DETAILS</h3>
  <table class="table table-sm table-striped table-hover">
    <tr><td><b>Address</b></td><td><?php echo $_SERVER['REMOTE_ADDR']; ?></td></tr>
    <tr><td><b>Port</b></td><td><?php echo $_SERVER['REMOTE_PORT']; ?></td></tr>
  </table>
</div>
