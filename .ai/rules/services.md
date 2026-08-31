---
paths:
  - app/Services/GoogleCalendarService.php
---

# Services

## Google Calendar OAuth redirect terpisah dari login
GoogleCalendarService memakai redirect URI route('google.calendar.callback') (https://studyplanner.web.id/google/calendar/callback), bukan config('services.google.redirect') yang dipakai login (Socialite /auth/google/callback). Keduanya berbagi client_id yang sama. Saat connect/sync gagal, pastikan URI https://studyplanner.web.id/google/calendar/callback TERDAFTAR di Google Cloud Console > Credentials > OAuth, dan batasi scopes sesuai kebutuhan.

## Gunakan timezone kalender terpisah (bukan app.timezone UTC)
config('app.timezone') adalah UTC. Saat membuat/update event Google, JANGAN pakai config('app.timezone') sebagai timeZone event — akan membuat tanggal/jam bergeser (misal 31 Aug 23:08 WIB tersimpan jadi 1 Sep 06:08). Pakai config('services.google.calendar_timezone', 'Asia/Jakarta') via helper calendarTimezone(). Event lama yang ber-tanggal salah hanya diperbaiki saat jadwal di-update (branch update) atau tekan Sync di Integrations.
