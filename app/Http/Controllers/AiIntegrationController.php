<?php

namespace App\Http\Controllers;

use App\Models\ChatSession;
use App\Models\Schedule;
use App\Models\Task;
use App\Models\SchoolTimetable;
use App\Services\GoogleCalendarService;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class AiIntegrationController extends Controller
{
    /**
     * Authenticate the AI request and get the user
     */
    private function authenticateAiRequest(Request $request)
    {
        // Simple security: check the secret key
        $secretKey = config('services.n8n.secret_key');
        if ($request->header('X-API-KEY') !== $secretKey) {
            abort(401, 'Unauthorized AI Request');
        }

        // The AI must pass the session_id so we know WHICH user is asking
        $sessionId = $request->header('X-SESSION-ID');
        if (!$sessionId) {
            abort(400, 'Missing Session ID');
        }

        // Example session ID: "user_1_session_abc123"
        // We can parse it, or better, query ChatSession
        // Wait, ChatSession doesn't have a direct 'user_X_session_Y' field.
        // It has user_id and session_key. 
        if (preg_match('/user_(\d+)_session_(.+)/', $sessionId, $matches)) {
            $userId = $matches[1];
            $sessionKey = $matches[2];
            
            $session = ChatSession::where('user_id', $userId)
                ->where('session_key', $sessionKey)
                ->first();
                
            if ($session && $session->user) {
                return $session->user;
            }
        }
        
        abort(404, 'Session not found');
    }

    public function getCalendar(Request $request)
    {
        $user = $this->authenticateAiRequest($request);
        
        // Return upcoming schedules for the user
        $schedules = Schedule::where('user_id', $user->id)
            ->whereDate('date', '>=', today())
            ->orderBy('date')
            ->orderBy('start_time')
            ->limit(20)
            ->get();
            
        return response()->json([
            'status' => 'success',
            'data' => $schedules
        ]);
    }

    public function createCalendarEvent(Request $request)
    {
        $user = $this->authenticateAiRequest($request);
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'subject' => 'nullable|string|max:255',
            'date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
        ]);
        
                $schedule = Schedule::create([
            'user_id' => $user->id,
            ...$validated,
            'status' => 'pending'
        ]);
        
        try {
            $service = app(GoogleCalendarService::class, ['user' => $user]);
            $service->syncSchedule($schedule);
        } catch (\Exception $e) {
            Log::error('AI Failed to sync Schedule to Google', ['schedule_id' => $schedule->id, 'error' => $e->getMessage()]);
        }
        
        return response()->json([
            'status' => 'success',
            'message' => 'Event created successfully',
            'data' => $schedule
        ]);
    }
    
    public function getTasks(Request $request)
    {
        $user = $this->authenticateAiRequest($request);
        
        // Return pending tasks
        $tasks = Task::where('user_id', $user->id)
            ->where('status', 'pending')
            ->orderBy('due_date')
            ->limit(20)
            ->get();
            
        return response()->json([
            'status' => 'success',
            'data' => $tasks
        ]);
    }
    
    public function createTask(Request $request)
    {
        $user = $this->authenticateAiRequest($request);
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
            'due_time' => 'nullable|date_format:H:i',
            'category' => 'required|in:school,project,study,personal',
            'priority' => 'required|in:low,medium,high',
        ]);
        
                $task = Task::create([
            'user_id' => $user->id,
            ...$validated,
            'status' => 'pending'
        ]);
        
        try {
            $service = app(GoogleCalendarService::class, ['user' => $user]);
            $service->syncTask($task);
        } catch (\Exception $e) {
            Log::error('AI Failed to sync Task to Google', ['task_id' => $task->id, 'error' => $e->getMessage()]);
        }
        
        return response()->json([
            'status' => 'success',
            'message' => 'Task created successfully',
            'data' => $task
        ]);
    }

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
}