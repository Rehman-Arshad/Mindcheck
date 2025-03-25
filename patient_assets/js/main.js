document.addEventListener("DOMContentLoaded", function () {
  let header = document.querySelector("#header");

  function toggleHeaderScrolled() {
    if (window.scrollY > 50) {
      header.classList.add("header-scrolled");
    } else {
      header.classList.remove("header-scrolled");
    }
  }

  // Run on scroll
  window.addEventListener("scroll", toggleHeaderScrolled);

  // Run on page load in case user reloads at scrolled position
  toggleHeaderScrolled();
});
document.addEventListener("DOMContentLoaded", function () {
  let header = document.querySelector("#header");

  function toggleHeaderScrolled() {
    if (window.scrollY > 50) {
      header.classList.add("header-scrolled");
    } else {
      header.classList.remove("header-scrolled");
    }
  }

  // Run on scroll
  window.addEventListener("scroll", toggleHeaderScrolled);

  // Run on page load in case user reloads at scrolled position
  toggleHeaderScrolled();
});

