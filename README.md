# Tic-Tac Store - Relojería Online

## Proyecto Universitario - Materia: Aplicaciones Web
**Universidad Nacional de la Patagonia San Juan Bosco (UNPSJB)**

### Integrantes de la Comisión:
- Vedoya Juan Pablo
- Pazos Sebastian Luis

## Descripción
Tic-Tac Store es una empresa ficticia especializada en la venta online de relojes de alta gama, diseñada como proyecto final para la materia de Aplicaciones Web de la UNPSJB.

## Tecnologías Utilizadas
- Laravel Framework
- PHP
- MongoDB
- HTML/CSS/JavaScript

## Características del Proyecto
- Catálogo de productos con filtros y búsqueda
- Sistema de autenticación de usuarios
- Panel de administración para gestión de productos y pedidos

## Instrucciones para Ejecutar Localmente

Para correr este proyecto en tu entorno local por primera vez (o al bajar una nueva rama), sigue estos pasos:

1. **Instalar dependencias de PHP y Node**
   Asegúrate de estar en la carpeta `eCommerse-Pazos-Vedoya` y ejecuta:
   ```bash
   composer install
   npm install
   ```

2. **Configurar el entorno (Si es tu primera vez clonando el proyecto)**
   Copia el archivo de ejemplo y genera la clave de la aplicación:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

3. **Crear y poblar la Base de Datos**
   Para tener los relojes de prueba configurados en el proyecto (esto ejecutará las migraciones y los seeders):
   ```bash
   php artisan migrate --seed
   ```

4. **Levantar los servidores de desarrollo**
   Necesitas abrir **dos terminales** diferentes en la carpeta del proyecto.
   
   En la **primera terminal**, levanta el servidor de Laravel:
   ```bash
   php artisan serve
   ```
   
   En la **segunda terminal**, levanta Vite para que compile los estilos de TailwindCSS en tiempo real:
   ```bash
   npm run dev
   ```

5. **Acceder a la aplicación**
   - **Tienda Pública**: [http://127.0.0.1:8000/](http://127.0.0.1:8000/)
   - **Panel de Administración (ABM Productos)**: [http://127.0.0.1:8000/products](http://127.0.0.1:8000/products)

---
*Proyecto creado para fines educativos - Todos los derechos reservados © 2026*