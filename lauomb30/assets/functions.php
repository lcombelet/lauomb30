<?php
function yearDiff($date1, $date2) {
  $date1=date_create($date1);
  $date2=date_create($date2);
  $diff=date_diff($date1,$date2);
  echo $diff->format('%y year');
}
?>

<script>
function updatePlayers() {
    var x = document.getElementById("players").value;
    document.getElementById("functionResult").innerHTML = x;
}
</script>
