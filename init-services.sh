#!/bin/bash

docker-compose -f ./container-compose.yaml --env-file .env up --build -d

docker exec -it backend npm install
docker exec -it backend npm run build

docker exec -it backend composer require laravel/sanctum
docker exec -it backend php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
docker exec -it backend composer require blade-ui-kit/blade-icons

docker exec -it backend php artisan migrate
docker exec -it backend php artisan test

