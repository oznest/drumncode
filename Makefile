PHP_CONTAINER = symfony_php
#WORKDIR = /var/www/symfony/app/
WORKDIR = /app/
EXEC_PHP = docker exec -it -w $(WORKDIR) $(PHP_CONTAINER)

# Цели
.PHONY: build up down sh test composer_install recreate check_code_style fix_code_style \
        restart_php fixtures_load create_db migrate elastica_populate generate_jwt_pair install

build:
	docker-compose up -d --build
	@if [ ! -f .build_installed ]; then make install && touch .build_installed; fi

up:
	docker-compose up -d

down:
	docker-compose down

sh:
	$(EXEC_PHP) /bin/bash

test:
	$(EXEC_PHP) php bin/phpunit tests

composer_install:
	$(EXEC_PHP) composer install

recreate:
	make down && make up

check_code_style:
	$(EXEC_PHP) php vendor/bin/phpcs && \
	$(EXEC_PHP) php vendor/bin/deptrac analyse

fix_code_style:
	$(EXEC_PHP) php vendor/bin/phpcbf

restart_php:
	docker-compose restart php

fixtures_load:
	$(EXEC_PHP) php bin/console -q hautelook:fixtures:load

create_db:
	$(EXEC_PHP) php bin/console doctrine:database:create

migrate:
	$(EXEC_PHP) php bin/console doctrine:migrations:migrate --no-interaction

elastica_populate:
	$(EXEC_PHP) php bin/console fos:elastica:populate

generate_jwt_pair:
	$(EXEC_PHP) php bin/console --overwrite -q lexik:jwt:generate-keypair

install:
	make composer_install && \
	make create_db && \
	make migrate && \
	make fixtures_load && \
	make elastica_populate && \
	make generate_jwt_pair