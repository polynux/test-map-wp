var $j = jQuery.noConflict();
(function ($j) {
  $j(document).ready(function () {
    if (checkiOSVersion() <= 10 || checkSafariVersion() <= 10 || checkChromeVersion() <= 59) {
      menu();
      sliders();
    }
  });
})(jQuery);

function checkiOSVersion() {
  var agent = navigator.userAgent,
    start = agent.indexOf("OS ");
  if ((agent.indexOf("iPhone") > -1 || agent.indexOf("iPad") > -1) && start > -1) {
    return agent.substr(start + 3, 2).replace("_", ".");
  }
  return 100;
}

function checkSafariVersion() {
  if (navigator.userAgent.split("Version/")[1] !== undefined) {
    return navigator.userAgent.split("Version/")[1].substring(0, 2);
  }
  return 100;
}

function checkChromeVersion() {
  if (navigator.userAgent.split("Chrome/")[1] !== undefined) {
    return navigator.userAgent.split("Chrome/")[1].substring(0, 3).replace("_", ".");
  }
  return 1000;
}

function menu() {
  var menuBtn = $j(".elementor-menu-toggle");

  menuBtn.each(function () {
    var btn = $j(this);
    var nav = $j(this).next();
    var right = btn.offset().left + btn.outerWidth() - window.outerWidth;
    if (window.outerWidth === 0) {
      right = btn.offset().left + btn.outerWidth() - screen.width;
    }

    var style = {
      width: "100vw",
      right: right + "px",
      top: btn.outerHeight() + "px"
    };

    nav.css(style);

    btn.off("click");
    btn.on("click", function () {
      btn.toggleClass("elementor-active");
    });

    window.addEventListener("resize", function () {
      var right = btn.offset().left + btn.outerWidth() - window.outerWidth;
      if (window.outerWidth === 0) {
        right = btn.offset().left + btn.outerWidth() - screen.width;
      }
      nav.css({ right: right + "px", top: btn.outerHeight() + "px" });
    });
  });
}

function sliders() {
  var sliderContainer = document.querySelectorAll(".elementor-slides-wrapper");

  for (var j = 0; j < sliderContainer.length; j++) {
    var slider = sliderContainer[j];
    var slides = slider.children[0].children,
      prevBtn = slider.children[2],
      nextBtn = slider.children[3];

    for (var k = 0; k < slides.length; k++) {
      var slide = slides[k];
      slide.style.setProperty("transform", "translateX(0%)");
      slide.style.setProperty("transition", "transform 0.6s ease-in-out 0s");
      slide.style.setProperty("-webkit-transform", "translateX(0%)");
      slide.style.setProperty("-webkit-transition", "-webkit-transform 0.6s ease-in-out 0s");
    }

    var counter = 0;

    prevBtn.addEventListener("click", function () {
      counter = translateSlide(slides, counter, "left");
    });

    nextBtn.addEventListener("click", function () {
      counter = translateSlide(slides, counter, "right");
    });

    setInterval(function () {
      counter = translateSlide(slides, counter, "right");
    }, 5000);
  }

  function translateSlide(slides, counter, side) {
    if (side === "left") {
      if (counter === 0) {
        counter = slides.length - 1;
      } else {
        counter--;
      }
    } else {
      if (counter === slides.length - 1) {
        counter = 0;
      } else {
        counter++;
      }
    }
    for (var l = 0; l < slides.length; l++) {
      var slide = slides[l];
      slide.style.setProperty("transform", "translateX(-" + counter + "00%)");
      slide.style.setProperty("-webkit-transform", "translateX(-" + counter + "00%)");
    }

    return counter;
  }
}
