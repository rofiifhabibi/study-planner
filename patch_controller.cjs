const fs = require('fs');
let content = fs.readFileSync('app/Http/Controllers/StudySessionController.php', 'utf8');

const oldLogic = `    public function store(Request $request): JsonResponse
    {
        $active = StudySession::where('user_id', auth()->id())
            ->where('status', 'running')
            ->first();

        if ($active) {
            return response()->json([
                'status' => 'error',
                'message' => 'Ada sesi belajar yang masih berjalan. Selesaikan atau hentikan terlebih dahulu.',
                'active_session' => $active,
            ], 409);
        }
`;

const newLogic = `    public function store(Request $request): JsonResponse
    {
        $active = StudySession::where('user_id', auth()->id())
            ->where('status', 'running')
            ->first();

        if ($active) {
            $active->update([
                'ended_at' => now(),
                'duration_seconds' => $active->duration_seconds + $active->started_at->diffInSeconds(now()),
                'status' => 'completed',
            ]);
        }
`;

content = content.replace(oldLogic, newLogic);
fs.writeFileSync('app/Http/Controllers/StudySessionController.php', content);
console.log('patched successfully');
