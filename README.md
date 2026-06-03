# BeninMarket API

API Laravel de la marketplace BeninMarket.

## Stack

- Laravel 12
- PHP 8.2+
- Laravel Sanctum
- Cloudinary Laravel

## Demarrage

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

API locale: `http://localhost:8000/api`.

## Routes principales

- Auth: `POST /api/register`, `POST /api/login`, `POST /api/logout`
- Catalogue: `GET /api/products`, `GET /api/products/{idOrSlug}`
- Boutiques: `GET /api/shops`, `POST /api/shops`
- Commandes: `POST /api/checkout`, `GET /api/orders`
- Vendeur: `/api/vendor/*`
- Admin web: `/admin`

## Securite

- Les anciennes routes temporaires de creation admin, migration et seed production ont ete supprimees.
- Le checkout recalcule les prix et les frais cote serveur.
- Les operations de commande utilisent une transaction et verrouillent les produits pendant la verification du stock.
- Les mots de passe admin des seeders passent par `ADMIN_SEEDER_PASSWORD` et `DEMO_ADMIN_PASSWORD`.

## Tests

```bash
composer test
```
