---
paths:
  - composer.json
  - '*.md'
  - .env
---

# General

## php-fpm lacks ext-mbstring; shim lives in bootstrap/mb-polyfill.php
The php-fpm pool serving this app has NO ext-mbstring and cannot be modified without root. symfony/polyfill-mbstring does not implement mb_strcut/mb_strimwidth, which crash email rendering (league/commonmark) in web context. bootstrap/mb-polyfill.php (loaded via composer.json autoload.files) provides guarded fallbacks. Do NOT remove it. Proper long-term fix: `apt install php8.5-mbstring && service php8.5-fpm restart` — the shim is then inert automatically.

## php-fpm 8.4 needs DOM (xml) for mail/register
The site is served by php-fpm 8.4 (Nginx study-planner vhost -> unix:/run/php/php8.4-fpm.sock), NOT the CLI php (8.5). If Laravel Mail/css-to-inline-styles errors with 'Class "DOMDocument" not found' during register (email verification) or any mail render, php8.4 needs the xml/dom extension: `apt-get install php8.4-xml` then `systemctl restart php8.4-fpm`. CLI php having DOM doesn't mean fpm has it.

## Email verification now sends; check spam
The verification email pipeline is fully functional: DOMDocument fixed (php8.4-xml installed), Gmail SMTP auth returns 235, full send returns 250 2.0.0 OK, and `sendEmailVerificationNotification()` to an unverified user completes with no exception. Since Aug 2026 there are no new mail errors in storage/logs/laravel.log. If a user reports the verification email "not arriving", verify it isn't landing in spam and they registered after the DOM fix (last error 00:47:38 on that day) — auth/SMTP are confirmed working.
