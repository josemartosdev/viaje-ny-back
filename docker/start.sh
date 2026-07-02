#!/usr/bin/env sh
set -e

PORT="${PORT:-8080}"

if [ ! -f .env ]; then
	printf "APP_ENV=%s\nAPP_DEBUG=%s\nAPP_SECRET=%s\n" "${APP_ENV:-prod}" "${APP_DEBUG:-0}" "${APP_SECRET:-change-me-in-railway}" > .env
fi

php bin/console cache:clear --env=prod --no-debug
php bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration --env=prod

exec php -S 0.0.0.0:${PORT} -t public public/index.php
