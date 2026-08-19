<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $appName ?? 'Study Planner' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        *{margin:0;padding:0;box-sizing:border-box}
        body{font-family:'Inter',system-ui,sans-serif;min-height:100vh;display:flex;align-items:center;justify-content:center;background:#0f0f0f;color:#ececec}
        .wrap{width:100%;max-width:400px;padding:20px}
        .logo{text-align:center;margin-bottom:28px}
        .logo .ico{width:48px;height:48px;border-radius:50%;background:#10a37f;display:inline-flex;align-items:center;justify-content:center;margin-bottom:12px}
        .logo .ico svg{width:24px;height:24px;color:#fff}
        .logo h1{font-size:20px;font-weight:600;color:#ececec}
        .logo p{font-size:13px;color:#525252;margin-top:4px}
        .fg{margin-bottom:16px}
        .fg label{display:block;font-size:13px;font-weight:500;color:#999;margin-bottom:6px}
        .fg input{width:100%;padding:10px 14px;border:1px solid #333;border-radius:10px;font-size:14px;font-family:inherit;background:#1a1a1a;color:#ececec;outline:none;transition:border-color .2s}
        .fg input:focus{border-color:#10a37f}
        .fg input::placeholder{color:#555}
        .fc{display:flex;align-items:center;gap:8px;margin-bottom:16px}
        .fc input[type="checkbox"]{width:16px;height:16px;accent-color:#10a37f}
        .fc label{font-size:13px;color:#888}
        .btn{width:100%;padding:11px;border:none;border-radius:10px;background:#10a37f;color:#fff;font-size:14px;font-weight:600;font-family:inherit;cursor:pointer;transition:background .15s}
        .btn:hover{background:#0e8c6c}
        .btn:active{transform:scale(.98)}
        .links{text-align:center;margin-top:20px;font-size:13px;color:#555}
        .links a{color:#10a37f;text-decoration:none;font-weight:500}
        .links a:hover{text-decoration:underline}
        .alert{padding:10px 14px;border-radius:10px;font-size:13px;margin-bottom:16px}
        .alert-success{background:#0a2e1f;color:#34d399;border:1px solid #064e32}
        .alert-error{background:#2a1a1a;color:#ef4444;border:1px solid #4a2020}
        .back{text-align:center;margin-top:20px}
        .back a{font-size:12px;color:#444;text-decoration:none}
        .back a:hover{color:#999}
    </style>
</head>
<body>
    <div class="wrap">
        <div class="logo">
            <div class="ico">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342M6.75 15a.75.75 0 100-1.5.75.75 0 000 1.5zm0 0v-3.675A55.378 55.378 0 0112 8.443m-7.007 11.55A5.981 5.981 0 006.75 15.75v-1.5"/></svg>
            </div>
            <h1>{{ $appName ?? 'Study Planner' }}</h1>
            <p>{{ $subtitle ?? '' }}</p>
        </div>
        @if(session('status'))<div class="alert alert-success">{{ session('status') }}</div>@endif
        @if($errors->any())<div class="alert alert-error">@foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>@endif
        {{ $slot }}
        <div class="back"><a href="{{ route('chat') }}">&larr; Back to Chat</a></div>
    </div>
</body>
</html>
