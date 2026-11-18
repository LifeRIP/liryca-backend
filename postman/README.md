# Liryca API en Postman

Importar `Liryca.postman_collection.json` (Collection v2.1). Opcionalmente,
importar `Liryca.local.postman_environment.json` y seleccionar **Liryca - Local**.
El servidor predeterminado es `http://127.0.0.1:8000`.

## Primera prueba

1. Iniciar Laravel con `composer run dev` desde el repositorio.
2. Comprobar **00 - Estado y documentación**.
3. Ejecutar **solo la carpeta 01 - Flujo básico** en orden, con Collection Runner
   o enviando sus solicitudes una a una.
4. Revisar los resultados de los scripts en Postman. Después se puede ejecutar
   **02 - Validaciones y errores**: los estados 400 y 401 son los esperados allí.

La carpeta 01 genera una cuenta única en cada registro, inicia sesión, crea una
playlist, un post y un comentario, y prueba consultas, edición y likes. Guarda
automáticamente `token`, `user_id`, `playlist_id`, `post_id` y `comment_id`.
No requiere datos de demostración, canciones ni un artista verificado.

El registro envía una notificación según el `MAIL_MAILER` del servidor. En local
se puede usar `MAIL_MAILER=log`; no se debe confundir con un envío real de correo.
Las pruebas de la colección no crean roles: la base debe tener los roles iniciales
(`php artisan db:seed --class=RoleSeeder`).

## Carpetas opcionales

| Carpeta | Requisito |
| --- | --- |
| 03 - Cuenta de artista | Crea otra cuenta y guarda `artist_token` sin reemplazar el token del usuario normal. |
| 04 - Álbumes y canciones | Artista verificado; ejecutar primero crear álbum y crear canción. |
| 05 - Canciones en playlists | `playlist_id` del flujo básico y `song_id` del catálogo. |
| 06 - Seguimientos y publicaciones | Cuenta normal y artista; algunas solicitudes requieren un álbum. |
| 07 - 2FA | Ejecución manual: registrar QR en una app autenticadora y rellenar `otp`. |
| 08 - Correo, perfil y OAuth | Credenciales de correo/proveedor; tokens o códigos reales cuando corresponda. |
| 09 - Limpieza | Elimina comentario, post y playlist del flujo básico y revoca el token. Conserva las cuentas y el catálogo opcional. |

**No ejecutar toda la colección de una vez**: las carpetas opcionales requieren
preparación y la última elimina los objetos de prueba.

Registrar un artista no marca `artists.verified=true`. El backend exige esa
verificación para crear álbumes y no expone una ruta de verificación en esta
colección. En una base local de desarrollo se puede verificar explícitamente
la cuenta de prueba desde Tinker, usando su UUID real:

```php
App\Models\Artist::where('user_id', 'PEGAR_ARTIST_USER_ID')->update(['verified' => true]);
```

`artist_user_id` es el UUID en `users`. `artist_id` es el ID numérico en
`artists`. Las rutas de consulta del artista y de sus álbumes reciben el UUID
de usuario, mientras que el backend obtiene el artista de creación desde el token.

## Variables y ejemplos

- Cambiar `base_url` en el environment para usar otro servidor; sin environment
  se utiliza la variable de la colección.
- `password` es una contraseña ficticia para las cuentas de prueba. Para entrar
  con una cuenta existente, rellenar `user_email` y `password` y ejecutar login.
- Los IDs de álbum y canción se capturan al crearlos. Algunos cuerpos contienen
  valores numéricos como `{{album_id}}` sin comillas: deben estar definidos.
- No crear variables de environment llamadas `token` o `user_id` vacías: tendrían
  prioridad sobre los valores capturados en la colección.
- Los ejemplos de respuesta guardados son **ilustrativos**, no respuestas
  obtenidas de Aiven. Las URLs de imágenes/audio son de demostración.
- Las aserciones también detectan respuestas HTTP 200 con `success=false`.
- Crear publicaciones usa `x-www-form-urlencoded`: el controlador actual lee
  esos campos como parámetros de formulario. El resto de ejemplos con cuerpo
  usa JSON, salvo donde la propia solicitud indica lo contrario.
- Los endpoints de like/seguimiento alternan entre agregar y quitar la relación.
- Para inspeccionar el redirect OAuth, desactivar el seguimiento automático de
  redirecciones; la autorización completa requiere navegador, cookies y `state`.

Al exportar una colección ya utilizada, quitar los tokens y el secreto 2FA antes
de compartirla. Los archivos incluidos no contienen credenciales reales.

## Alcance

Hay 74 solicitudes agrupadas por función. Se omitieron las acciones de
`Route::resource` cuyo método no existe o está pendiente en el controlador
(por ejemplo, crear usuario por `POST /user` y las pantallas `create`/`edit`).
Tampoco se presenta `POST /auth/validate-token` como una validación general de
bearer: su implementación actual es un helper de 2FA y no un endpoint JSON
independiente para ese propósito.

La colección cumple el esquema oficial Postman v2.1. Se validaron las 74 rutas,
los cuerpos JSON y la sintaxis de los scripts. El flujo básico, los errores y
la limpieza pasaron 25 solicitudes y 53 aserciones contra SQLite en memoria,
con notificaciones simuladas. Las carpetas que dependen de servicios externos
o de preparación manual no se ejecutaron sobre Aiven.
