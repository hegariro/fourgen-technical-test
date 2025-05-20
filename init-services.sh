#!/bin/bash

docker-compose -f ./container-compose.yaml --env-file .env up --build -d

docker exec -it backend composer install
docker exec -it backend composer require laravel/sanctum
docker exec -it backend php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
docker exec -it backend composer require blade-ui-kit/blade-icons
docker exec -it backend composer require guzzlehttp/guzzle
docker exec -it backend composer require knuckleswtf/scribe
docker exec -it backend php artisan vendor:publish --tag=scribe-config

docker exec -it backend php artisan key:generate
docker exec -it backend php artisan migrate
docker exec -it backend php artisan db:seed
docker exec -it backend php artisan test
docker exec -it backend php artisan scribe:generate

docker exec -it backend npm install
docker exec -it backend npm run build

docker exec -it backend composer dump-autoload
docker exec -it backend php artisan optimize:clear

chown www-data:www-data -R ./backend/storage 
chown www-data:www-data -R ./backend/bootstrap/cache 

chmod 775 -R ./backend/storage 
chmod 775 -R ./backend/bootstrap/cache 

