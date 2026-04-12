# Guía de Despliegue en Vercel (Plan Gratuito)

Este proyecto está configurado para ejecutarse en Vercel plan gratuito (Hobby).

## 🚀 Pasos para Desplegar

### 1. Conectar el Repositorio a Vercel

1. Ve a [vercel.com](https://vercel.com) e inicia sesión
2. Haz clic en "Add New..." → "Project"
3. Selecciona "Import Git Repository"
4. Selecciona tu repositorio de GitHub (eCommerse-Pazos-Vedoya)
5. Haz clic en "Import"

### 2. Configurar Variables de Entorno

Vercel abrirá la página de configuración del proyecto automáticamente. Si no es así:

1. Ve a tu proyecto en Vercel
2. Haz clic en "Settings" → "Environment Variables"
3. Agrega las siguientes variables:

```
APP_NAME=Laravel
APP_ENV=production
APP_KEY=base64:j9ZO4sYg+Rb8nlALyEJ8APthM0w5vCBzwqeKAPjvf4g=
APP_DEBUG=false
APP_URL=https://[TU-PROJECT-NAME].vercel.app
APP_LOCALE=en
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

**IMPORTANTE:** 
- Reemplaza `[TU-PROJECT-NAME]` con el nombre de tu proyecto en Vercel
- El `APP_URL` debe ser exactamente: `https://[TU-PROJECT-NAME].vercel.app`
- Mantén el `APP_KEY` que está generado

### 3. Verificar Configuración de Build

1. En Settings, ve a "Build & Development Settings"
2. Verifica que:
   - **Framework Preset**: `Other` (si no está auto-detectado)
   - **Build Command**: (puede estar vacío - Vercel usará el de vercel.json)
   - **Output Directory**: `public`
   - **Install Command**: (puede estar vacío - Vercel usará el de vercel.json)

3. Haz clic en "Save"

### 4. Desplegar

1. Haz clic en "Deploy"
2. Espera a que se complete el build (esto puede tomar 2-3 minutos)
3. Una vez completado, verás un link a tu aplicación

## 📋 Verificación Post-Despliegue

Una vez desplegado, verifica:

1. **Accede a la URL**: Debería mostrar tu página de inicio
2. **Revisa los Logs**: 
   - En Vercel, ve a "Deployments"
   - Selecciona el último deployment
   - Ve a "Functions" → "api/index.php"
   - Haz clic en "Logs" para ver los logs en tiempo real

## 🔧 Solución de Problemas Comunes

### Error: "Build command failed"

**Causa**: El buildCommand en vercel.json está causando problemas

**Solución**: El archivo ya está configurado correctamente. Si persiste:
1. Elimina el build actual
2. Ve a "Deployments" → "Redeploy" del último deployment exitoso
3. O haz un nuevo push a tu rama principal

### Error: "Failed to open stream: No such file or directory"

**Causa**: Ya fue resuelto. La configuración ahora no usa config cache en Vercel.

**Solución**: Ve a Settings → Redeploy

### Error: "Class 'config' does not exist"

**Causa**: Problema de configuración

**Solución**: Verifica que todas las variables de entorno en el paso 2 estén correctas

### Página muestra errores en Vercel pero funciona localmente

1. Ve a "Deployments" → "Functions" → "api/index.php" → "Logs"
2. Busca líneas rojas (errores)
3. Lee el mensaje de error completo
4. Agrega la variable faltante a Environment Variables si es necesario

## 📁 Configuración de Archivos

### vercel.json
- Define el buildCommand simplificado
- Establece variables de entorno por defecto
- Configura la función serverless de PHP

### api/index.php
- Punto de entrada de la aplicación
- Se ejecuta en cada request
- Limpia archivos de caché stale
- Maneja errores específicos de Vercel

### bootstrap/app.php
- Inicializa la aplicación Laravel
- Desactiva el config cache en Vercel para evitar archivos stale

## ⚠️ Limitaciones del Plan Gratuito

- **Límite de función**: 2048 MB de memoria por función
- **Tiempo máximo**: 30 segundos por request
- **Almacenamiento**: Sistema de archivos temporal (se pierde entre deployments)
  - *Nota*: Tu SQLite database se recreará en cada deploy. Para producción, usar MySQL/PostgreSQL.

## 🔄 Actualizar en Vercel

Simplemente haz push a tu repositorio:

```bash
git add .
git commit -m "Tu mensaje"
git push origin main  # o develop, según tu rama
```

Vercel automáticamente detectará los cambios y desplegará.

## 📞 Soporte

Si algo aún no funciona:

1. Revisa los logs en Vercel (Functions → api/index.php → Logs)
2. Verifica que todas las variables de entorno están configuradas
3. Asegúrate de que el APP_URL es exacto (incluyendo https://)
4. Intenta un "Redeploy" desde un deployment exitoso anterior

