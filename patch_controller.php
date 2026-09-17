<?php
\$file = 'app/Http/Controllers/StudySessionController.php';
\$content = file_get_contents(\$file);

\$oldLogic = <<<PHP
    public function store(Request \$request): JsonResponse
    {
        \$active = StudySession::where('user_id', auth()->id())
            ->where('status', 'running')
            ->first();

        if (\$active) {
            return response()->json([
                'status' => 'error',
                'message' => 'Ada sesi belajar yang masih berjalan. Selesaikan atau hentikan terlebih dahulu.',
                'active_session' => \$active,
            ], 409);
        }
PHP;

\$newLogic = <<<PHP
    public function store(Request \$request): JsonResponse
    {
        \$active = StudySession::where('user_id', auth()->id())
            ->where('status', 'running')
            ->first();

        // Auto-stop currently active session if they start a new one
        if (\$active) {
            \$active->update([
                'ended_at' => now(),
                'duration_seconds' => \$active->duration_seconds + \$active->started_at->diffInSeconds(now()),
                'status' => 'completed',
            ]);
        }
PHP;

\$content = str_replace(\$oldLogic, \$newLogic, \$content);
file_put_contents(\$file, \$content);
echo "Patched controller to auto-stop active session.\n";
