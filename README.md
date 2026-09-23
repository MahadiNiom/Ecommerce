# Matir Shaad

An e-commerce application built with Laravel 12 for selling fresh, hand-picked goods. It features a customer-facing storefront (catalog, cart, wishlist, checkout, order history) and a full admin backend with granular, role-based access control.

## Tech Stack

- **Backend:** Laravel 12, PHP 8.2
- **Database:** SQLite (default)
- **Auth & RBAC:** Laravel Breeze + [spatie/laravel-permission](https://spatie.be/docs/laravel-permission/v6) with granular permissions
- **Frontend:** Blade + Tailwind CSS 3 + Alpine.js, bundled with Vite
- **Testing:** Pest (PHPUnit under the hood), Laravel Pint for formatting

## Features

### Storefront
- Public shop with category, brand, and tag filtering
- Product detail pages with variant support (combination pricing, stock levels)
- Cart and wishlist (guest + authenticated)
- Checkout and order placement
- Personal order history and order status tracking

### Admin (role-gated)
- Product, category, brand, and tag CRUD
- Variant types and variant options per product
- Product variant combinations (auto-generated from selected options)
- Order management and status updates
- User management and role assignment
- Role management with per-permission selection
- Permission CRUD

### Access Control
Granular, per-action permissions replace the older coarse "module" permissions. They live as named constants in `app/Support/Permissions.php` and are grouped into three modules:

| Module | Permissions |
| --- | --- |
| Catalog | `view / create / edit / delete` x `products`, `categories`, `brands`, `tags` |
| Orders | `view orders`, `update order status` |
| Access control | `view / create / edit / delete` x `roles`, `permissions`; `view users`, `assign roles` |

- Routes are protected per action via `permission:` middleware (e.g. `products.store` requires `create products`), and UI links/buttons are hidden using `Permissions::canAny()`.
- A user's effective permissions are the **union** of all their assigned roles.

## Requirements

- PHP 8.2+
- Composer
- Node.js 20+ (for frontend build tooling)

## Installation

```bash
composer install
npm install

# Environment setup (creates .env, generates app key)
@php -r "file_exists('.env') || copy('.env.example', '.env');"
php artisan key:generate

# Database (SQLite)
php artisan migrate --seed
```

`setup` is also available as a Composer script (`composer run setup`) that runs install, env setup, migrations, and the frontend build.

### Demo data & default login

Running `php artisan db:seed` (or `migrate --seed`) creates:

- Roles: `admin` (all 28 permissions) and `user`
- Demo admin: `admin@example.com` / `password`
- A small catalog: a variant-enabled "Classic T-Shirt" and a variant-less "Canvas Tote Bag", with a "Clothing" category, "Acme Apparel" brand, and a couple of tags.

To start from scratch: `php artisan migrate:fresh --seed`.

## Development

```bash
php artisan serve        # app server
npm run dev              # Vite dev server (HMR)
composer run dev         # server + queue + pail logs + Vite concurrently
```

If a frontend change doesn't appear in the UI, run `npm run build` (or keep `npm run dev` running).

## Testing

```bash
php artisan test
# or with the compact reporter:
php artisan test --compact

# Formatting:
vendor/bin/pint
```

The suite covers storefront behavior, catalog CRUD, order management, product-variant combinations, and role/permission access control (including multi-role union access).

## Project Structure

```
app/
  Http/Controllers/       # Web controllers (incl. admin-prefixed order management)
  Models/                 # Eloquent models
  Support/Permissions.php # Single source of truth for permission names + canAny()
database/
  migrations/             # Schema (permission tables via spatie/laravel-permission)
  seeders/                # Roles + granular permissions, demo catalog
resources/views/          # Blade + Tailwind/Alpine UI
routes/web.php            # Web routes with per-action permission middleware
tests/Feature/            # Pest feature tests
```