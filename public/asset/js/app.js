// Sélectionne le bouton "Retour en haut"
const backToTopBtn = document.getElementById("backToTopBtn");

// Affiche ou masque le bouton en fonction du défilement
window.onscroll = function() {
    if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
        backToTopBtn.classList.add("show");
    } else {
        backToTopBtn.classList.remove("show");
    }
};

// Ramène la page en haut lorsque le bouton est cliqué
backToTopBtn.addEventListener("click", function() {
    document.documentElement.scrollTop = 0;
});
