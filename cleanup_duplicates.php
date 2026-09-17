<?php
require 'vendor/autoload.php';
\$app = require_once 'bootstrap/app.php';
\$kernel = \$app->make(Illuminate\Contracts\Console\Kernel::class);
\$kernel->bootstrap();

use App\Models\SchoolTimetable;

\$timetables = SchoolTimetable::all();
\$seen = [];
\$deleted = 0;

foreach (\$timetables as \$t) {
    \$key = \$t->user_id . '_' . \$t->day_of_week . '_' . \$t->start_time . '_' . \$t->subject;
    if (in_array(\$key, \$seen)) {
        \$t->delete();
        \$deleted++;
    } else {
        \$seen[] = \$key;
    }
}
echo "Deleted \$deleted duplicate timetables.\n";
