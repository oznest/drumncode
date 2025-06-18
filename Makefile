build:
	docker-compose up -d --build && make install
up:
	docker-compose up -d
down:
	docker-compose down
sh:
	docker exec -it symfony_php /bin/bash
test:
	docker exec -it symfony_php php /var/www/symfony/app/bin/phpunit /var/www/symfony/app/tests
install:
	docker exec -it symfony_php sh -c "cd /var/www/symfony/app && composer install"
recreate:
	make down && make up
