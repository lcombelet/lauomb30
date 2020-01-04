var playerSlider = document.getElementById("playerRange");
var playerSliderOutput = document.getElementById("playerValue");
var cardSlider = document.getElementById("cardRange");
var cardSliderOutput = document.getElementById("cardValue");

playerSliderOutput.innerHTML = playerSlider.value;
cardSliderOutput.innerHTML = cardSlider.value;

playerSlider.oninput = function() {
  playerSliderOutput.innerHTML = this.value;

  var x = document.getElementById("cardRange").max = Math.floor(51 / this.value);
}

cardSlider.oninput = function() {
  cardSliderOutput.innerHTML = this.value;
}
