document.addEventListener("DOMContentLoaded", function () {
  for (let text of document.getElementsByClassName("bms-archive-description")) {
    if (!text.innerText.includes("[masquer]")) return;

    let textToHide = text.innerHTML.split("[masquer]")[1];
    text.innerHTML = text.innerHTML.split("[masquer]")[0];
    text.innerHTML += `<span class="bms-category-readmore">${textToHide}</span>`;
    text.innerHTML += `<div class="bms-category-readmore-btn">Lire plus</div>`;
  }

  let readMoreList = document.querySelectorAll(".bms-category-readmore-btn");
  for (let readMore of readMoreList) {
    readMore.addEventListener("click", function () {
      readMore.previousElementSibling.classList.toggle("show");

      if (readMore.previousElementSibling.classList.contains("show")) {
        readMore.innerHTML = "Lire moins";
      } else {
        readMore.innerHTML = "Lire plus";
      }
    });
  }
});
