# Book API

Laravel REST API for user authentication and book management.

## Requirements

- PHP 8.3+
- Composer
- PHP `pdo_sqlite` extension

The project uses SQLite.

## Setup

```bash
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate --seed
php artisan serve
```

The API runs at:

```text
http://127.0.0.1:8000
```

## Test user

The seeder creates this user:

```text
Email: test@example.com
Password: password
```

## Run tests

```bash
php artisan test
```

## API ENDPOINTS

- Replace `YOUR_TOKEN` with the token returned by the login or register request. 
- Replace `BOOK_ID` with a book ID.

### Register a user

```bash
curl -X POST http://127.0.0.1:8000/api/register \
  -H "Content-Type: application/json" \
  -d '{"name":"New Reader","email":"reader@example.com","password":"secret-password","password_confirmation":"secret-password"}'
```

### Login

```bash
curl -X POST http://127.0.0.1:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"test@example.com","password":"password"}'
```

### Get the current user

```bash
curl http://127.0.0.1:8000/api/user \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### List books

```bash
curl http://127.0.0.1:8000/api/books \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### Search books

```bash
curl "http://127.0.0.1:8000/api/books?search=clean&per_page=15" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### Create a book

```bash
curl -X POST http://127.0.0.1:8000/api/books \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"name":"Clean Code","isbn":"9780132350884","value":"39.90"}'
```

### Get a book

```bash
curl http://127.0.0.1:8000/api/books/BOOK_ID \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### Update a book

```bash
curl -X PATCH http://127.0.0.1:8000/api/books/BOOK_ID \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"value":"42.00"}'
```

### Delete a book

```bash
curl -X DELETE http://127.0.0.1:8000/api/books/BOOK_ID \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### Logout

```bash
curl -X POST http://127.0.0.1:8000/api/logout \
  -H "Authorization: Bearer YOUR_TOKEN"
```

## Assumptions

- `value` represents the book price or estimated value.
- ISBN values must contain digits only; ISBN-10 and ISBN-13 format validation is not required.
- Each user can only manage their own books.
- SQLite was chosen to keep the assessment easy to run locally.
- For a larger application, full-text search, caching, queues, and a production database could be added.

## Structure

- `BookService`: book business logic, search, and ownership checks.
- Form Requests: request validation.
- `BookResource`: book response formatting.
- Laravel Sanctum: token authentication.

Users can only view and manage their own books. ISBN values must contain digits only, and book values support up to two decimal places.
