<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$u = App\Models\User::where('email', 'muhammadrofiif38@gmail.com')->first();
$svc = new \App\Services\GoogleCalendarService($u);

$reflection = new ReflectionClass($svc);
$prop = $reflection->getProperty('client');
$client = $prop->getValue($svc);

$calendar = new \Google\Service\Calendar($client);

$events = $calendar->events->listEvents('primary', [
    'timeMin' => \Carbon\Carbon::today()->subDays(5)->toRfc3339String(),
    'maxResults' => 200,
]);

$deleted = 0;
foreach ($events->getItems() as $event) {
    $title = $event->getSummary();
    if ($title && str_starts_with($title, '[Pelajaran]')) {
        try {
            $calendar->events->delete('primary', $event->getId());
            $deleted++;
        } catch (\Exception $e) {
        }
    }
}
echo "Deleted $deleted orphaned [Pelajaran] events.\n";

$timetables = \App\Models\SchoolTimetable::where('user_id', $u->id)->get();
$synced = 0;
foreach ($timetables as $t) {
    $t->google_event_id = null;
    $t->save();
    $svc->syncSchoolTimetable($t);
    $synced++;
}
echo "Re-synced $synced valid SchoolTimetables.\n";
