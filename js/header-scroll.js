window.addEventListener('scroll', function() {
    const header = document.querySelector('.menu-container');
    if (window.scrollY > 50) {
        header.classList.add('header-scrolled');
    } else {
        header.classList.remove('header-scrolled');
    }
});
