const fs = require('fs');
let content = fs.readFileSync('app/Http/Controllers/StudySessionController.php', 'utf8');

const oldLogic = `        // Make sure no other running session exists
        $active = StudySession::where('user_id', auth()->id())
            ->where('status', 'running')
            ->first();

        if ($active) {
            return response()->json([
                'status' => 'error',
                'message' => 'Masih ada sesi lain yang berjalan. Hentikan dulu sesi yang sedang aktif.',
            ], 409);
        }`;

const newLogic = `        // Auto-stop currently running session if there is one
        $active = StudySession::where('user_id', auth()->id())
            ->where('status', 'running')
            ->first();

        if ($active) {
            $active->update([
                'ended_at' => now(),
                'duration_seconds' => $active->duration_seconds + $active->started_at->diffInSeconds(now()),
                'status' => 'completed',
            ]);
        }`;

content = content.replace(oldLogic, newLogic);
fs.writeFileSync('app/Http/Controllers/StudySessionController.php', content);
console.log('patched successfully');
