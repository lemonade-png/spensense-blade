#!/usr/bin/env bash
set -euo pipefail

demo=false
serve=false
for option in "$@"; do
    case "$option" in
        --demo) demo=true ;;
        --serve) serve=true ;;
        --help) echo "Usage: ./scripts/setup-linux.sh [--demo] [--serve]"; exit 0 ;;
        *) echo "Unknown option: $option" >&2; exit 2 ;;
    esac
done

cd "$(dirname -- "${BASH_SOURCE[0]}")/.."
for program in php composer; do
    if ! command -v "$program" >/dev/null; then
        echo "Install $program before running this script. See README.md." >&2
        exit 1
    fi
done

if [[ ! -f .env ]]; then
    (umask 077; cat .env.example > .env)
fi

if grep -Eq '^DB_CONNECTION=sqlite[[:space:]]*$' .env && [[ ! -e database/database.sqlite ]]; then
    (umask 077; touch database/database.sqlite)
fi

composer install --no-interaction
if grep -Eq '^APP_KEY=([[:space:]]*|""|'\'''\'')[[:space:]]*$' .env; then
    php artisan key:generate --no-interaction
fi

php artisan migrate --no-interaction
if "$demo"; then
    php artisan db:seed --no-interaction
fi

php artisan test
if "$serve"; then
    exec php artisan serve --host=127.0.0.1 --port=8001
fi
echo "Setup complete. Run php artisan serve --port=8001 to start SpendSense Blade."
