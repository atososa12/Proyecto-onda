const cards = document.querySelectorAll('.card img');
const modal = document.createElement('div');
modal.classList.add('modal');
document.body.appendChild(modal);

cards.forEach(img => {
  img.addEventListener('click', () => {
    modal.innerHTML = `<img src="${img.src}" alt="${img.alt}">`;
    modal.classList.add('show');
  });
});

modal.addEventListener('click', () => {
  modal.classList.remove('show');
});
