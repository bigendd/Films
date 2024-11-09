document.addEventListener("DOMContentLoaded", (event) => {
  const searchInput = document.getElementById("search-input");
  const suggestionsContainer = document.getElementById("suggestions");
  const genreSelect = document.getElementById("genre-select");
  const searchForm = document.getElementById("search-form");

  // Fonction pour aller chercher les suggestions d'autocomplétion quand l'utilisateur tape quelque chose
  function fetchSuggestions() {
    const query = searchInput.value;

    // Si la requête est trop courte (< 2 caractères), on cache les suggestions
    if (query.length < 2) {
      suggestionsContainer.style.display = "none";
      return;
    }

    // Requête au serveur pour obtenir des suggestions (endpoint `/autocomplete`)
    fetch(`https://qcqc.fr/amar/public/autocomplete?query=${query}`)
      .then((response) => response.json()) // Conversion de la réponse en JSON
      .then((suggestions) => {
        // On vide et affiche le conteneur des suggestions
        suggestionsContainer.innerHTML = "";
        suggestionsContainer.style.display = "block";

        // Pour chaque suggestion, on crée un élément div et on l'ajoute au conteneur
        suggestions.forEach((suggestion) => {
          const div = document.createElement("div");
          div.textContent = suggestion.title; // Le titre du film est affiché
          div.classList.add("suggestion-item");
          // Si on clique sur la suggestion, redirection vers la page du film
          div.onclick = () => {
            window.location.href = `https://qcqc.fr/amar/public/film/${suggestion.id}`;
          };
          suggestionsContainer.appendChild(div);
        });
      })
      // Gestion des erreurs si la requête échoue
      .catch((error) => {
        console.error("Erreur lors de la récupération des suggestions :", error);
      });
  }

  // Déclenche la fonction de suggestions à chaque fois qu'on tape dans la barre de recherche
  searchInput.addEventListener("keyup", fetchSuggestions);

  // Quand on clique en dehors des suggestions, elles disparaissent
  document.addEventListener("click", function (event) {
    if (!suggestionsContainer.contains(event.target)) {
      suggestionsContainer.style.display = "none";
    }
  });

  // Empêche l'envoi du formulaire si la barre de recherche est vide, sinon sauvegarde la recherche dans le localStorage
  searchForm.addEventListener("submit", function (event) {
    if (searchInput.value.trim() === "") {
      event.preventDefault(); // Annule l'envoi si la recherche est vide
      window.location.href = "https://qcqc.fr/amar/public/"; // Redirige vers la page d'accueil
    }
    // Sauvegarde la recherche dans le localStorage avant soumission
    localStorage.setItem("searchQuery", searchInput.value);
  });

  // Fonction pour réinitialiser la barre de recherche et cacher les suggestions
  function resetSearchInput() {
    searchInput.value = ""; // Efface la recherche
    suggestionsContainer.innerHTML = ""; // Vide les suggestions
    suggestionsContainer.style.display = "none"; // Cache le conteneur de suggestions
  }

  // Si la page est rechargée depuis le cache (bouton "Précédent"), réinitialise la recherche
  window.addEventListener("pageshow", (event) => {
    if (event.persisted || (window.performance && window.performance.navigation.type === 2)) {
      resetSearchInput();
    }
  });

  // Quand l'utilisateur sélectionne un genre, il est redirigé vers l'URL correspondante
  genreSelect.addEventListener("change", function () {
    if (genreSelect.value) {
      window.location.href = genreSelect.value;
    }
  });
});
