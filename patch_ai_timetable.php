<?php
$content = file_get_contents('app/Http/Controllers/AiIntegrationController.php');

$content = str_replace(
    "use App\Models\Task;",
    "use App\Models\Task;\nuse App\Models\SchoolTimetable;",
    $content
);

$methods = <<<'HTML'
    public function getTimetable(Request $request)
    {
        $user = $this->authenticateAiRequest($request);
        
        $dayOfWeek = $request->query('day_of_week'); // optional: 1-7
        
        $query = SchoolTimetable::where('user_id', $user->id);
        
        if ($dayOfWeek !== null) {
            $query->where('day_of_week', $dayOfWeek);
        }
        
        $timetables = $query->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get();
            
        // Map day_of_week to string for AI context
        $days = [0=>'Minggu',1=>'Senin',2=>'Selasa',3=>'Rabu',4=>'Kamis',5=>'Jumat',6=>'Sabtu'];
        
        $timetables->transform(function($item) use ($days) {
            $item->day_name = $days[$item->day_of_week] ?? 'Unknown';
            return $item;
        });

        return response()->json([
            'status' => 'success',
            'data' => $timetables
        ]);
    }
HTML;

// Insert before the last closing brace
$content = preg_replace('/}\s*$/', "\n$methods\n}", $content);
file_put_contents('app/Http/Controllers/AiIntegrationController.php', $content);
