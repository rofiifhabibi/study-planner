---
paths:
  - composer.json
---

# General

## php-fpm lacks ext-mbstring; shim lives in bootstrap/mb-polyfill.php
The php-fpm pool serving this app has NO ext-mbstring and cannot be modified without root. symfony/polyfill-mbstring does not implement mb_strcut/mb_strimwidth, which crash email rendering (league/commonmark) in web context. bootstrap/mb-polyfill.php (loaded via composer.json autoload.files) provides guarded fallbacks. Do NOT remove it. Proper long-term fix: `apt install php8.5-mbstring && service php8.5-fpm restart` — the shim is then inert automatically.
