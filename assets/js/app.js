require('../css/app.css');

function myFunction() {
  var x = document.getElementById("navbar-toggler-icon");
  if (x.style.display === "none") {
    x.style.display = "block";
  } else {
    x.style.display = "none";
  }
}