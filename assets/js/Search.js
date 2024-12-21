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

            if (query.length < 3) {
                return;
            }

            clearTimeout(debounceTimeout);

            var debounceTimeout = setTimeout(async () => {
                const results = await this.searchService.search(query);
                this.displayResults(results);
            },500);
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
                return itemDiv;  // Return the item element for later manipulation
            });

            // Use requestAnimationFrame for smoother transition
            let delay = 0;
            itemElements.forEach((itemDiv, index) => {
                // Delay each item sliding in using setTimeout and CSS classes
                setTimeout(() => {
                    itemDiv.querySelector('.result').classList.add('visible');
                }, delay);
                delay += 300; // Increment delay for each item by 300ms
            });

            // results.forEach((item) => {

            //     const itemDiv = document.createElement('div');
            //     itemDiv.className = 'book-item';

            //     itemDiv.innerHTML = `                    
            //          <div class="result" style="opacity: 0; transition: opacity 0.5s;">
            //             <div class="author">${item.author_name}</div>
            //             <div class="title">${item.book_title}</div>
            //         </div>            
            //     `;

            //     this.resultsContainer.appendChild(itemDiv);
            // });

            // const resultsList = this.resultsContainer.querySelectorAll('.result');
            // resultsList.forEach((result, index) => {
            //     setTimeout(() => {
            //         result.style.opacity = 1; // Show the result with a fade-in effect
            //     }, index * 3000); // Delay of 300ms between each item
            // });
        }
    }
}