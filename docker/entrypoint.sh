#!/bin/bash
set -e

cd /var/www/optik

echo "=== Starting Optik Perkasa ==="

# Tunggu MySQL siap
echo "Waiting for database..."
until php artisan db:monitor --max=1 2>/dev/null; do
    sleep 2
done
echo "Database is ready!"

# Generate APP_KEY jika belum ada
if grep -q "APP_KEY=$" .env || grep -q "APP_KEY=base64:$" .env 2>/dev/null; then
    echo "Generating APP_KEY..."
    php artisan key:generate --force
fi

# Jalankan migration
echo "Running migrations..."
php artisan migrate --force

# Jalankan seeder jika tabel users kosong
USER_COUNT=$(php artisan tinker --execute="echo App\Models\User::count();" 2>/dev/null | tail -1)
if [ "$USER_COUNT" = "0" ] || [ -z "$USER_COUNT" ]; then
    echo "Seeding database..."
    php artisan db:seed --force
fi

# Storage link
php artisan storage:link --force 2>/dev/null || true

# Cache untuk production
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Permissions
chown -R www-data:www-data /var/www/optik/storage
chown -R www-data:www-data /var/www/optik/bootstrap/cache
mkdir -p /var/log/supervisor

echo "=== Optik Perkasa Ready! ==="

# Jalankan Supervisor (Nginx + PHP-FPM)
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
