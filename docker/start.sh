#!/usr/bin/env sh
set -e

PORT="${PORT:-8080}"

php bin/console cache:clear --env=prod --no-debug
php bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration --env=prod

exec php -S 0.0.0.0:${PORT} -t public public/index.php
