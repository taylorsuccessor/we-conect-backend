# Laravel 11 Project

This is a sample Laravel 11 project. It provides a robust framework for web application development with a focus on simplicity and speed. This README will guide you through the setup and usage of this project.

## Table of Contents

- [Links to API's swagger and frontend](#Links)
- [Requirements](#requirements)
- [Installation](#installation)
- [Running the Project](#running-the-project)
  - [With Docker Compose](#with-docker-compose)
  - [With Nginx](#with-nginx)
  - [Directly on Localhost](#directly-on-localhost)
- [Database Setup](#database-setup)
  - [Migration](#migration)
  - [Seeding](#seeding)
- [Running Tests](#running-tests)
- [Algorithm Complexity - Fibonacci](#Algorithm)
- [PL/SQL](#PL/SQL)
- [Communication](#Communication)
- [Contributing](#contributing)
- [License](#license)

## Links

- Front-end and final output.

  ```
  https://frontend-tech-test.livaatverse.com

- API's and Swagger documentations.

  ```
  http://tech-test-server.livaatverse.com/api/documentation


- Front-end Repository on github

  ```
  https://tech-test-server.livaatverse.com/api/documentation


- Lambda function using vapor to deploy Laravel

  ```
  https://tech-test.livaatverse.com/api/documentation


## Requirements

- PHP >= 8.1
- Composer
- MySQL or PostgreSQL
- Docker (optional)
- Nginx (optional)

## Installation

1. Clone the repository:

   ```bash
   git clone https://github.com/your-username/laravel11-project.git
   cd laravel11-project
2. Install dependencies:

   ```bash
   composer install
3. Copy the `.env.example` file to `.env` and configure your environment variables:

   ```bash
   cp .env.example .env
   php artisan key:generate

## Running the Project

### With Docker Compose

1. Ensure Docker and Docker Compose are installed on your system.
2. Build and run the containers:

   ```bash
   make docker-build

also check

    make docker-migrate
    make docker-seed
    make docker-fresh-database
    make docker-enter

The application will be accessible at `http://localhost`.

### With Nginx

1. Ensure Nginx is installed on your system.
2. Configure Nginx with the following server block:

   ```nginx
   server {
       listen 80;
       server_name your-domain.com;

       root /path/to/laravel11-project/public;

       index index.php index.html;

       location / {
           try_files $uri $uri/ /index.php?$query_string;
       }

       location ~ \.php$ {
           fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
           fastcgi_index index.php;
           include fastcgi_params;
           fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
           fastcgi_param PATH_INFO $fastcgi_path_info;
       }

       location ~ /\.ht {
           deny all;
       }
   }
#### Restart Nginx:

    ```bash
    sudo systemctl restart nginx

The application will be accessible at `http://your-domain.com`.

### Directly on Localhost

1. Serve the application using the built-in Laravel server:

   ```bash
   php artisan serve

The application will be accessible at http://localhost:8000.

## Database Setup
### Migration
To run the database migrations, execute:

bash

    make migrate

### Seeding
To seed the database with sample data, execute:

    make seed

## Running Tests
To run the tests, execute:

    make test

## Algorithm
This algorithm time complexity is O(n) because it loop from 2 to n we can use caching if we need to make an API to get this number.
Where we can save the number with it's fibonacci sequance the max we usually get.

like we have max_fibonacci=[0, 1, 1, 2, 3 ... max] and dictinarry fibonacci_indexes shows the number with the index in max_fibonacci.
then we can cut it,
max_fibonacci[0: fibonacci_indexes[number]]
am using python for simpler syntax.


    function fibonacci($n) {
        $fib = [0, 1];
        for ($i = 2; $i <= $n; $i++) {
            $fib[] = $fib[$i - 1] + $fib[$i - 2];
        }
        return $fib;
    }


## PL/SQL

I don't know if we should implement this in laravel or just add it as script.


    CREATE OR REPLACE PROCEDURE GetArticleById(
        article_id IN NUMBER,
        article_title OUT VARCHAR2,
        article_content OUT CLOB
    ) IS
    BEGIN
        SELECT title, content
        INTO article_title, article_content
        FROM articles
        WHERE id = article_id;
    EXCEPTION
        WHEN NO_DATA_FOUND THEN
            article_title := NULL;
            article_content := NULL;
    END GetArticleById;
    /

## Communication

I was wondring if I should make things simple but I added things like:

  - Authontication and Authorization.
  - Github Pipeline and Action for front-end and back-end.
  - Using React for frontend.
  - Deploy frontend to S3.
  - Add Swagger.
  - Add test for all API's.
  - Docker and docker-compose.
  - Using Vapor to deploy to lambda.
  - Subdomain for 1- frontend 2- API server 3- lambda functions 4- images.

## swagger generate and update
To run the tests, execute:

    make swagger-generate

## Contributing
Thank you for considering contributing to this project! Please fork the repository and create a pull request with your changes.
