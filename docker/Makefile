define print_help
	@printf "\e[1;32m$1\e[0m\n"
endef

define print_info
	@printf "\e[1;33m$1 ...\e[0m\n"
endef

help-php-cs-fix:
	$(call print_help,Runs PHP CS fixer.)
run-php-cs-fix:
	docker-compose run --rm php /bin/bash -c './vendor/bin/php-cs-fixer fix'

help-eslint-fix:
	$(call print_help,Runs ESLint fixer.)
run-eslint-fix:
	docker-compose run --rm php /bin/bash -c '/root/.yarn/bin/yarn eslint-fix'

help-stylelint-fix:
	$(call print_help,Runs stylelint fixer.)
run-stylelint-fix:
	docker-compose run --rm php /bin/bash -c '/root/.yarn/bin/yarn stylelint-fix'

help-cs-fix-all: help-php-cs-fix help-eslint-fix help-stylelint-fix
run-cs-fix-all: run-php-cs-fix run-eslint-fix run-stylelint-fix

help-init-docker-compose-override:
	$(call print_help,Copies docker-compose.override.yml.dist to docker-compose.override.yml (existing file will be replaced).)
run-init-docker-compose-override:
	$(call print_info,Copying dist of docker-compose.override)
	cp docker-compose.override.yml.dist docker-compose.override.yml

help-composer-install:
	$(call print_help,Runs composer install command.)
run-composer-install:
	$(call print_info,Running composer install)
	docker-compose run --rm php composer install

help-yarn-install:
	$(call print_help,Runs yarn install command.)
run-yarn-install:
	$(call print_info,Running yarn install)
	docker-compose run --rm php bash -c '/root/.yarn/bin/yarn'

help-yarn-dev:
	$(call print_help,Runs yarn dev command.)
run-yarn-dev:
	$(call print_info,Running yarn dev)
	docker-compose run --rm php bash -c '/root/.yarn/bin/yarn dev'

help-db-create:
	$(call print_help,Creates DB if not exists.)
run-db-create:
	$(call print_info,Creating DB)
	docker-compose run --rm php bash -c '/usr/wait-for.sh db:3306 -- bin/console doctrine:database:create --if-not-exists'

help-db-migrate:
	$(call print_help,Migrates DB.)
run-db-migrate:
	$(call print_info,Migrating DB)
	docker-compose run --rm php bash -c '/usr/wait-for.sh db:3306 -- bin/console doctrine:migrations:migrate --no-interaction'

help-docker-build:
	$(call print_help,Build docker containers.)
run-docker-build:
	$(call print_info,Building docker containers)
	docker-compose build

help-docker-compose-up:
	$(call print_help,Starts up docker containers.)
run-docker-compose-up:
	$(call print_info,Starting up docker containers)
	docker-compose up -d

help-init:
	$(call print_help,Initializes the whole project. Existing project data will not be overwritten.)
run-init: run-init-docker-compose-override run-docker-build run-composer-install run-yarn-install run-yarn-dev run-docker-compose-up run-db-create run-db-migrate
	@printf "############################################################################\n"
	@printf "#####                                                                  #####\n"
	@printf "#####  Please make sure you have '127.0.0.1 docker.me' entry           #####\n"
	@printf "#####  in the '/etc/hosts' file.                                       #####\n"
	@printf "#####                                                                  #####\n"
	@printf "############################################################################\n"
	@printf "#####                                                                  #####\n"
	@printf "#####   The application runs at http://docker.me/                      #####\n"
	@printf "#####                                                                  #####\n"
	@printf "############################################################################\n"
	@printf "#####                                                                  #####\n"
	@printf "#####  See Readme.md for more information.                             #####\n"
	@printf "#####                                                                  #####\n"
	@printf "############################################################################\n"
