# Configuración para Vercel

Este proyecto está configurado para ejecutarse en Vercel. Aquí está lo que necesitas hacer:

## Pasos para desplegar en Vercel

### 1. Variables de Entorno en Vercel Dashboard

En el dashboard de Vercel, debes agregar las siguientes variables de entorno:

```
APP_NAME=Laravel
APP_ENV=production
APP_KEY=base64:j9ZO4sYg+Rb8nlALyEJ8APthM0w5vCBzwqeKAPjvf4g=
APP_DEBUG=false
APP_URL=https://tu-dominio.com

LOG_CHANNEL=stack
LOG_STACK=stderr
LOG_LEVEL=warning

DB_CONNECTION=sqlite
SESSION_DRIVER=file
SESSION_LIFETIME=120

CACHE_STORE=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync

BROADCAST_CONNECTION=log
```

**IMPORTANTE:** Reemplaza `https://tu-dominio.com` con tu URL real de Vercel.

### 2. Configuración de Vercel

El archivo `vercel.json` ya está configurado. Asegúrate de que:

- El `buildCommand` ejecute `php artisan config:cache`
- Las variables de entorno incluyan `APP_CONFIG_CACHE=/tmp/laravel-config.php`
- El `functions.api/index.php` tenga `laravelBuildCommand` configurado

### 3. Problemas Comunes y Soluciones

#### Error: "Failed to open stream: No such file or directory"

Este era el problema que encontraste. Ha sido resuelto configurando:
- `APP_CONFIG_CACHE` para usar un directorio temporal (`/tmp/`)
- El buildCommand para ejecutar `php artisan config:cache`
- El `api/index.php` para limpiar archivos de caché corruptos

#### Error: "Class 'config' does not exist"

Este error ocurría porque el archivo de caché de configuración no existía o estaba inválido. Ahora está resuelto.

#### Problemas de Permisos en Storage

El archivo `api/index.php` ahora automáticamente intenta establecer permisos en el directorio `storage/`.

### 4. Verificar que Todo Está Bien

Para verificar que tu despliegue funcionará:

1. Asegúrate de que `composer.json` esté actualizado con `composer update`
2. Ejecuta localmente: `php artisan config:cache`
3. Prueba que puedas gen
erar el caché sin errores
4. Verifica que `bootstrap/cache/config.php` se haya generado

### 5. Desplegar

1. Haz push de tus cambios a Git
2. Conecta tu repositorio a Vercel si aún no lo has hecho
3. Vercel automáticamente desplegará cuando hagas push
4. Revisa los logs de Vercel para cualquier error

## Archivos Modificados

- `vercel.json` - Configuración mejorada con buildCommand correcto y variables de entorno
- `api/index.php` - Manejo mejorado de caché y errores
- `bootstrap/app.php` - Configuración para Vercel
- `.vercelignore` - Archivos correctos a ignorar
- `.env.production` - Variables de producción

## Notas Importantes

1. **Base de Datos**: El proyecto usa SQLite por defecto. En Vercel, el sistema de archivos es temporal, así que la base de datos se creará y se perderá en cada despliegue. Si necesitas persistencia, cambia a MySQL o PostgreSQL.

2. **Sesiones y Cache**: Se están almacenando en el servidor de archivos local, que es temporal en Vercel. Para producción, considera usar Redis o una base de datos.

3. **Logs**: Se envían a `stderr` para que aparezcan en los logs de Vercel.

4. **Config Cache**: Se genera durante el build y se almacena en `/tmp/` que es un directorio writable en Vercel.
