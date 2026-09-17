<?php
$file = 'app/Http/Controllers/TaskController.php';
$content = file_get_contents($file);

$oldDestroy = <<<PHP
    public function destroy(Task \$task): JsonResponse
    {
        if (\$task->user_id !== auth()->id()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized.',
            ], 403);
        }

        \$task->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Task berhasil dihapus.',
        ]);
    }
PHP;

$newDestroy = <<<PHP
    public function destroy(Task \$task): JsonResponse
    {
        if (\$task->user_id !== auth()->id()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized.',
            ], 403);
        }

        if (!empty(\$task->google_task_id)) {
            \$this->calendarService()->deleteTaskInGoogle(\$task);
        }

        \$task->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Task berhasil dihapus.',
        ]);
    }
PHP;

$content = str_replace($oldDestroy, $newDestroy, $content);
file_put_contents($file, $content);
echo "Patched task destroy.\n";
