# Liryca backend

API de Liryca con Laravel 13, Sanctum, Socialite, permisos y autenticacion de dos factores.

## Requisitos

- PHP 8.4 o superior y Composer 2.
- Extensiones de Laravel, `pdo_pgsql`, `pdo_sqlite` para pruebas, `mbstring`, `openssl`, `fileinfo`, `curl`, `intl`, `zip` y `gd` para QR.
- Node.js 22.12 o superior (CI usa Node.js 24).
- PostgreSQL. La conexion a Aiven requiere TLS.

## Instalacion

```sh
composer install
npm ci
cp .env.example .env
php artisan key:generate
```

Configurar `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME` y `DB_PASSWORD` en `.env`.
Usar `DB_CONNECTION=pgsql` y `DB_SSLMODE=require`. Para verificar el certificado de
Aiven, indicar `DB_SSLMODE=verify-full` y `DB_SSLROOTCERT` con la ruta a su CA.

```sh
php artisan app:check-database-connection
php artisan migrate
php artisan db:seed --class=RoleSeeder
npm run build
composer run dev
```

Para iniciar el proyecto en las siguientes sesiones, ejecutar `composer run dev`
desde esta carpeta. El servidor escucha en `http://127.0.0.1:8000` y se detiene con
`Ctrl+C`.

El seeder de roles es idempotente. `DatabaseSeeder` carga datos de demostracion;
no es necesario para iniciar una base vacia.

La API esta bajo `/api/v1`; `/up` comprueba el arranque. La interfaz OpenAPI esta
en `/docs`; el documento JSON esta en `/docs/api-docs.json`.

## Verificacion

```sh
vendor/bin/pest
php artisan l5-swagger:generate
php artisan optimize
php artisan optimize:clear
composer audit
npm audit
npm run build
```

Las pruebas usan SQLite en memoria, sin conectarse a Aiven. Cubren registro de
usuarios y artistas, UUID, roles, Sanctum, login/logout, playlists, catalogo,
rankings y secretos 2FA cifrados. Los proveedores OAuth y el envio real de correo
requieren sus propias credenciales y una comprobacion manual.

## Postman

Importar [la coleccion](postman/Liryca.postman_collection.json) y, opcionalmente,
[el entorno local](postman/Liryca.local.postman_environment.json). Ejecutar primero
solo la carpeta `01 - Flujo básico`; captura el token y los IDs automaticamente.
Los requisitos de catalogo, 2FA y OAuth estan en [postman/README.md](postman/README.md).
