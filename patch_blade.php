<?php
$file = 'resources/views/emails/daily_agenda.blade.php';
$content = file_get_contents($file);

$oldBlade = <<<HTML
        @if(\$timetables->count() > 0)
            @foreach(\$timetables as \$lesson)
                <div class="card">
                    <div class="subject">{{ \$lesson->subject }}</div>
                    <div class="time">
                        {{ \Carbon\Carbon::parse(\$lesson->start_time)->format('H:i') }} 
                        {{ \$lesson->end_time ? '- ' . \Carbon\Carbon::parse(\$lesson->end_time)->format('H:i') : '' }}
                    </div>
                </div>
            @endforeach
        @else
            <div class="empty">
                Tidak ada jadwal pelajaran untuk @if(\$targetType === "today") hari ini @else besok @endif. Selamat beristirahat!
            </div>
        @endif
        
        <p style="margin-top: 30px; font-size: 12px; color: #aaa;">
            Dikirim otomatis oleh Study Planner App.
        </p>
HTML;

$newBlade = <<<HTML
        <h3 style="color: #5B1744; margin-bottom: 10px; font-size: 16px; border-bottom: 1px solid #eee; padding-bottom: 5px;">🎓 Jadwal Pelajaran</h3>
        @if(\$timetables->count() > 0)
            @foreach(\$timetables as \$lesson)
                <div class="card">
                    <div class="subject">{{ \$lesson->subject }}</div>
                    <div class="time">
                        {{ \Carbon\Carbon::parse(\$lesson->start_time)->format('H:i') }} 
                        {{ \$lesson->end_time ? '- ' . \Carbon\Carbon::parse(\$lesson->end_time)->format('H:i') : '' }}
                    </div>
                </div>
            @endforeach
        @else
            <div class="empty">Tidak ada jadwal pelajaran.</div>
        @endif

        <h3 style="color: #5B1744; margin-top: 25px; margin-bottom: 10px; font-size: 16px; border-bottom: 1px solid #eee; padding-bottom: 5px;">📅 Kegiatan & Acara</h3>
        @if(\$schedules->count() > 0)
            @foreach(\$schedules as \$schedule)
                <div class="card" style="background: #fdfdfd; border-color: #ddd;">
                    <div class="subject" style="color: #444;">{{ \$schedule->title }}</div>
                    <div class="time">
                        {{ \Carbon\Carbon::parse(\$schedule->start_time)->format('H:i') }} 
                        {{ \$schedule->end_time ? '- ' . \Carbon\Carbon::parse(\$schedule->end_time)->format('H:i') : '' }}
                    </div>
                </div>
            @endforeach
        @else
            <div class="empty">Tidak ada agenda kegiatan.</div>
        @endif

        <h3 style="color: #5B1744; margin-top: 25px; margin-bottom: 10px; font-size: 16px; border-bottom: 1px solid #eee; padding-bottom: 5px;">☑️ Tugas Harus Selesai</h3>
        @if(\$tasks->count() > 0)
            @foreach(\$tasks as \$task)
                <div class="card" style="background: #fffafa; border-color: #ffcccc;">
                    <div class="subject" style="color: #cc0000;">{{ \$task->title }}</div>
                    <div class="time">
                        Tenggat Waktu: {{ \$task->due_time ? \Carbon\Carbon::parse(\$task->due_time)->format('H:i') : '23:59' }}
                    </div>
                </div>
            @endforeach
        @else
            <div class="empty">Bebas tugas! Tidak ada tugas yang tenggat waktunya hari ini.</div>
        @endif
        
        <p style="margin-top: 30px; font-size: 12px; color: #aaa;">
            Dikirim otomatis oleh Study Planner App. Semangat terus!
        </p>
HTML;

$content = str_replace($oldBlade, $newBlade, $content);
file_put_contents($file, $content);
echo "Patched blade view.\n";
