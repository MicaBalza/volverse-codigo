const modal = document.querySelector(".modal-container");
const capsuleFile = document.querySelector("#file");
const fecha = document.querySelector("#open-date");

function mostrarCapsula(e) {
  modal.style.visibility = "visible";
  if (e.id === "JB") {
    capsuleFile.innerHTML = '<img src="assets/img/Julia.jpeg">';
    fecha.innerHTML = "06/07/1994";
  } else if (e.id === "ET") {
    capsuleFile.innerHTML = '<img src="assets/img/Euge.jpg">';
    fecha.innerHTML = "14/02/1995";
  } else if (e.id === "JM") {
    capsuleFile.innerHTML = '<img src="assets/img/Jules.jpg">';
    fecha.innerHTML = "05/11/1994";
  } else if (e.id === "VB") {
    capsuleFile.innerHTML = '<video controls src="assets/videos/Vanu.mp4">';
    fecha.innerHTML = "05/02/1997";
  } else if (e.id === "RV") {
    capsuleFile.innerHTML = '<img src="assets/img/Ro.jpg">';
    fecha.innerHTML = "20/09/1995";
  } else if (e.id === "RDB") {
    capsuleFile.innerHTML = '<img src="assets/img/Rosi.jpg">';
    fecha.innerHTML = "12/09/1996";
  }
}

function cerrarCapsula() {
  modal.style.visibility = "hidden";
}
