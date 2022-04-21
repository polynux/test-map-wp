import Glide from "@glidejs/glide";

export const initSlider = selector => {
  let autoplay = false;
  if (document.querySelector(selector).dataset.autoplay === "true") {
    autoplay = parseInt(document.querySelector(selector).dataset.autoplaySpeed);
  }
  let gap = document.querySelector(selector).dataset.columnsGap;
  let hoverpause = document.querySelector(selector).dataset.pauseOnHover === "true";
  let glide = new Glide(selector, {
    type: "carousel",
    perView: 3,
    startAt: 0,
    autoplay,
    animationDuration: 800,
    hoverpause,
    gap,
    breakpoints: {
      1024: {
        perView: 2
      },
      768: {
        perView: 1
      }
    }
  });
  glide.mount();
};
