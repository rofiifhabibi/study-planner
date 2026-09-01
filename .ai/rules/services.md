---
paths:
  - app/Services/GoogleCalendarService.php
  - 'app/Services/*.php'
---

# Services

## Google Calendar OAuth redirect terpisah dari login
GoogleCalendarService memakai redirect URI route('google.calendar.callback') (https://studyplanner.web.id/google/calendar/callback), bukan config('services.google.redirect') yang dipakai login (Socialite /auth/google/callback). Keduanya berbagi client_id yang sama. Saat connect/sync gagal, pastikan URI https://studyplanner.web.id/google/calendar/callback TERDAFTAR di Google Cloud Console > Credentials > OAuth, dan batasi scopes sesuai kebutuhan.

## Gunakan timezone kalender terpisah (bukan app.timezone UTC)
config('app.timezone') adalah UTC. Saat membuat/update event Google, JANGAN pakai config('app.timezone') sebagai timeZone event — akan membuat tanggal/jam bergeser (misal 31 Aug 23:08 WIB tersimpan jadi 1 Sep 06:08). Pakai config('services.google.calendar_timezone', 'Asia/Jakarta') via helper calendarTimezone(). Event lama yang ber-tanggal salah hanya diperbaiki saat jadwal di-update (branch update) atau tekan Sync di Integrations.

## Google Tasks task list must use real list ID, not 'default'
Google Tasks API rejects task list id 'default' with "Invalid task list ID". Use config('services.google.tasks_task_list') (real list id in GOOGLE_TASKS_TASK_LIST env, default MDYyODYwMTQ5Mzc0OTM1MDUzNzM6MDow). The GoogleCalendarService::taskListId() helper resolves this for upsert/delete.

## Google Calendar reminders + recurring event RRULE
Every Google Calendar event gets reminders useDefault=false with popup 60 & 10 minutes (alarm). Recurring schedules store frequency/interval/days/until/count on schedules table; GoogleCalendarService::recurrenceRule() builds the RRULE string and sets it via Event::setRecurrence (weekly uses BYDAY = stored uppercase comma list). Only NULL frequency skips recurrence.

## Tasks due_date nullable + resolve service via container for testability
tasks.due_date is now nullable because Google Tasks tasks may have no due date. pullTasksFromGoogle imports Google Tasks -> tasks table (deduped by google_task_id, never re-inserts). Also: controllers must resolve GoogleCalendarService via app()/service() helper (container), NOT `new GoogleCalendarService(...)`, so feature-test mocks actually take effect.
