function toggle_liste_dEroulante(id) {
  var liste = document.getElementById(id);
  if (liste.style.display === "none") {
    liste.style.display = "block";
  } else {
    liste.style.display = "none";
  }
}
