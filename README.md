# Viaje NY API (Symfony 7 + MySQL 8)

API REST abierta para gestionar viajes sin autenticacion.

## Stack

- Symfony 7
- Doctrine ORM
- Doctrine Migrations
- Symfony Validator
- Nelmio CORS
- MySQL 8 (localhost:3306)

## Requisitos

- PHP 8.2+ (recomendado 8.3+)
- Composer
- MySQL 8 levantado en 127.0.0.1:3306

## Configuracion

La conexion local queda en `.env`:

`DATABASE_URL="mysql://root:@127.0.0.1:3306/viaje_ny?serverVersion=8.0.32&charset=utf8mb4"`

Ajusta usuario/password si tu MySQL local usa otras credenciales.

## Instalacion y ejecucion

1. Instalar dependencias:

```bash
composer install --prefer-source
```

2. Crear base de datos:

```bash
php bin/console doctrine:database:create
```

3. Ejecutar migraciones:

```bash
php bin/console doctrine:migrations:migrate --no-interaction
```

4. Cargar fixtures de ejemplo (itinerario + gastronomia):

```bash
php bin/console doctrine:fixtures:load --no-interaction
```

5. Levantar servidor local:

```bash
php -S 127.0.0.1:8000 -t public
```

## Endpoints CRUD

- `/api/trips` y `/api/trips/{id}`
- `/api/days` y `/api/days/{id}`
- `/api/places` y `/api/places/{id}`
- `/api/activities` y `/api/activities/{id}`
- `/api/tickets` y `/api/tickets/{id}`

## Filtros

- Activities: `dayId`, `status`, `category`, `q`
- Tickets: `dayId`, `type`, `q`
- Places: `type`, `minPriceLevel`, `maxPriceLevel`, `q`
- Days: `tripId`, `q`
- Trips: `q`

## Error JSON uniforme

Todas las respuestas de error devuelven:

```json
{
    "message": "Validation failed",
    "code": "VALIDATION_ERROR",
    "fieldErrors": {
        "title": "This value should not be blank."
    }
}
```

## Pruebas rápidas

Usa [api-tests.http](api-tests.http) para probar CRUD y filtros.

## Deploy en Railway (Docker)

Este repositorio ya incluye config lista para Railway:

- [Dockerfile](Dockerfile)
- [railway.toml](railway.toml)
- [docker/start.sh](docker/start.sh)

### Pasos

1. Crea un proyecto en Railway y conecta este repositorio.
2. Crea un servicio MySQL en Railway.
3. En el servicio de la API, define variables de entorno:

```dotenv
APP_ENV=prod
APP_DEBUG=0
APP_SECRET=pon_un_secret_largo_y_unico
DATABASE_URL=mysql://USER:PASSWORD@HOST:3306/DBNAME?serverVersion=8.0.32&charset=utf8mb4
CORS_ALLOW_ORIGIN=^https://tu-frontend\.railway\.app$
```

4. Ejecuta Deploy.

El contenedor arranca con `docker/start.sh` y hace:

1. `cache:clear` en prod
2. `doctrine:migrations:migrate`
3. levantar servidor HTTP en `0.0.0.0:$PORT`

### Nota importante

- No cargar fixtures en produccion.
- Mantener CORS restringido al dominio real del frontend.
