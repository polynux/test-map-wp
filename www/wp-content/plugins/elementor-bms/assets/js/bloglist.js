class Slide {
  constructor(element) {
    this.element = element;
    this.slide = this.element.querySelector(".slide");
    this.slide.style.width = this.element.offsetWidth + "px";
    this.slide.style.height = this.element.offsetHeight + "px";
    this.slide.style.transform = "translateX(0)";
  }
}

class Slides {
  constructor(widget) {
    this.widget = widget;
    this.count = 0;
    this.time = getTime();
    this.duration = parseInt(widget.dataset.autoplaySpeed);
  }

  createSlides() {}

  next() {
    this.count = translateItems(this.widget, this.count + 1);
    this.time = getTime();
  }
  previous() {
    this.count = translateItems(this.widget, this.count - 1);
    this.time = getTime();
  }
  play() {
    setInterval(() => {
      if (getTime() - this.time < this.duration) return;
      this.next();
    }, this.duration);
  }
}

function setCount(count, nbHidden) {
  if (count > nbHidden) {
    return 0;
  }
  if (count < 0) {
    return nbHidden;
  }
  return count;
}

function translateItems(widget, count) {
  let items = widget.children;
  count = setCount(count, items.length % widget.dataset.columns);

  let itemGap = getComputedStyle(widget).gap;
  let sign = count >= 0 ? "-" : "+";

  for (let item of items) {
    item.style.setProperty("transform", `translateX(calc(-${count * 100}% ${sign} ${itemGap} * ${count}))`);
  }

  return count;
}

function getTime() {
  return new Date().getTime();
}

function carousel(widget) {
  if (widget.children.length <= widget.dataset.columns) return;

  let arrowLeft = widget.nextElementSibling.children[0];
  let arrowRight = widget.nextElementSibling.children[1];
  let count = 0;

  let time = getTime();

  arrowLeft.addEventListener("click", () => {
    count = translateItems(widget, count - 1);
    time = getTime();
  });

  arrowRight.addEventListener("click", () => {
    count = translateItems(widget, count + 1);
    time = getTime();
  });

  if (widget.dataset.autoplay === "true") {
    let duration = parseInt(widget.dataset.autoplaySpeed);
    setInterval(() => {
      if (getTime() - time < duration) return;
      count = translateItems(widget, count + 1);
    }, duration);
  }
}

function render(widget) {
  let type = widget.dataset.type;

  if (type === "carousel") {
    carousel(widget);
  }
}

jQuery(window).on("elementor/frontend/init", () => {
  elementorFrontend.hooks.addAction("frontend/element_ready/bms-bloglist.default", el => {
    render(el[0].children[0].children[0]);
  });
});
