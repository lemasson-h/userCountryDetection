#Requirements

- Have php7.1
- Have mysql
- Have RabbitMq up and running

#Installation

- Run `composer install`
- Run `php bin/console doctrine:database:create` to create DB
- Run `php bin/console doctrine:migrations:migrate` to create schema DB
- Run `php bin/console server:run &` to run a localhost server
- Then open your browser and go to `http://localhost:8000/`

#Run tests

`./vendor/bin/simple-phpunit`

#Localhost IP

Localhost isn't recognised by the service to find your localisation, so you can hardcode your ip on HomeController, line 21 to be able to try properly the route.
