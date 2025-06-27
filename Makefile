build:
	docker-compose up -d --build && make install
up:
	docker-compose up -d
down:
	docker-compose down
sh:
	docker exec -it -w /var/www/symfony/app/ symfony_php /bin/bash
test:
	docker exec -it symfony_php php /var/www/symfony/app/bin/phpunit /var/www/symfony/app/tests
composer_install:
	docker exec -it -w /var/www/symfony/app/ symfony_php  composer install
recreate:
	make down && make up
check_code_style:
	docker exec -it -w /var/www/symfony/app/ symfony_php php vendor/bin/phpcs && docker exec -it -w /var/www/symfony/app/ symfony_php php vendor/bin/deptrac analyse
fix_code_style:
	docker exec -it -w /var/www/symfony/app/ symfony_php php vendor/bin/phpcbf
restart_php:
	docker compose restart php
fixtures_load:
	docker exec -it -w /var/www/symfony/app/ symfony_php php bin/console -q hautelook:fixtures:load
create_db:
	docker exec -it -w /var/www/symfony/app/ symfony_php php bin/console doctrine:database:create
migrate:
	docker exec -it -w /var/www/symfony/app/ symfony_php php bin/console doctrine:migrations:migrate --no-interaction
elastica_populate:
	docker exec -it -w /var/www/symfony/app/ symfony_php php bin/console fos:elastica:populate
generate_jwt_pair:
	docker exec -it -w /var/www/symfony/app/ symfony_php php bin/console lexik:jwt:generate-keypair
install:
	make composer_install && make create_db && make migrate && make fixtures_load && make elastica_populate && make generate_jwt_pair

