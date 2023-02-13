1)cp .env.dist .env

2)docker-compose run --rm php-fpm composer install

3)docker-compose run --rm php-fpm ./bin/console doctrine:migrations:migrate

4)docker-compose up -d