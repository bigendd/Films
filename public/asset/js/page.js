document.addEventListener("DOMContentLoaded", (event) => {

  const searchInput = document.getElementById("search-input");
  const suggestionsContainer = document.getElementById("suggestions");

  // Fonction pour récupérer les suggestions
  function fetchSuggestions() {
    const query = searchInput.value; 
    if (query.length < 2) {
      // Vérifie si la longueur de la requête est inférieure à 2 caractères.
      suggestionsContainer.style.display = "none"; 
      return; 
    }

    // Utilise fetch et des promesses pour obtenir les suggestions d'auto-complétion
    fetch(`/autocomplete?query=${query}`)
      .then((response) => response.json()) // Convertit la réponse en JSON
      .then((suggestions) => {
        suggestionsContainer.innerHTML = ""; // Vide le conteneur des suggestions avant d'ajouter de nouvelles suggestions.
        suggestionsContainer.style.display = "block"; 
      
        suggestions.forEach((suggestion) => {
          const div = document.createElement("div"); 
          div.textContent = suggestion.title; // Définit le texte du div comme étant le titre de la suggestion.
          div.classList.add("suggestion-item"); 
          div.onclick = () => {
            window.location.href = `/film/${suggestion.id}`; // Redirige vers la page de détail du film.
          };
          suggestionsContainer.appendChild(div); // Ajoute le div de suggestion au conteneur des suggestions.
        });
      })
      .catch((error) => {
        console.error("Erreur lors de la récupération des suggestions :", error);
      });
  }

  // Attache fetchSuggestions à l'événement keyup de l'input de recherche
  searchInput.addEventListener("keyup", fetchSuggestions);

  // Cache les suggestions lorsque l'utilisateur clique à l'extérieur du conteneur des suggestions
  document.addEventListener("click", function (event) {
    if (!suggestionsContainer.contains(event.target)) {
      // Vérifie si le conteneur des suggestions ne contient pas l'élément cliqué.
      suggestionsContainer.style.display = "none"; // Cache le conteneur des suggestions.
    }
  });

  // Fonction pour réinitialiser le champ de recherche et les suggestions
  function resetSearchInput() {
    searchInput.value = ""; // Réinitialise la valeur du champ de recherche.
    suggestionsContainer.innerHTML = ""; // Vide le conteneur des suggestions.
    suggestionsContainer.style.display = "none"; // Cache le conteneur des suggestions.
  }

  // Efface le champ de recherche et les suggestions lorsque l'utilisateur revient à la page via le bouton "Précédent"
  window.addEventListener("pageshow", (event) => {
    if (
      event.persisted ||
      (window.performance && window.performance.navigation.type === 2)
    ) {
      // Vérifie si la page est chargée à partir du cache (via l'utilisation du bouton "Précédent").
      resetSearchInput(); 
    }
  });
});
