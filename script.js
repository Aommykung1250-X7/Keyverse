function scrollRight() {
  document.getElementById('scrollContainer').scrollBy({
    left: 300,
    behavior: 'smooth'
  });
}

function scrollLeft() {
  document.getElementById('scrollContainer').scrollBy({
    left: -300,
    behavior: 'smooth'
  });
}