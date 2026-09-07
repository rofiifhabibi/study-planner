<?php

use App\Models\Schedule;
use App\Models\User;
use App\Services\GoogleCalendarService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use ReflectionMethod;

uses(RefreshDatabase::class);

afterEach(function () {
    Carbon::setTestNow();
});

function buildRemindersFor(array $attributes, ?Carbon $now = null): array
{
    Carbon::setTestNow($now ?? new Carbon('2026-09-01 08:00:00'));

    $user = User::factory()->create();
    $service = new GoogleCalendarService($user);
    $schedule = Schedule::factory()->create($attributes);

    $method = new ReflectionMethod(GoogleCalendarService::class, 'buildReminders');
    $method->setAccessible(true);

    return $method->invoke($service, $schedule);
}

function minutesOf(array $reminders): array
{
    $minutes = array_map(fn ($r) => $r['minutes'], $reminders);
    $unique = array_values(array_unique($minutes));
    sort($unique);

    return $unique;
}

function reminderMinutes(array $reminders, string $method): array
{
    $minutes = array_map(fn ($r) => $r['minutes'], array_filter($reminders, fn ($r) => $r['method'] === $method));
    $unique = array_values(array_unique($minutes));
    sort($unique);

    return $unique;
}

it('keeps 60 and 10 minute reminders on a long schedule', function () {
    $reminders = buildRemindersFor([
        'date' => '2026-09-01',
        'start_time' => '09:00',
        'end_time' => '10:30',
    ]);

    expect(minutesOf($reminders))->toBe([10, 60]);
});

it('drops the 60 minute reminder when the schedule is shorter than one hour', function () {
    $reminders = buildRemindersFor([
        'date' => '2026-09-01',
        'start_time' => '09:00',
        'end_time' => '09:30',
    ]);

    expect(minutesOf($reminders))->toBe([10]);
});

it('uses a valid reminder for a very short schedule', function () {
    $reminders = buildRemindersFor([
        'date' => '2026-09-01',
        'start_time' => '09:00',
        'end_time' => '09:01',
    ]);

    $minutes = minutesOf($reminders);

    expect($minutes)->not->toBeEmpty()
        ->and($minutes)->each->toBeLessThanOrEqual(1)
        ->and($minutes)->toContain(1);
});

it('uses a reminder equal to the duration for a two minute schedule', function () {
    $reminders = buildRemindersFor([
        'date' => '2026-09-01',
        'start_time' => '09:00',
        'end_time' => '09:02',
    ]);

    expect(minutesOf($reminders))->toBe([2]);
});

it('falls back to a zero minute reminder when duration cannot be computed', function () {
    $reminders = buildRemindersFor([
        'date' => '2026-09-01',
        'start_time' => '09:00',
        'end_time' => '09:00',
    ]);

    expect(minutesOf($reminders))->toBe([0]);
});

it('every reminder is a popup or email method with both present', function () {
    $reminders = buildRemindersFor([
        'date' => '2026-09-01',
        'start_time' => '09:00',
        'end_time' => '09:03',
    ]);

    $methods = array_map(fn ($r) => $r['method'], $reminders);

    expect($methods)->each->toBeIn(['popup', 'email'])
        ->and($methods)->toContain('popup')
        ->and($methods)->toContain('email')
        ->and(reminderMinutes($reminders, 'email'))->toBe(reminderMinutes($reminders, 'popup'));
});

it('includes a 20:00 evening reminder for lesson schedules', function () {
    $reminders = buildRemindersFor([
        'date' => '2026-09-01',
        'start_time' => '09:00',
        'end_time' => '10:00',
        'is_lesson' => true,
    ]);

    $minutes = minutesOf($reminders);

    expect($minutes)->toContain(780);
});

it('does not add evening reminder for non-lesson schedules', function () {
    $reminders = buildRemindersFor([
        'date' => '2026-09-01',
        'start_time' => '09:00',
        'end_time' => '10:00',
        'is_lesson' => false,
    ]);

    expect(minutesOf($reminders))->not->toContain(780);
});

it('always includes duration reminder alongside evening reminder for lessons', function () {
    $reminders = buildRemindersFor([
        'date' => '2026-09-01',
        'start_time' => '09:00',
        'end_time' => '10:30',
        'is_lesson' => true,
    ]);

    $minutes = minutesOf($reminders);

    expect($minutes)->toContain(10)
        ->and($minutes)->toContain(60)
        ->and($minutes)->toContain(780);
});

it('sends an immediate reminder when the schedule starts within minutes', function () {
    $reminders = buildRemindersFor([
        'date' => '2026-09-01',
        'start_time' => '08:05',
        'end_time' => '08:15',
    ], new Carbon('2026-09-01 08:00:00'));

    expect(minutesOf($reminders))->toBe([0]);
});

it('sends an immediate reminder when the schedule starts within a minute', function () {
    $reminders = buildRemindersFor([
        'date' => '2026-09-01',
        'start_time' => '08:01',
        'end_time' => '08:10',
    ], new Carbon('2026-09-01 08:00:00'));

    expect(minutesOf($reminders))->toBe([0]);
});
