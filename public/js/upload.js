//jQuery time
"use strict";

var current_fs, next_fs, previous_fs; //fieldsets
var left, opacity, scale; //fieldset properties which we will animate
var animating; //flag to prevent quick multi-click glitches

const element = document.getElementById("date-input");
element.valueAsNumber =
  Date.now() - new Date().getTimezoneOffset() * 60000 + 5184000 * 1000;

$(".next").click(function () {
  if (animating) return false;
  animating = true;

  current_fs = $(this).parent();
  next_fs = $(this).parent().next();

  //activate next step on progressbar using the index of next_fs
  $("#progressbar li").eq($("fieldset").index(next_fs)).addClass("active");

  //show the next fieldset
  next_fs.show();
  //hide the current fieldset with style
  current_fs.animate(
    { opacity: 0 },
    {
      step: function (now, mx) {
        //as the opacity of current_fs reduces to 0 - stored in "now"
        //1. scale current_fs down to 80%
        scale = 1 - (1 - now) * 0.2;
        //2. bring next_fs from the right(50%)
        left = now * 50 + "%";
        //3. increase opacity of next_fs to 1 as it moves in
        opacity = 1 - now;
        current_fs.css({ transform: "scale(" + scale + ")" });
        next_fs.css({ left: left, opacity: opacity });
      },
      duration: 500,
      complete: function () {
        current_fs.hide();
        animating = false;
      },
      //this comes from the custom easing plugin
      easing: "easeOutQuint",
    }
  );
});

$(".previous").click(function () {
  if (animating) return false;
  animating = true;

  current_fs = $(this).parent();
  previous_fs = $(this).parent().prev();

  //de-activate current step on progressbar
  $("#progressbar li")
    .eq($("fieldset").index(current_fs))
    .removeClass("active");

  //show the previous fieldset
  previous_fs.show();
  //hide the current fieldset with style
  current_fs.animate(
    { opacity: 0 },
    {
      step: function (now, mx) {
        //as the opacity of current_fs reduces to 0 - stored in "now"
        //1. scale previous_fs from 80% to 100%
        scale = 0.8 + (1 - now) * 0.2;
        //2. take current_fs to the right(50%) - from 0%
        left = (1 - now) * 50 + "%";
        //3. increase opacity of previous_fs to 1 as it moves in
        opacity = 1 - now;
        current_fs.css({ left: left });
        previous_fs.css({
          transform: "scale(" + scale + ")",
          opacity: opacity,
        });
      },
      duration: 500,
      complete: function () {
        current_fs.hide();
        animating = false;
      },
      //this comes from the custom easing plugin
      easing: "easeOutQuint",
    }
  );
});

$(".submit").click(function () {
  return false;
});

(function ($, window, document, undefined) {
  $("#input-file").each(function () {
    var $input = $(this),
      $label = $input.next("label"),
      labelVal = $label.html();

    $input.on("change", function (e) {
      var fileName = "";

      if (this.files && this.files.length > 1)
        fileName = (this.getAttribute("data-multiple-caption") || "").replace(
          "{count}",
          this.files.length
        );
      else if (e.target.value) fileName = e.target.value.split("\\").pop();

      if (fileName) $label.find("span").html(fileName);
      else $label.html(labelVal);

      console.log(fileName);
    });

    // Firefox bug fix
    $input
      .on("focus", function () {
        $input.addClass("has-focus");
      })
      .on("blur", function () {
        $input.removeClass("has-focus");
      });
  });
})(jQuery, window, document);

///////////////////////////// File Logic

const optionButton = document.querySelector("#option-button");
const audioBox = document.querySelector(".audio-box");
const videoBox = document.querySelector(".video-box");
const textBox = document.querySelector(".text-box");
const imageBox = document.querySelector(".image-box");
const audioInput = document.querySelector("#audio-file");
const videoInput = document.querySelector("#video-file");
const imageInput = document.querySelector("#image-file");
var option;

optionButton.addEventListener("click", mostrarOpcion);

function mostrarOpcion() {
  var ele = document.getElementsByName("radio");

  for (let i = 0; i < ele.length; i++) {
    if (ele[i].checked) {
      option = ele[i].value;
      hideInput();
    }
  }
}

function hideInput() {
  if (option === "1") {
    audioBox.style.display = "block";
  } else if (option === "2") {
    videoBox.style.display = "block";
  } else if (option === "3") {
    textBox.style.display = "block";
  } else {
    imageBox.style.display = "block";
  }
}

audioInput.addEventListener("change", validateAudio);
videoInput.addEventListener("change", validateVideo);
imageInput.addEventListener("change", validateImage);

function validateAudio() {
  let size = audioInput.files[0].size;
  if (size > 10000000) {
    document.querySelector("#error-audio").style.color = "red";
  } else {
    document.querySelector("#audio-label").innerHTML = audioInput.files[0].name;
  }
}

function validateVideo() {
  let size = videoInput.files[0].size;
  if (size > 25000000) {
    document.querySelector("#error-video").style.color = "red";
  } else {
    document.querySelector("#video-label").innerHTML = videoInput.files[0].name;
  }
}

function validateImage() {
  let size = imageInput.files[0].size;
  if (size > 5000000) {
    document.querySelector("#error-image").style.color = "red";
  } else {
    document.querySelector("#image-label").innerHTML = imageInput.files[0].name;
  }
}
