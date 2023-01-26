# CS voting app


## Postman url

- Postman Collection [CS Voting App Postman](https://documenter.getpostman.com/view/11352997/2s8ZDeSduf)

## How To Run

- run `git clone https://github.com/adejorosam/cs-voting-app-be`
- run `composer install` to install all dependencies
- Create a `.env` file copy contents from `.env.example` to `.env` using `cp .env.example .env`then populate it with required values where necessary.
- run `php artisan key:generate` to generate application key for your application
- run `php artisan migrate` to migrate database migration to the database
- run `php artisan db:seed` to populate the database with user and company data
- run `php artisan serve` to start the development server

> **Note:** please ensure that you have MySQL database client installed locally and is running.





