define print_info
	@printf "\e[1;33m$1 ...\e[0m\n"
endef

init-docker-compose-override:
	$(call print_info,Copying dist of docker-compose.override)
	cp docker-compose.override.yml.dist docker-compose.override.yml

composer-install:
	$(call print_info,Running composer install)
	docker-compose run --rm php composer install

yarn-install:
	$(call print_info,Running yarn install)
	docker-compose run --rm php bash -c '/root/.yarn/bin/yarn'

yarn-dev:
	$(call print_info,Running yarn dev)
	docker-compose run --rm php bash -c '/root/.yarn/bin/yarn dev'

db-create:
	$(call print_info,Creating DB)
	docker-compose run --rm php bash -c '/usr/wait-for.sh db:3306 -- bin/console doctrine:database:create --if-not-exists'

db-migrate:
	$(call print_info,Migrating DB)
	docker-compose run --rm php bash -c '/usr/wait-for.sh db:3306 -- bin/console doctrine:migrations:migrate --no-interaction'

docker-build:
	$(call print_info,Building docker containers)
	docker-compose build

docker-compose-up:
	$(call print_info,Starting up docker containers)
	docker-compose up -d

init: init-docker-compose-override docker-build composer-install yarn-install yarn-dev docker-compose-up db-create db-migrate
	$(call print_info,The application has been installed successfully and runs on http://localhost/)
