<?php
$file = 'app/Http/Controllers/TaskStepController.php';
$content = file_get_contents($file);

$oldLogic = <<<PHP
        \$step->update([
            'is_completed' => \$validated['is_completed'],
        ]);
        
        // Auto-complete task if all steps are completed? Optional, but good UX.
        // Let's not automatically complete the task to give users control,
        // or we can just let them complete the task manually.

        return response()->json([
            'status' => 'success',
            'step' => \$step,
        ]);
PHP;

$newLogic = <<<PHP
        \$step->update([
            'is_completed' => \$validated['is_completed'],
        ]);
        
        // Auto-complete task if all steps are completed
        \$totalSteps = \$task->steps()->count();
        \$completedSteps = \$task->steps()->where('is_completed', true)->count();
        
        \$taskCompleted = false;
        if (\$totalSteps > 0 && \$totalSteps === \$completedSteps && \$task->status !== 'completed') {
            \$task->update(['status' => 'completed']);
            \$taskCompleted = true;
            
            // Sync to Google Tasks
            try {
                \$service = app(\\App\\Services\\GoogleCalendarService::class, ['user' => auth()->user()]);
                \$service->syncTask(\$task);
            } catch (\\Exception \$e) {
                \\Illuminate\\Support\\Facades\\Log::error('Auto-sync task failed', ['error' => \$e->getMessage()]);
            }
        } elseif (\$task->status === 'completed' && \$completedSteps < \$totalSteps) {
            // Un-complete the task if a step is unchecked
            \$task->update(['status' => 'pending']);
            \$taskCompleted = false;
            
            // Sync to Google Tasks
            try {
                \$service = app(\\App\\Services\\GoogleCalendarService::class, ['user' => auth()->user()]);
                \$service->syncTask(\$task);
            } catch (\\Exception \$e) {
                \\Illuminate\\Support\\Facades\\Log::error('Auto-sync task failed', ['error' => \$e->getMessage()]);
            }
        }

        return response()->json([
            'status' => 'success',
            'step' => \$step,
            'task_status' => \$task->status,
        ]);
PHP;

$content = str_replace($oldLogic, $newLogic, $content);
file_put_contents($file, $content);
echo "Patched TaskStepController.\n";
