# MiniBooking (Laravel 12)

A minimal booking system built with **Laravel 12**. Customers can search flights & hotels, add one item to a cart, checkout and confirm a booking. Admins can view dashboard and bookings.

## Requirements

* PHP **8.2+**
* Laravel **12**
* Composer
* MySQL / MariaDB (or another supported DB)
* Git

## Quick setup (local)

1. **Clone the repo and install dependencies**

```bash
git clone <repo-url> mini-booking
cd mini-booking
composer install
```

2. **Environment**

```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env` and set your database credentials. Recommended for local jobs:

```env
QUEUE_CONNECTION=database
```

3. **Migrate database**

```bash
php artisan migrate
```

4. **Seed roles & users**

```bash
php artisan db:seed --class=RolesAndUsersSeeder
```

Seeder creates two users (check `database/seeders/RolesAndUsersSeeder.php` for exact credentials). Typical values used in this project:
* Admin → `admin@gmail.com` / `1234`
* Customer → `customer@gmail.com` / `1234`

5. **Load FX mock rates**

Make sure the FX mock file exists at:

```
storage/app/mocks/fx.json
```

Example format used by the app:

```json
{
  "base": "INR",
  "rates": {
    "USD": 0.012
  },
  "updated_at": "2025-09-08T10:00:00+05:30"
}
```

Then run:

```bash
php artisan fx:update
```

This populates the `currencies` table.

6. **(Optional) If you added helper files, refresh autoload**

```bash
composer dump-autoload
```

7. **Start the app**

```bash
php artisan serve
```

Open: `http://127.0.0.1:8000`

## Queue

The app dispatches `SendBookingEmailJob` after booking confirmation. For local development you can use inline execution:


* **For queued processing (database driver)**

```bash
# set in .env
QUEUE_CONNECTION=database

# create queue tables and migrate
php artisan queue:table
php artisan queue:failed-table
php artisan migrate
# ignore if table already exist


# run a worker in a separate terminal
php artisan queue:work --tries=3
```

The job writes a fake email log to `storage/logs/laravel.log`.

## Important routes

* `GET /login` — login
* `POST /login` — submit login
* `POST /logout` — logout
* `GET /search/flights` — flight search (customer only)
* `GET /search/flights/{id}` — flight details (customer only)
* `GET /search/hotels` — hotel search (customer only)
* `GET /search/hotels/{id}` — hotel details (customer only)
* `POST /cart/flights/add` — add flight to cart
* `POST /cart/hotels/add` — add hotel to cart
* `GET /checkout` — cart & checkout (customer only)
* `POST /checkout/confirm` — confirm booking (customer only)
* `GET /bookings` — bookings list (customers see their bookings; admins see all)
* `GET /bookings/{id}` — booking details
* `GET /admin` — admin dashboard (admin only)

Run `php artisan route:list` for a full list.

## Notes & troubleshooting

* If `Call to undefined method App\Models\User::assignRole()` occurs, ensure:
   * Spatie package published and migrated,
   * `HasRoles` trait is present in `App\Models\User`,
   * Spatie tables exist (`roles`, `model_has_roles`, etc.).
* If a `FormRequest` blocks page display (e.g. validating GET requests), ensure its `authorize()` returns `true` and validation is applied only when intended.
* If jobs are not being processed, check `QUEUE_CONNECTION` and whether `php artisan queue:work` is running (for database driver).
* If helper functions are added, add them to `composer.json` autoload `files` and run `composer dump-autoload`.

## Minimal project structure (where to look)

* Controllers: `app/Http/Controllers/`
* Models: `app/Models/`
* Services: `app/Services/`
* Jobs: `app/Jobs/`
* Views: `resources/views/`
* Migrations: `database/migrations/`
* FX mock: `storage/app/mocks/fx.json`