## What is Done

- Simple Restful APIs to manage daily tasks, it demostrates the usage of Symfony 5,API Platform and Doctrine.

## Running The App
- clone the repo.
- run: composer install
- edit .env file and .env.test with your database configuration for app database and test base.   
- run to create app database: symfony console doctrine:database:create
- run to create app database schema: symfony console doctrine:schema:create
- run to create app test database: symfony console doctrine:database:create --env=test
- run to create app test database schema: symfony console doctrine:schema:create --env=test
- run to create jwt keys and configuration and ensure your .env and .evv.test both have your jwt keys.
- run tests : php vendor/bin/simple-phpunit
- run app using symfony built in server through: symfony serve or whatever you like.
- api documentation : http://127.0.0.1:8000/api/documentation
