#!/usr/bin/env sh
set -e

PORT="${PORT:-8080}"

# Railway may expose MySQL connection details as MYSQL_URL or MYSQL* variables.
if [ -z "${DATABASE_URL:-}" ] && [ -n "${MYSQL_URL:-}" ]; then
	DATABASE_URL="${MYSQL_URL}"
	export DATABASE_URL
fi

if [ -z "${DATABASE_URL:-}" ] && [ -n "${MYSQLHOST:-}" ] && [ -n "${MYSQLDATABASE:-}" ] && [ -n "${MYSQLUSER:-}" ]; then
	MYSQL_PORT="${MYSQLPORT:-3306}"
	DATABASE_URL="mysql://${MYSQLUSER}:${MYSQLPASSWORD:-}@${MYSQLHOST}:${MYSQL_PORT}/${MYSQLDATABASE}?serverVersion=8.0.32&charset=utf8mb4"
	export DATABASE_URL
fi

if [ -z "${DATABASE_URL:-}" ]; then
	echo "[start] ERROR: DATABASE_URL no esta definida (ni MYSQL_URL/MYSQL*)."
	exit 1
fi

case "${DATABASE_URL}" in
	*"@localhost"*|*"@localhost:"*)
		echo "[start] ERROR: DATABASE_URL usa host localhost; en Railway debe ser host remoto del servicio MySQL."
		exit 1
		;;
esac

if [ ! -f .env ]; then
	printf "APP_ENV=%s\nAPP_DEBUG=%s\nAPP_SECRET=%s\nDATABASE_URL=%s\n" "${APP_ENV:-prod}" "${APP_DEBUG:-0}" "${APP_SECRET:-change-me-in-railway}" "${DATABASE_URL}" > .env
fi

# Export APP_ENV for the PHP server
export APP_ENV="${APP_ENV:-prod}"
export APP_DEBUG="${APP_DEBUG:-0}"

php bin/console cache:clear --env=prod --no-debug

attempt=1
max_attempts=12
while [ "$attempt" -le "$max_attempts" ]; do
	if php bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration --env=prod; then
		break
	fi

	if [ "$attempt" -eq "$max_attempts" ]; then
		echo "[start] ERROR: no se pudo conectar a la base de datos tras ${max_attempts} intentos."
		exit 1
	fi

	echo "[start] Base de datos aun no disponible, reintentando (${attempt}/${max_attempts})..."
	attempt=$((attempt + 1))
	sleep 5
done

exec php -S 0.0.0.0:${PORT} -t public public/index.php
