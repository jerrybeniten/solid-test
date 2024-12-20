CREATE TABLE authors (
    id SERIAL PRIMARY KEY, -- Unique ID for each author
    name VARCHAR(100), -- First name of the author
    CONSTRAINT unique_name UNIQUE (name)
);

CREATE TABLE books (
    id SERIAL PRIMARY KEY,      -- Unique ID for each book
    title VARCHAR(255),              -- Title of the book
    author_id INT,                   -- Foreign key referencing the authors table    
    CONSTRAINT unique_title_author UNIQUE (title, author_id)
)