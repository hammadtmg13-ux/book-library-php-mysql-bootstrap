# BookNest - Personal Library Tracker

A personal library tracker built with HTML, CSS, JavaScript, Bootstrap, MySQL and PHP. Keep track of the books you own, what you are reading, and what you have finished, all in one place.

## Features
- Add a book with title, author, genre, reading status, rating and page count
- Mark a book as reading or finished with one click
- Rate finished books from 1 to 5 stars
- Library stats: total books, finished, currently reading, average rating and pages read
- Reading progress bar showing finished books out of the total
- Search books by title or author
- Filter by genre and reading status
- Edit and delete books

## Tech stack
- HTML and CSS
- JavaScript
- Bootstrap 5
- PHP with PDO
- MySQL

## Setup
1. Create the database and tables using `database.sql`:
```sql
mysql -u root -p < database.sql
```
2. Update the database details in `config.php` if needed.
3. Copy the project folder to your web server, for example `htdocs` for XAMPP.
4. Open `http://localhost/book-library-php-mysql/` in your browser.

## Database
Database name: `book_library`

Table: `books`
- id: INT, auto increment, primary key
- title: VARCHAR(255)
- author: VARCHAR(255)
- genre: ENUM fiction, non-fiction, science, history, biography, fantasy, self-help, mystery
- status: ENUM to-read, reading, finished
- rating: TINYINT, 1 to 5, can be empty
- pages: INT
- created_at: TIMESTAMP, default current timestamp

## Project structure
- `index.php` - main page, stats, progress bar, search, filters and book list
- `config.php` - MySQL connection with PDO
- `add_book.php` - handles new books
- `edit_book.php` - handles editing a book
- `delete_book.php` - deletes a book
- `update_status.php` - changes a book's reading status
- `database.sql` - database and table setup with sample books
- `assets/css/style.css` - custom styles
- `assets/js/script.js` - delete confirmation and form helpers

## Author
Built by Hammad
