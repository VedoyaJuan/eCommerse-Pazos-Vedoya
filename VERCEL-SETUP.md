# Despliegue en Vercel

## Variables de entorno requeridas en el dashboard de Vercel

Ve a tu proyecto → **Settings → Environment Variables** y agrega SOLO estas dos (el resto ya está en `vercel.json`):

| Variable | Valor |
|----------|-------|
| `APP_KEY` | `base64:j9ZO4sYg+Rb8nlALyEJ8APthM0w5vCBzwqeKAPjvf4g=` |
| `APP_URL` | `https://TU-PROYECTO.vercel.app` |

> **Importante:** Reemplaza `TU-PROYECTO` con el nombre real de tu proyecto en Vercel.

---

## Variables ya configuradas automáticamente (via `vercel.json`)

Estas NO hace falta agregarlas en el dashboard, ya están en el repo:

```
APP_ENV=production
APP_DEBUG=false
LOG_CHANNEL=stderr
VIEW_COMPILED_PATH=/tmp
CACHE_STORE=array
SESSION_DRIVER=cookie
QUEUE_CONNECTION=sync
FILESYSTEM_DISK=local
```

---

## Por qué estas configuraciones para Vercel (serverless)

| Variable | Valor | Motivo |
|----------|-------|--------|
| `LOG_CHANNEL=stderr` | `stderr` | Vercel captura stderr. El filesystem es de solo lectura. |
| `VIEW_COMPILED_PATH=/tmp` | `/tmp` | `/tmp` es el único dir escribible en Vercel. Blade compila vistas aquí. |
| `CACHE_STORE=array` | `array` | Sin filesystem escribible, cache en memoria por request. |
| `SESSION_DRIVER=cookie` | `cookie` | Sin filesystem escribible, sesiones en cookie encriptada. |

---

## Pasos para desplegar

1. **Conectar repo**: Ve a [vercel.com](https://vercel.com) → New Project → Import desde GitHub
2. **Agregar env vars**: Como se indica arriba (solo `APP_KEY` y `APP_URL`)
3. **Deploy**: Vercel detecta el `vercel.json` automáticamente. Clic en Deploy.

---

## Solución de problemas

### 500 en logs con `Blade compiler` o `realpath... false`
El directorio `storage/framework/views` no existe en Vercel. Ya resuelto con `VIEW_COMPILED_PATH=/tmp` y `config/view.php`.

### 500 sin mensaje de error
Verificar que `APP_KEY` esté cargado en las env vars del dashboard de Vercel.

### Error de filesystem / logs
El filesystem de Vercel es de solo lectura excepto `/tmp`. Ya resuelto con `LOG_CHANNEL=stderr`.

### Ver logs en tiempo real
Vercel Dashboard → Deployments → Functions → `api/index.php` → Logs

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

