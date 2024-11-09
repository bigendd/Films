

document.addEventListener('DOMContentLoaded', () => {
    const menuToggle = document.querySelector('.menu-toggle');
    const navbar = document.querySelector('.navbar');
    const nav = document.querySelector('nav'); // Sélectionne l'élément nav pour détecter les clics en dehors

    menuToggle.addEventListener('click', (event) => {
        event.stopPropagation(); // Empêche le clic sur le bouton burger de se propager
        menuToggle.classList.toggle('open');
        navbar.classList.toggle('open');
    });

    // Ajoute un gestionnaire d'événements pour détecter les clics en dehors de la navbar
    document.addEventListener('click', (event) => {
        if (!nav.contains(event.target)) { // Vérifie si le clic est en dehors de l'élément nav
            menuToggle.classList.remove('open');
            navbar.classList.remove('open');
        }
    });
});

