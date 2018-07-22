<div class="card">
  <h2><i class="far fa-address-card"></i> ABOUT YOU</h2>
<table>
<tr><td>Username:</td><td><?php echo htmlspecialchars($_SESSION['username']); ?></td></tr>
<tr><td>Registered to:</td><td><?php echo htmlspecialchars($_SESSION['useremail']); ?></td></tr>
<tr><td>Member since:</td><td><?php echo htmlspecialchars($_SESSION['usercreated_at']); ?></td></tr>
</table>
</div>
