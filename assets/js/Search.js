class Search {
    constructor(searchInput, searchService, resultsContainer) {
        this.searchInput = searchInput;
        this.searchService = searchService;
        this.resultsContainer = resultsContainer;
        this.init();
    }

    init() {
        let debounceTimeout = null;

        this.searchInput.addEventListener('input', (event) => {
            const query = event.target.value;

            if (query.length < 3) {
                return;
            }

            clearTimeout(debounceTimeout);

            debounceTimeout = setTimeout(() => {
                this.handleSearch(query);
            }, 500);
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
        if (results.length === 0 || results.length === undefined) {
            this.resultsContainer.innerHTML = '<p>No results found</p>';
        } else {

            const itemElements = results.map((item) => {
                const itemDiv = document.createElement('div');
                itemDiv.className = 'book-item';
                itemDiv.innerHTML = `                    
                    <div class="result">
                        <div class="author">${item.author_name}</div>
                        <div class="title">${item.book_title}</div>
                    </div>            
                `;
                this.resultsContainer.appendChild(itemDiv);
                return itemDiv;
            });

            let delay = 0;
            itemElements.forEach((itemDiv, index) => {
                setTimeout(() => {
                    itemDiv.querySelector('.result').classList.add('visible');
                }, delay);
                delay += 300;
            });
        }
    }
}