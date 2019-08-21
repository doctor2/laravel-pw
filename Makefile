#!/bin/sh
GREEN='\033[0;32m'

cp .env.example .env

echo "\n${GREEN}Prepare settings...${NC}"

docker-compose run --rm --no-deps pw-server composer install
docker-compose run --rm --no-deps pw-server php artisan key:generate
docker-compose run --rm --no-deps pw-server php artisan storage:link
docker run --rm -it -v $(pwd):/app -w /app node yarn

echo "\n${GREEN}DONE"

echo "Now run"
docker-compose up -d
docker-compose exec pw-server php artisan migrate --seed
docker-compose exec pw-server php artisan serve
echo "${NC}"

sudo chgrp -R www-data storage bootstrap/cache
sudo chmod -R ug+rwx storage bootstrap/cache

#sudo chown doctor bootstrap/cache -R
#sudo chmod 777 storage -R
