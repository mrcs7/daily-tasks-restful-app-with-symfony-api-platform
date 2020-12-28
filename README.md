## What is Done

- Simple Restful APIs to manage daily tasks, it demostrates the usage of Symfony 5,API Platform and Doctrine.

## Running The App with docker
- run: docker-compose up -d .
- run: sudo docker exec -it {BuiltServiceName} composer install
- run to create app database: sudo docker exec -it {BuiltServiceName} php bin/console doctrine:database:create
- run to create app database schema: sudo docker exec -it {BuiltServiceName} php bin/console doctrine:schema:create
- run to create app test database: sudo docker exec -it {BuiltServiceName} php bin/console doctrine:database:create --env=test
- run to create app test database schema: sudo docker exec -it {BuiltServiceName} php bin/console doctrine:schema:create --env=test
- run tests : sudo docker exec -it {BuiltServiceName} php vendor/bin/simple-phpunit
- api documentation : http://127.0.0.1:8080/api/documentation.
- post http://127.0.0.1:8080/api/v1/users are open to create user, it is excluded from authentication.
- post http://127.0.0.1:8080/login with json body email,password to get the token.
- you can fill database with dummy data by : php bin/console doctrine:fixtures:load inside the container.
- users password created from factory is testtest.
