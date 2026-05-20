# Tic-Tac Store — Documentación técnica

## Arquitectura general

El proyecto es una aplicación Laravel que cumple **dos roles al mismo tiempo**:

1. **Panel de administración** (web, Blade + Tailwind): gestión de productos y pedidos.
2. **API REST** (JSON, prefijo `/api`): consumida por la app móvil React Native del cliente.

Ambos comparten la misma base de datos (Neon PostgreSQL en producción, SQLite en local).

```
┌─────────────────────────────────────────────────────┐
│                  Laravel (este proyecto)             │
│                                                      │
│  ┌──────────────────┐      ┌──────────────────────┐  │
│  │  Panel Admin     │      │  API REST (/api/*)    │  │
│  │  (Blade views)   │      │  JSON + Sanctum auth  │  │
│  └──────────────────┘      └──────────────────────┘  │
│              │                       │                │
│              └──────────┬────────────┘                │
│                         ▼                             │
│              ┌─────────────────────┐                  │
│              │   Base de datos     │                  │
│              │  PostgreSQL (Neon)  │                  │
│              └─────────────────────┘                  │
└─────────────────────────────────────────────────────┘
         ▲                          ▲
         │                          │
  Admin (navegador)        App móvil (React Native)
```

---

## Base de datos

### Tablas y relaciones

```
users
  id, name, email, password, ...

products
  id, name, description, price, stock, brand, image_url

orders
  id, user_id (FK → users, nullable), status, total,
  customer_name, customer_email, customer_phone,
  shipping_address, notes, created_at, updated_at

order_items
  id, order_id (FK → orders), product_id (FK → products),
  quantity, unit_price

personal_access_tokens  ← generada por Sanctum
  id, tokenable_id, tokenable_type, name, token, ...
```

### Relaciones Eloquent

```
User      → hasMany → Order
Order     → belongsTo → User
Order     → hasMany → OrderItem
OrderItem → belongsTo → Order
OrderItem → belongsTo → Product
Product   → hasMany → OrderItem
```

### Estados posibles de un pedido (`orders.status`)

| Valor        | Significado            |
|-------------|------------------------|
| `pending`    | Recibido, sin procesar |
| `processing` | En preparación         |
| `shipped`    | Enviado                |
| `delivered`  | Entregado              |
| `cancelled`  | Cancelado              |

---

## Panel de administración (Web)

### Rutas

| Método | URL                    | Descripción                        |
|--------|------------------------|------------------------------------|
| GET    | `/`                    | Vitrina pública de productos       |
| GET    | `/product/{id}`        | Detalle de producto (público)      |
| GET    | `/products`            | Admin: listado de productos        |
| GET    | `/products/create`     | Admin: formulario nuevo producto   |
| POST   | `/products`            | Admin: guardar nuevo producto      |
| GET    | `/products/{id}/edit`  | Admin: formulario editar producto  |
| PUT    | `/products/{id}`       | Admin: actualizar producto         |
| DELETE | `/products/{id}`       | Admin: eliminar producto           |
| GET    | `/orders`              | Admin: listado de pedidos          |
| GET    | `/orders/{id}`         | Admin: detalle de pedido           |
| PUT    | `/orders/{id}`         | Admin: cambiar estado del pedido   |
| DELETE | `/orders/{id}`         | Admin: eliminar pedido             |

> El panel admin no tiene login por ahora. Cualquier persona con la URL puede acceder.

---

## API REST

**Base URL local:** `http://localhost:8000/api`  
**Base URL producción:** `https://e-commerse-pazos-vedoya.vercel.app/api`

### Autenticación

La API usa **Laravel Sanctum** con tokens Bearer.  
El flujo es:
1. La app móvil llama a `POST /api/register` o `POST /api/login`.
2. El servidor devuelve un `token`.
3. Todos los endpoints protegidos requieren el header: `Authorization: Bearer <token>`.

### Endpoints

#### Auth

| Método | Endpoint        | Auth | Descripción                        |
|--------|----------------|------|------------------------------------|
| POST   | `/register`     | No   | Registrar usuario nuevo            |
| POST   | `/login`        | No   | Iniciar sesión, devuelve token     |
| POST   | `/logout`       | Sí   | Invalidar token actual             |
| GET    | `/user`         | Sí   | Datos del usuario autenticado      |

**Body de `/register`:**
```json
{
  "name": "Juan",
  "email": "juan@mail.com",
  "password": "secret123",
  "password_confirmation": "secret123"
}
```

**Body de `/login`:**
```json
{
  "email": "juan@mail.com",
  "password": "secret123"
}
```

**Respuesta de login/register:**
```json
{
  "token": "1|abc123...",
  "user": { "id": 1, "name": "Juan", "email": "juan@mail.com" }
}
```

---

#### Productos

