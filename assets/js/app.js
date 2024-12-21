document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('search-input');
    const resultsContainer = document.getElementById('search-results');
    const searchService = new SearchService('http://localhost/api/v1/search/');
    new Search(searchInput, searchService, resultsContainer);
});