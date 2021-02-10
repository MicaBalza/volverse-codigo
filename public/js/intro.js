function mostrar(e) {
  let currentFraseNum = e.parentElement.id;
  let currentFrase = document.querySelector(`#${currentFraseNum}`);
  let nextFraseNum =
    currentFraseNum.slice(0, -1) +
    (parseInt(currentFraseNum[currentFraseNum.length - 1]) + 1);
  let nextFrase = document.querySelector(`#${nextFraseNum}`);
  currentFrase.style.opacity = "0";
  nextFrase.style.opacity = "0.5";
}

function mostrarTodo() {
  let frases = document.querySelectorAll(".frase");
  for (let i = 0; i < frases.length; i++) {
    frases[i].style.opacity = "0.5";
    frases[i].querySelector("span").classList.remove("link");
  }
  let titulo = document.querySelector("#frase-0");
  titulo.style.opacity = "0.5";
  titulo.querySelector("span").classList.remove("link");
  let boton = document.querySelector(".intro-button");
  boton.style.opacity = "1";
}
