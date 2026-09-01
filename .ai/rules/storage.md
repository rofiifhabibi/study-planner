---
paths:
  - 'storage/**'
---

# Storage

## Keep storage writable by www-data (touch Utime errors)
Never run `php artisan tinker` / artisan CLI commands that write compiled views (view:cache, view renders, tinker routes) as root, or storage/framework/views files become owned by root and php-fpm (www-data) fails with `touch(): Utime failed: Operation not permitted`. If it happens: `chown -R www-data:www-data storage bootstrap/cache` and clear+re-cache views as www-data (`sudo -u www-data php artisan view:cache`).
