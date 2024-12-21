class Search {
    constructor(searchInput, searchService, resultsContainer) {
        this.searchInput = searchInput;
        this.searchService = searchService;
        this.resultsContainer = resultsContainer;
        this.init();
    }

    init() {
        this.searchInput.addEventListener('input', (event) => {
            this.handleSearch(event.target.value);
        });
    }

    async handleSearch(query) {
        try {
            const results = await this.searchService.search(query);
            this.displayResults(results);
        } catch (error) {
            console.error(error);
            this.displayResults([]);
        }
    }

    displayResults(results) {
        this.resultsContainer.innerHTML = '';
        if (results.length === 0) {
            this.resultsContainer.innerHTML = '<p>No results found</p>';
        } else {

            results.forEach((item) => {

                const itemDiv = document.createElement('div');
                itemDiv.className = 'book-item';
                
                itemDiv.innerHTML = `
                    <div class="col">
                        <div class="box">
                            <span> ${item.author_name} </span> | 
                            <span> ${item.book_title} </span>
                        </div>
                    </div>
                `;
            
                this.resultsContainer.appendChild(itemDiv);
            });
        }
    }
}