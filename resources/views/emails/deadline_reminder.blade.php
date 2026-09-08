<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: sans-serif; line-height: 1.5; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ffeeba; border-radius: 8px; background: #fffdf5; }
        .header { font-size: 20px; font-weight: bold; margin-bottom: 20px; color: #856404; text-align: center; }
        .card { background: #fff; border-left: 4px solid #dc3545; padding: 15px; margin-bottom: 10px; border-radius: 4px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .subject { font-weight: bold; font-size: 16px; color: #dc3545; }
        .desc { font-size: 13px; color: #666; margin-top: 5px; }
        .time { font-size: 14px; color: #856404; margin-top: 8px; font-weight: bold; }
        .btn { display: inline-block; margin-top: 20px; padding: 10px 20px; background: #dc3545; color: #fff; text-decoration: none; border-radius: 5px; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            Pengingat Deadline Tugas
        </div>
        
        <p style="text-align: center; margin-bottom: 25px;">
            Halo <strong>{{ $user->name }}</strong>, jangan lupa ada <strong>{{ $tasks->count() }} tugas</strong> yang sudah sangat dekat dengan tenggat waktunya. Segera selesaikan ya!
        </p>

        @foreach($tasks as $task)
            <div class="card">
                <div class="subject">{{ $task->title }}</div>
                @if($task->description)
                <div class="desc">{{ \Illuminate\Support\Str::limit($task->description, 100) }}</div>
                @endif
                <div class="time">
                    Tenggat: {{ \Carbon\Carbon::parse($task->due_date)->translatedFormat('l, d F Y') }} 
                    {{ $task->due_time ? \Carbon\Carbon::parse($task->due_time)->format('H:i') : '' }}
                </div>
            </div>
        @endforeach
        
        <div style="text-align: center; margin-top: 30px;">
            <a href="{{ config('app.url') }}/dashboard" class="btn">Buka Study Planner</a>
        </div>
        
        <p style="margin-top: 30px; font-size: 12px; color: #aaa; text-align: center;">
            Dikirim otomatis oleh bot pengingat Study Planner.
        </p>
    </div>
</body>
</html>
