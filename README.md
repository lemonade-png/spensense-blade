# SpendSense Blade

SpendSense tracks personal income, expenses, and monthly budgets in PKR. This edition uses Laravel Blade templates, CSS, and plain JavaScript. The default database is SQLite, stored in a local file.

## Linux setup

Install PHP 8.5 and Composer. On Arch, use:

```bash
sudo pacman -Syu --needed php php-sqlite composer
```

Check the active configuration with `php --ini`. Enable `curl`, `iconv`, and `pdo_sqlite` in `php.ini` if those extensions are disabled. Verify them with `php -m`.

From the project folder, run:

```bash
./scripts/setup-linux.sh --demo --serve
```

## Windows setup

Install PHP 8.5 and Composer, add them to PATH, and open a new PowerShell terminal. Run `php --ini` to find the active PHP configuration. If there is none, copy `php.ini-development` in the PHP directory to `php.ini`. In that file, set `extension_dir = "ext"` and enable `curl`, `mbstring`, `openssl`, `pdo_sqlite`, and `zip` if they are disabled. Use `php -m` to confirm `iconv` is available as well.

From the project folder, run:

```powershell
powershell.exe -NoProfile -ExecutionPolicy Bypass -File .\scripts\setup-windows.ps1 -Demo -Serve
```

## What setup does

Both scripts install the locked Composer dependencies, create the local configuration and SQLite file when needed, generate the application key if empty, migrate the database, and run the tests. With the demo and serve flags, they also seed sample data and start Laravel.

Open [SpendSense Blade](http://127.0.0.1:8001). Sign in as `demo@spendsense.test` with `SpendSenseDemo2026!` to see sample records. For a new account, find the email verification link in `storage/logs/laravel.log`.

To restart an existing installation, run `php artisan serve --port=8001`. To use MariaDB instead of SQLite, create a separate `spendsense_blade` database and account, enable `pdo_mysql`, and set the database fields in `.env` before migrating.

## Project files

- `resources/views` contains the HTML templates and forms.
- `public/spendsense.css` and `public/spendsense.js` contain the frontend styles and behavior.
- `routes`, `app/Http/Controllers`, and `app/Models` contain the Laravel application logic.

Run the feature tests with `php artisan test`.
