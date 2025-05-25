#!/bin/bash
set -e

mkdir -p var/cache var/log
chmod -R 0777 var

composer install

if [ "$WAIT_FOR_DB" = "true" ]; then
  echo "Waiting for the database..."
  until php bin/console doctrine:query:sql "SELECT 1" > /dev/null 2>&1; do
    echo "Database connection failed. Retrying..."
    php bin/console doctrine:query:sql "SELECT 1" || true
    sleep 1
  done
  echo "Database connection established."
fi

php bin/console cache:clear
php bin/console cache:warmup

if [ "$RUN_MIGRATIONS" = "true" ]; then
  php bin/console doctrine:migrations:migrate --no-interaction
fi

if [ "$LOAD_FIXTURES" = "true" ]; then
  php bin/console doctrine:fixtures:load --no-interaction
fi

screen -dmS message-consumer sh -c "php /var/www/api/bin/console messenger:consume scheduler_bus > /var/www/api/var/log/message_consumer.log"

exec "$@"
