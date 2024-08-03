document.addEventListener('DOMContentLoaded', (event) => {
    const searchInput = document.getElementById('search-input');
    const searchForm = document.getElementById('search-form');
    const suggestionsContainer = document.getElementById('suggestions');
    const genreSelect = document.getElementById('genre-select');

    // Function to fetch suggestions
    async function fetchSuggestions() {
        const query = searchInput.value;
        if (query.length < 2) {
            suggestionsContainer.style.display = 'none';
            return;
        }

        const response = await fetch(`/autocomplete?query=${query}`);
        const suggestions = await response.json();

        suggestionsContainer.innerHTML = '';
        suggestionsContainer.style.display = 'block';

        suggestions.forEach(suggestion => {
            const div = document.createElement('div');
            div.textContent = suggestion.title;
            div.style.padding = '8px';
            div.style.cursor = 'pointer';
            div.onclick = () => {
                window.location.href = `/film/${suggestion.id}`;
            };
            suggestionsContainer.appendChild(div);
        });
    }

    // Attach fetchSuggestions to input keyup event
    searchInput.addEventListener('keyup', fetchSuggestions);

    // Hide suggestions when clicking outside
    document.addEventListener('click', function (event) {
        if (!suggestionsContainer.contains(event.target)) {
            suggestionsContainer.style.display = 'none';
        }
    });

    // Intercept form submission to handle empty search query
    searchForm.addEventListener('submit', function (event) {
        if (searchInput.value.trim() === '') {
            event.preventDefault();
            window.location.href = '/';
        }
        localStorage.setItem('searchQuery', searchInput.value);
    });

    // Function to reset search input and suggestions
    function resetSearchInput() {
        searchInput.value = '';
        suggestionsContainer.innerHTML = '';
        suggestionsContainer.style.display = 'none';
    }

    // Clear search input and suggestions if coming back via back button
    window.addEventListener('pageshow', (event) => {
        if (event.persisted || (window.performance && window.performance.navigation.type === 2)) { // Reset search input and suggestions to empty
            resetSearchInput();
        }
    });

    // Save search input value to localStorage before navigating away
    window.addEventListener('beforeunload', function () {
        localStorage.removeItem('searchQuery');
        resetSearchInput();
    });

    // Check localStorage for saved search query and clear it
    window.addEventListener('load', function () {
        const savedQuery = localStorage.getItem('searchQuery');
        if (savedQuery) {
            localStorage.removeItem('searchQuery');
            resetSearchInput();
        }
    });

    // Redirect when selecting a genre
    genreSelect.addEventListener('change', function () {
        if (genreSelect.value) {
            window.location.href = genreSelect.value;
        }
    });
});
