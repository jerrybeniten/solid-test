class SearchService {
    constructor(apiUrl) {
        this.apiUrl = apiUrl;
    }
    
    search(query) {
        return new Promise((resolve, reject) => {
            const xhr = new XMLHttpRequest();
            xhr.open('POST', this.apiUrl, true);
            xhr.setRequestHeader('Content-Type', 'application/json');
            
            // Create the data to be sent in the request body
            const data = JSON.stringify({ query: query });

            xhr.onreadystatechange = () => {
                if (xhr.readyState === XMLHttpRequest.DONE) {
                    if (xhr.status === 200) {
                        resolve(JSON.parse(xhr.responseText));
                    } else {
                        reject('Search failed');
                    }
                }
            };
            
            xhr.send(data);
        });
    }
}
