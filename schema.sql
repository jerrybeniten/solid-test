-- Create the 'authors' table
CREATE TABLE authors (
    id SERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create the 'books' table
CREATE TABLE books (
    id SERIAL PRIMARY KEY,
    title VARCHAR(255), 
    author_id INT NOT NULL,    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP    
);

-- Ensure a unique combination of book title and author_id (an author cannot have duplicate book titles)
CREATE UNIQUE INDEX unique_book_author ON books (title, author_id);