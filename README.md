# بناء وتشغيل الـ containers

docker compose up -d --build

# توليد الـ APP_KEY (أول مرة بس)

docker compose exec app php artisan key:generate

# تشغيل الـ migrations

docker compose exec app php artisan migrate:fresh --seed


# for testing 
docker compose exec app php artisan test tests/Feature