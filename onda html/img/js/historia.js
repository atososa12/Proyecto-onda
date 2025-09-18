const inner = document.querySelector(".carrusel-inner");
const imgs = document.querySelectorAll(".carrusel-inner img");
let index = 0;

document.querySelector(".next").addEventListener("click", () => {
  index = (index + 1) % imgs.length;
  inner.style.transform = `translateX(-${index * 100}%)`;
});

document.querySelector(".prev").addEventListener("click", () => {
  index = (index - 1 + imgs.length) % imgs.length;
  inner.style.transform = `translateX(-${index * 100}%)`;
});