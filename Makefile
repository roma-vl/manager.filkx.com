up: docker-up
init: docker-down-clear docker-pull docker-build docker-up manager-init

docker-up:
	docker compose up -d

docker-down:
	docker compose down --remove-orphans

docker-down-clear:
	docker compose down -v --remove-orphans

docker-pull:
	docker compose pull

docker-build:
	docker compose build --pull

manager-init: manager-composer-install

manager-composer-install:
	docker compose run --rm manager-php-cli composer install

cli:
	docker compose run --rm manager-php-cli php bin/app.php

