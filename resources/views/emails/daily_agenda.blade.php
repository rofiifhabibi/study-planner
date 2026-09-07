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
            Halo! Jangan lupa persiapkan buku untuk besok hari {{ $dayName }}.
        </div>

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
            <div class="empty">
                Tidak ada jadwal pelajaran untuk besok. Selamat beristirahat!
            </div>
        @endif
        
        <p style="margin-top: 30px; font-size: 12px; color: #aaa;">
            Dikirim otomatis oleh Study Planner App.
        </p>
    </div>
</body>
</html>
