<?php

use App\Models\SchoolTimetable;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('authenticated user can view timetable page', function () {
    $user = User::factory()->create();
    SchoolTimetable::factory()->create([
        'user_id' => $user->id,
        'day_of_week' => 1,
        'subject' => 'Matematika Diskrit',
        'start_time' => '08:00',
        'end_time' => '09:30',
    ]);

    $response = $this->actingAs($user)->get(route('timetable.index'));

    $response->assertOk();
    $response->assertSee('Class Schedule');
    $response->assertSee('Matematika Diskrit');
    $response->assertSee('08:00');
    $response->assertSee('09:30');
});

test('user can add a new timetable class', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('timetable.store'), [
        'day_of_week' => 2,
        'subject' => 'Fisika Dasar',
        'start_time' => '07:30',
        'end_time' => '09:00',
    ]);

    $response->assertSessionHas('success', 'Jadwal pelajaran berhasil ditambahkan.');
    $this->assertDatabaseHas('school_timetables', [
        'user_id' => $user->id,
        'day_of_week' => 2,
        'subject' => 'Fisika Dasar',
        'start_time' => '07:30:00',
        'end_time' => '09:00:00',
    ]);
});

test('user can add timetable when time is submitted with seconds format', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('timetable.store'), [
        'day_of_week' => 3,
        'subject' => 'Pemrograman Web',
        'start_time' => '10:00:00',
        'end_time' => '12:00:00',
    ]);

    $response->assertSessionHas('success');
    $this->assertDatabaseHas('school_timetables', [
        'user_id' => $user->id,
        'subject' => 'Pemrograman Web',
        'start_time' => '10:00:00',
    ]);
});

test('user can delete their timetable class', function () {
    $user = User::factory()->create();
    $timetable = SchoolTimetable::factory()->create([
        'user_id' => $user->id,
        'subject' => 'Kimia Organik',
    ]);

    $response = $this->actingAs($user)->delete(route('timetable.destroy', $timetable));

    $response->assertSessionHas('success', 'Jadwal pelajaran berhasil dihapus.');
    $this->assertDatabaseMissing('school_timetables', [
        'id' => $timetable->id,
    ]);
});

test('user cannot delete other user timetable class', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $timetable = SchoolTimetable::factory()->create([
        'user_id' => $user1->id,
    ]);

    $response = $this->actingAs($user2)->delete(route('timetable.destroy', $timetable));

    $response->assertForbidden();
    $this->assertDatabaseHas('school_timetables', [
        'id' => $timetable->id,
    ]);
});
