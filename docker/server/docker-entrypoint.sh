#!/bin/sh

composer install --prefer-dist --no-progress --no-interaction

echo "Waiting for database to be ready..."
ATTEMPTS_LEFT_TO_REACH_DATABASE=10
until [ $ATTEMPTS_LEFT_TO_REACH_DATABASE -eq 0 ] || DATABASE_ERROR=$(php artisan db:monitor 2>&1); do
	sleep 1
	ATTEMPTS_LEFT_TO_REACH_DATABASE=$((ATTEMPTS_LEFT_TO_REACH_DATABASE - 1))
	echo "Still waiting for database to be ready... $ATTEMPTS_LEFT_TO_REACH_DATABASE attempts left."
done

if [ $ATTEMPTS_LEFT_TO_REACH_DATABASE -eq 0 ]; then
	echo "Failed to connect to the database:"
	echo "$DATABASE_ERROR"
	exit 1
else
	echo "The database is ready and reachable"
fi

echo "Waiting for redis to be ready..."

REDIS_MESSAGE=$(redis-cli -h redis ping)

if [ "$REDIS_MESSAGE" = "PONG" ]; then
    echo "Successfully connected to redis"
else
    echo "Failed to connect to redis"
    exit 1
fi

php artisan migrate
php artisan db:seed

chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

exec docker-php-entrypoint apache2-foreground
