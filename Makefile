############################################################
# PROJECT ##################################################
############################################################
.PHONY: project install setup clean

project: install setup

install:
	composer install

up:
	docker-compose up

bash:
	docker-compose run web bash

setup:
	mkdir -p temp temp/sessions log
	chmod +0777 temp temp/sessions log

clean:
	find temp -mindepth 1 ! -name '.gitignore' -type f,d -exec rm -rf {} +
	find log -mindepth 1 ! -name '.gitignore' -type f,d -exec rm -rf {} +

############################################################
# DEVELOPMENT ##############################################
############################################################
.PHONY: qa dev cs csf phpstan tests coverage dev build

qa: cs phpstan

cs:
	vendor/bin/codesniffer app

csf:
	vendor/bin/codefixer app

phpstan:
	vendor/bin/phpstan analyse -c phpstan.neon --memory-limit=512M app

tests:
	echo "OK"

coverage:
	echo "OK"

dev:
	NETTE_DEBUG=1 NETTE_ENV=dev php -S 0.0.0.0:8000 -t www

build:
	echo "BUILD OK"

############################################################
# DEPLOYMENT ###############################################
############################################################
.PHONY: deploy

deploy:
	$(MAKE) clean
	$(MAKE) project
	$(MAKE) build
	$(MAKE) clean
