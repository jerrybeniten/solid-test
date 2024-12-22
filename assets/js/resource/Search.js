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
            }, 300);
        });
    }

    async handleSearch(query) {
        this.resultsContainer.innerHTML = '';
        if (query.length > 255) {
            const itemDiv = document.createElement('div');
            itemDiv.className = 'book-item';
            itemDiv.innerHTML = `                    
                    <div class="error-message" role="listitem" aria-labelledby="book-title">
                        <div aria-label="Author Name:">
                                Maximum allowed character is only 255
                        </div>
                    </div>`;
            this.resultsContainer.appendChild(itemDiv);
            itemDiv.querySelector('.result').classList.add('visible');
            return false;
        }

        try {
            const results = await this.searchService.search(query);
            this.displayResults(results, query);

        } catch (error) {
            console.error(error);
            this.displayResults([]);
        }
    }

    displayResults(results, query) {
        this.resultsContainer.innerHTML = '';         

        if (results.length === 0 || results.length === undefined) {
            const itemDiv = document.createElement('div');
            itemDiv.className = 'book-item';
            itemDiv.innerHTML = `                    
                    <div class="error-message" role="listitem" aria-labelledby="book-title">
                        <div aria-label="Author Name:">
                                No results for search term: ${query}
                        </div>
                    </div>`;
            this.resultsContainer.appendChild(itemDiv);          

        } else {

            const itemElements = results.map((item) => {
                const itemDiv = document.createElement('div');
                itemDiv.className = 'book-item';
                itemDiv.innerHTML = `                    
                    <div class="result" role="listitem" aria-labelledby="book-title">
                        <div class="author" aria-label="Author Name: ${item.author_name}">
                            ${item.author_name}
                        </div>
                        <div class="title" id="book-title" aria-label="Book Title: ${item.book_title}">
                            ${item.book_title ?? '(no books found)'}
                        </div>
                    </div>`;
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