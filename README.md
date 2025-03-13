# URL Shortener Service

This project is a URL shortener service built with Laravel 12. It allows users to create short url for long URLs and retrieve the original URLs using the short url.

This one is based on Domain Driven Design (DDD) and only modules created here is the Url inside `modules > Url`

## Features

- Create short url for long URLs
- Retrieve original URLs using short url

## Requirements

- PHP 8.2
- Composer

## Installation

1. Clone the repository:
    ```sh
    git clone https://github.com/inquisitive-stha/UrlShortnerLaravel.git
    cd UrlShortnerLaravel
    ```

2. Install PHP dependencies using Composer:
    ```sh
    composer install
    ```

## Configuration

1. Copy the `.env.example` file to `.env` and configure your environment variables:
    ```sh
    cp .env.example .env
    ```

2. Set up the database: (For demo purpose sqlite is used)
    ```sh
    php artisan migrate
    ```

## Running the Application

Start the development server:
```sh
php artisan serve
```

## Running Tests

Run the tests:
```sh
php artisan test
```

## API Endpoints

### Create Short URL

**Endpoint:**
```
POST /api/v1/urls/encode
```

**Request Body:**
```json
{
    "longUrl": "https://www.example.com/some/long/path?param1=value1&param2=value3"
}
```

**Response:**
```json
{
    "data": {
        "type": "url",
        "id": 1,
        "attributes": {
            "shortUrl": "http://short.est/vw5EyD",
            "longUrl": "https://www.example.com/some/long/path?param1=value1&param2=value3",
            "created_at": "2025-03-13T17:47:49.000000Z",
            "updated_at": "2025-03-13T17:47:49.000000Z"
        }
    }
}
```

### Retrieve Original URL

**Endpoint:**
```
POST /api/v1/urls/decode
```

**Request Body:**
```json
{
    "shortUrl": "http://short.est/{shortCode}" // e.g. http://short.est/vw5EyD
}
```

**Response:**
```json
{
    "data": {
        "type": "url",
        "id": 1,
        "attributes": {
            "shortUrl": "http://short.est/vw5EyD",
            "longUrl": "https://www.example.com/some/long/path?param1=value1&param2=value3",
            "created_at": "2025-03-13T17:47:49.000000Z",
            "updated_at": "2025-03-13T17:47:49.000000Z"
        }
    }
}
```

## How to Call the API

### Using `curl`

**Create Short URL:**
```sh
curl -X POST http://localhost:8000/api/v1/urls/encode -H "Content-Type: application/json" -d '{"longUrl": "https://www.example.com/some/long/path?param1=value1&param2=value3"}'
```

**Retrieve Original URL:**
```sh
curl -X POST http://localhost:8000/api/v1/urls/decode -H "Content-Type: application/json" -d '{"shortUrl": "http://short.est/{shortCode}"}'

```

### Using `Postman`

1. **Create Short URL:**
    - Set the request type to `POST`.
    - Set the headers:
        - Key: `Content-Type`, Value: `application/json`.
    - Set the URL to `http://localhost:8000/api/v1/urls/encode`.
      - Set the body to raw JSON:
        ```json
        {
          "longUrl": "https://www.example.com/some/long/path?param1=value1&param2=value3"
        }
        ```

2. **Retrieve Original URL:**
    - Set the request type to `POST`.
    - Set the headers:
        - Key: `Content-Type`, Value: `application/json`.
    - Set the URL to `http://localhost:8000/api/v1/urls/decode`.
    - Set the body to raw JSON:
      ```json
      {
        "shortUrl": "http://short.est/{shortCode}" // e.g. http://short.est/vw5EyD
      }
      ```

## License

This project is licensed under the MIT License.
```
