const play = document.querySelector(".play-container");
const audio = document.querySelector("#index-audio");
const video = document.querySelector("video");
const button = document.querySelector(".btn-container");

play.addEventListener("click", playAll);

function playAll() {
  setTimeout(function () {
    play.style.opacity = "0";
    video.currentTime = 0;
    buttonAppear();
  }, 1000);
  setTimeout(function () {
    play.style.visibility = "hidden";
  }, 4000);
  audio.play();
}

function buttonAppear() {
  setTimeout(function () {
    button.style.opacity = 1;
  }, 10000);
}
