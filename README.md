# VentasPro — Sistema de Ventas Profesional con Laravel 11

## Instalación (5 pasos)

```bash
# 1. Instalar dependencias (vendor ya incluido en el ZIP)
composer install

# 2. Copiar entorno y generar clave
cp .env.example .env
php artisan key:generate

# 3. Configurar base de datos en .env
#    DB_DATABASE=sistema_ventas
#    DB_USERNAME=root
#    DB_PASSWORD=tu_password

# 4. Crear BD, migrar y poblar datos
mysql -u root -p -e "CREATE DATABASE sistema_ventas CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
php artisan migrate
php artisan db:seed

# 5. Enlace de imágenes y levantar servidor
php artisan storage:link
php artisan serve
# → http://localhost:8000
```

## Credenciales
| Rol      | Email                | Password      |
|----------|----------------------|---------------|
| Admin    | admin@ventas.com     | admin123      |
| Vendedor | vendedor@ventas.com  | vendedor123   |

## Módulos del sistema

| Módulo         | URL                  | Rol           |
|----------------|----------------------|---------------|
| Dashboard       | /dashboard           | Todos         |
| Nueva Venta     | /ventas/nueva        | Todos         |
| Historial Ventas| /ventas              | Todos         |
| Clientes        | /clientes            | Todos         |
| Productos       | /productos           | Todos / Admin |
| Categorías      | /categorias          | Todos         |
| Proveedores     | /proveedores         | Todos         |
| Compras         | /compras             | Admin         |
| Caja            | /caja                | Admin         |
| Reportes        | /reportes            | Admin         |
| Usuarios        | /usuarios            | Admin         |
| Configuración   | /configuracion       | Admin         |
| Mi Perfil       | /perfil              | Todos         |

## Stack
- **Laravel 11** — Framework PHP
- **Eloquent ORM** — Modelos con relaciones y scopes
- **Blade** — Templates con @extends, @push/@stack
- **Chart.js 4** — Gráficas del dashboard y reportes
- **CSS propio** — Sin Bootstrap, diseño profesional responsive

## Estructura
```
app/
  Http/
    Controllers/   — 13 controladores
    Middleware/    — AuthSession
  Models/          — 12 modelos Eloquent
  Providers/       — AppServiceProvider
database/
  migrations/      — 5 migraciones
  seeders/         — DatabaseSeeder con datos demo
resources/views/   — 21 vistas Blade
  layouts/         — main.blade.php, auth.blade.php
  auth/            — login
  dashboard/       — dashboard con KPIs y gráficas
  products/        — index, form
  sales/           — index, nueva (POS), show
  clients/         — index con modal
  purchases/       — index, form, show
  caja/            — index, historial
  categories/      — index con modal
  suppliers/       — index con modal
  users/           — index con modal
  settings/        — configuración del negocio
  profile/         — perfil de usuario
public/
  css/app.css      — Estilos profesionales
  js/app.js        — JS global
routes/web.php     — 35+ rutas organizadas por módulo
```
