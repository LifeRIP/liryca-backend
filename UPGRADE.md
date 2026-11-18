# Plan de actualización de Laravel

Inicio: 16 de noviembre de 2026. Validación final: 17 de noviembre de 2026.

## Punto de partida

Laravel 11.32, PHP 8.4 local, PostgreSQL de Aiven mediante TLS. Dependencias
instaladas desde los lockfiles; Vite compilado; las 20 migraciones aplicadas y
los tres roles base creados. `/` y `/up` responden HTTP 200.

## Ejecución

1. Establecer una referencia en Laravel 11: corregir el driver `pgsql`, eliminar
   collations de MySQL en las migraciones y comprobar conexión, migraciones y HTTP.
2. Añadir regresiones aisladas de Aiven: registro normal y de artistas, UUID,
   roles, tokens Sanctum, login/logout, playlists y búsqueda. Corregir fallos
   encontrados antes de continuar.
3. Pasar a Laravel 12 con dependencias compatibles y Pest 3; revisar Carbon 3,
   generación UUID, resolución del contenedor, validación y almacenamiento.
   Ejecutar pruebas y comprobar arranque como punto de control.
4. Pasar a Laravel 13 estable (13.43.0 en la comprobación final), Tinker 3 y Pest 4;
   actualizar los paquetes que bloqueen la resolución. Revisar CSRF, cache,
   sesiones, permisos, documentación OpenAPI y configuración de despliegue.
5. Verificar pruebas, compilación, análisis de dependencias, cachés de Laravel,
   conexión TLS, estado de migraciones y HTTP; dejar el servidor local ejecutándose.

No se ejecuta `migrate:fresh` ni se cargan los seeders de datos de demostración
en Aiven. Las pruebas usan SQLite en memoria. Las pruebas de proveedores OAuth
y correo se simulan y requieren comprobación manual con sus cuentas externas.

## Referencias

- https://laravel.com/docs/12.x/upgrade
- https://laravel.com/docs/13.x/upgrade

## Entorno Windows

PHP y Composer locales están en `.tools` (ignorado por Git). Para Composer,
agregar `.tools/php` y `C:\Program Files\Git\usr\bin` al PATH de la sesión.
El extractor ZIP de PHP y las operaciones de copia y limpieza de Composer
encontraron bloqueos en Windows. Se instaló desde los archivos oficiales del
lockfile en una carpeta limpia y se sincronizó `vendor`. Los adaptadores de
instalación y los temporales están en `.tools`; no forman parte de la aplicación.

## Puntos de control

- Laravel 11.32: HTTP 200, 20 migraciones aplicadas; 8 pruebas funcionales
  correctas (una advertencia de dependencia antigua de PHP 8.4).
- Laravel 12.72: 8 pruebas, 35 aserciones, sin fallos; generación OpenAPI correcta.
- Para Laravel 13, OpenAPI pasa de anotaciones Doctrine a atributos PHP. Se
  mantienen las operaciones documentadas y se obtiene el servidor de `APP_URL`
  o `L5_SWAGGER_BASE_PATH`.
- Se actualizan PHP mínimo a 8.4, CI, runtime de Vercel, CSRF, sesiones y cache.
- Correcciones encontradas con las pruebas: recarga de rutas con `require`,
  nombres de recursos únicos para cachear rutas, logout de Sanctum y consultas
  a `username` en lugar de columnas inexistentes.

## Resultado

- Laravel 13.43.0, PHP 8.4.27, Pest 4, Tinker 3 y L5-Swagger 11.
- La comprobación final detectó el nuevo parche 13.43.0; se actualizó también
  Guzzle de 8.5.0 a 8.5.2 y se repitieron las comprobaciones sobre el resultado.
- 12 pruebas y 61 aserciones correctas; sintaxis verificada en 119 archivos PHP.
- 20 migraciones aplicadas en Aiven y roles base creados de forma idempotente.
- Conexión TLS comprobada mediante `pg_stat_ssl`. Ocho endpoints del catálogo
  y playlists comprobados contra PostgreSQL con datos en una transacción
  revertida al terminar.
- OpenAPI generado, cachés de configuración, eventos, rutas y vistas probadas.
  Se separó la interfaz `/docs` del JSON `/docs/api-docs.json`.
- Vite 8, su integración Laravel 3 y Axios actualizados. Compilación correcta;
  auditoría Composer sin vulnerabilidades conocidas.
- Servidor local en http://127.0.0.1:8000. Reinicio: `composer run dev`.

No se ejecutó un despliegue remoto. La configuración de Vercel usa ahora PHP
8.4; el proveedor OAuth y el envío real de correo requieren comprobación manual.

### Aviso pendiente de una dependencia de compilación

La auditoría npm final informa GHSA-fc5q-39xc-vjpf en `picomatch` y sus
dependientes de desarrollo (6 avisos altos en total). El aviso fue actualizado
el 17 de noviembre de 2026 y no tiene una versión corregida publicada; la
última disponible es 4.0.7. Afecta al procesamiento de patrones glob maliciosos
en las herramientas Node, que no forman parte del servidor PHP ni del bundle
JavaScript publicado. Los patrones de este proyecto proceden de su código y
configuración. No se aplicó el downgrade incompatible sugerido por
`npm audit fix --force`. CI comprueba la compilación y mantiene visible la
auditoría completa como advertencia; la auditoría sin dependencias de desarrollo
debe pasar. Repetir `npm audit` cuando se publique el parche.

Referencia: https://github.com/advisories/GHSA-fc5q-39xc-vjpf

El autoload local funciona correctamente. La optimización adicional de
Composer se canceló por su lentitud en este entorno Windows; se conserva la
configuración original `optimize-autoloader=true` para las instalaciones normales.