| Método | Endpoint             | Auth | Descripción                   |
|--------|---------------------|------|-------------------------------|
| GET    | `/products`          | No   | Listar productos (paginado)   |
| GET    | `/products/{id}`     | No   | Detalle de un producto        |

**Query params de `/products`:**

| Param       | Tipo   | Descripción                            |
|-------------|--------|----------------------------------------|
| `search`    | string | Busca en nombre, marca y descripción   |
| `brand`     | string | Filtra por marca                       |
| `max_price` | number | Precio máximo                          |
| `in_stock`  | bool   | `true` para mostrar solo con stock     |

**Respuesta:**
```json
{
  "data": [
    {
      "id": 1,
      "name": "Casio G-Shock",
      "brand": "Casio",
      "description": "Reloj deportivo resistente al agua",
      "price": 45000.00,
      "stock": 8,
      "in_stock": true,
      "image_url": "https://..."
    }
  ],
  "links": { ... },
  "meta": { "current_page": 1, "last_page": 3, ... }
}
```

---

#### Pedidos

| Método | Endpoint          | Auth | Descripción                          |
|--------|------------------|------|--------------------------------------|
| GET    | `/orders`         | Sí   | Pedidos del usuario autenticado      |
| POST   | `/orders`         | Sí   | Crear un nuevo pedido                |
| GET    | `/orders/{id}`    | Sí   | Detalle de un pedido propio          |

**Body de `POST /orders`:**
```json
{
  "customer_name": "Juan Vedoya",
  "customer_email": "juan@mail.com",
  "customer_phone": "3814001234",
  "shipping_address": "Av. Siempreviva 742, Tucumán",
  "notes": "Dejar en portería",
  "items": [
    { "product_id": 1, "quantity": 2 },
    { "product_id": 3, "quantity": 1 }
  ]
}
```

**Respuesta:**
```json
{
  "data": {
    "id": 5,
    "status": "pending",
    "total": 135000.00,
    "customer_name": "Juan Vedoya",
    "items": [
      {
        "id": 8,
        "product": { "id": 1, "name": "Casio G-Shock", ... },
        "quantity": 2,
        "unit_price": 45000.00,
        "subtotal": 90000.00
      }
    ],
    "created_at": "2026-05-19T20:00:00.000Z"
  }
}
```

> Al crear un pedido, el stock de cada producto se descuenta automáticamente.  
> Si el stock es insuficiente para algún ítem, la operación entera se cancela (transacción DB).

---

## Flujo completo de un pedido desde la app móvil

```
1. [App] POST /api/login → recibe token

2. [App] GET /api/products?in_stock=true → muestra catálogo

3. [App] Usuario agrega productos al carrito (local, en la app)

4. [App] POST /api/orders  (Bearer token + body con items)
         ├─ Laravel valida stock de cada producto
         ├─ Descuenta stock en una transacción
         ├─ Crea la orden y los order_items
         └─ Devuelve la orden creada con status "pending"

5. [Admin] Ve el nuevo pedido en /orders
6. [Admin] Cambia el estado (processing → shipped → delivered)

7. [App] GET /api/orders → cliente ve el estado actualizado de sus pedidos
```

---

## Despliegue

### Local (SQLite)
```bash
# Crear la base y correr migraciones
touch database/database.sqlite
DB_CONNECTION=sqlite DB_DATABASE=$(pwd)/database/database.sqlite php artisan migrate

# Levantar el servidor
php artisan serve
```

### Producción (Vercel + Neon PostgreSQL)
El `vercel.json` ya tiene las variables de entorno de Neon configuradas.  
En cada deploy, `build.sh` ejecuta `php artisan migrate --force` contra Neon automáticamente.

```bash
# Migrar manualmente a Neon (con .env apuntando a Neon)
php artisan migrate --force
```

---

## Estructura de archivos relevantes

```
routes/
  web.php               → Rutas del panel admin + vitrina
  api.php               → Rutas de la API REST

app/
  Models/
    Product.php         → Modelo de producto
    Order.php           → Modelo de pedido
    OrderItem.php       → Línea de pedido
    User.php            → Usuario (con HasApiTokens para Sanctum)
  Http/
    Controllers/
      ProductController.php       → CRUD productos (admin web)
      OrderController.php         → Gestión pedidos (admin web)
      StoreController.php         → Vitrina pública
      Api/
        AuthController.php        → register / login / logout
        ProductController.php     → API productos
        OrderController.php       → API pedidos
    Resources/
      ProductResource.php         → JSON de producto
      OrderResource.php           → JSON de pedido
      OrderItemResource.php       → JSON de ítem de pedido

resources/views/
  layouts/
    admin.blade.php     → Layout del panel admin (sidebar)
    store.blade.php     → Layout de la vitrina
  products/             → Vistas admin de productos
  orders/               → Vistas admin de pedidos
  home.blade.php        → Catálogo público
```
