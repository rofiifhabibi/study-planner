<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: sans-serif; line-height: 1.5; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { font-size: 20px; font-weight: bold; margin-bottom: 20px; color: #5B1744; }
        .card { background: #FAF6F0; border: 1px solid #E7C8DB; padding: 15px; margin-bottom: 10px; border-radius: 8px; }
        .subject { font-weight: bold; font-size: 16px; color: #5B1744; }
        .time { font-size: 14px; color: #666; }
        .empty { padding: 20px; text-align: center; color: #888; background: #f9f9f9; border-radius: 8px; border: 1px dashed #ccc; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            Halo! Berikut adalah jadwal pelajaran untuk @if($targetType === "today") hari ini @else besok hari @endif {{ $dayName }}.
        </div>

        <h3 style="color: #5B1744; margin-bottom: 10px; font-size: 16px; border-bottom: 1px solid #eee; padding-bottom: 5px;">Jadwal Pelajaran</h3>
        @if($timetables->count() > 0)
            @foreach($timetables as $lesson)
                <div class="card">
                    <div class="subject">{{ $lesson->subject }}</div>
                    <div class="time">
                        {{ \Carbon\Carbon::parse($lesson->start_time)->format('H:i') }} 
                        {{ $lesson->end_time ? '- ' . \Carbon\Carbon::parse($lesson->end_time)->format('H:i') : '' }}
                    </div>
                </div>
            @endforeach
        @else
            <div class="empty">Tidak ada jadwal pelajaran.</div>
        @endif

        <h3 style="color: #5B1744; margin-top: 25px; margin-bottom: 10px; font-size: 16px; border-bottom: 1px solid #eee; padding-bottom: 5px;">Kegiatan & Acara</h3>
        @if($schedules->count() > 0)
            @foreach($schedules as $schedule)
                <div class="card" style="background: #fdfdfd; border-color: #ddd;">
                    <div class="subject" style="color: #444;">{{ $schedule->title }}</div>
                    <div class="time">
                        {{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }} 
                        {{ $schedule->end_time ? '- ' . \Carbon\Carbon::parse($schedule->end_time)->format('H:i') : '' }}
                    </div>
                </div>
            @endforeach
        @else
            <div class="empty">Tidak ada agenda kegiatan.</div>
        @endif

        <h3 style="color: #5B1744; margin-top: 25px; margin-bottom: 10px; font-size: 16px; border-bottom: 1px solid #eee; padding-bottom: 5px;">Tugas Harus Selesai</h3>
        @if($tasks->count() > 0)
            @foreach($tasks as $task)
                <div class="card" style="background: #fffafa; border-color: #ffcccc;">
                    <div class="subject" style="color: #cc0000;">{{ $task->title }}</div>
                    <div class="time">
                        Tenggat Waktu: {{ $task->due_time ? \Carbon\Carbon::parse($task->due_time)->format('H:i') : '23:59' }}
                    </div>
                </div>
            @endforeach
        @else
            <div class="empty">Bebas tugas! Tidak ada tugas yang tenggat waktunya hari ini.</div>
        @endif
        
        <p style="margin-top: 30px; font-size: 12px; color: #aaa;">
            Dikirim otomatis oleh Study Planner App. Semangat terus!
        </p>
    </div>
</body>
</html>
