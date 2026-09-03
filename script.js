const hamburger = document.querySelector('#toggle-btn');

hamburger.addEventListener('click', () => {
  document.querySelector('#sidebar').classList.toggle('expand');
});
