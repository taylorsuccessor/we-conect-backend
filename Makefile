# Run PHP Code Sniffer
lint:
	./vendor/bin/phpcs ./app --standard=PSR2 --ignore=vendor,bootstrap  --exclude=Generic.Commenting.DocComment

# Fix PHP Code Sniffer violations
fix:
	./vendor/bin/phpcbf . --ignore=vendor,bootstrap

# Run PHPUnit tests
test:
	./vendor/bin/phpunit

# Install Composer dependencies
install:
	composer install

seed:
	php artisan db:seed --class=RolePermissionSeeder
	php artisan db:seed --class=AdminUserSeeder
	php artisan db:seed --class=ArticlesSeeder
	
dangerous-regenerate-db:
	php artisan migrate:fresh --seed

migrate:
	php artisan migrate

update:
	composer update

# Clear application cache
clear-cache:
	php artisan cache:clear
	php artisan config:clear
	php artisan route:clear
	php artisan view:clear

# Serve the application
serve:
	php artisan serve

# Serve the application
prepare-server:
	chmod +x ./scripts/preare_server_for_laravel.sh
	sudo ./scripts/preare_server_for_laravel.sh

# You have to use supervisorctl or systemctl
laravel-worker:
	php artisan queue:work redis --sleep=3 --tries=3

swagger-generate:
	php artisan  l5-swagger:generate

docker-build:
	 docker-compose  -f docker/docker-compose.yml  up  --build

docker-enter:
	docker-compose  -f docker/docker-compose.yml  exec app bash

docker-migrate:
	docker-compose  -f docker/docker-compose.yml  exec app make migrate

docker-seed:
	docker-compose  -f docker/docker-compose.yml exec app make seed

docker-fresh-database:
	docker-compose  -f docker/docker-compose.yml  exec app make dangerous-regenerate-db

# Run all checks and tests
check: lint test

.PHONY: lint fix test install update clear-cache reset serve check

