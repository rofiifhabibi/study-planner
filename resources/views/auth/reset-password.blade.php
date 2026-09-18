<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Reset Password - Study Planner</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>*{margin:0;padding:0;box-sizing:border-box}body{font-family:'Inter',system-ui,sans-serif;min-height:100vh;display:flex;align-items:center;justify-content:center;background:#0f0f0f;color:#ececec}.wrap{width:100%;max-width:400px;padding:20px}.logo{text-align:center;margin-bottom:28px}.logo .ico{width:48px;height:48px;border-radius:50%;background:#10a37f;display:inline-flex;align-items:center;justify-content:center;margin-bottom:12px}.logo .ico svg{width:24px;height:24px;color:#fff}.logo h1{font-size:20px;font-weight:600;color:#ececec}.logo p{font-size:13px;color:#525252;margin-top:4px}.fg{margin-bottom:16px}.fg label{display:block;font-size:13px;font-weight:500;color:#999;margin-bottom:6px}.fg input{width:100%;padding:10px 14px;border:1px solid #333;border-radius:10px;font-size:14px;font-family:inherit;background:#1a1a1a;color:#ececec;outline:none;transition:border-color .2s}.fg input:focus{border-color:#10a37f}.btn{width:100%;padding:11px;border:none;border-radius:10px;background:#10a37f;color:#fff;font-size:14px;font-weight:600;font-family:inherit;cursor:pointer;transition:background .15s}.btn:hover{background:#0e8c6c}.alert{padding:10px 14px;border-radius:10px;font-size:13px;margin-bottom:16px}.alert-error{background:#2a1a1a;color:#ef4444;border:1px solid #4a2020}.back{text-align:center;margin-top:20px}.back a{font-size:12px;color:#444;text-decoration:none}</style>
</head>
<body>
    <div class="wrap">
        <div class="logo"><div class="ico"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg></div><h1>Reset Password</h1><p>Enter your new password</p></div>
        @if($errors->any())<div class="alert alert-error">@foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>@endif
        <form method="POST" action="{{ route('password.store') }}">@csrf<input type="hidden" name="token" value="{{ $request->route('token') }}"><div class="fg"><label for="email">Email</label><input id="email" type="email" name="email" value="{{ old('email', $request->email) }}" required autofocus></div><div class="fg"><label for="password">New Password</label><input id="password" type="password" name="password" required autocomplete="new-password"></div><div class="fg"><label for="password_confirmation">Confirm Password</label><input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"></div><button type="submit" class="btn">Reset Password</button></form>
        <div class="back"><a href="{{ route('chat') }}">&larr; Back to Chat</a></div>
    </div>
</body>
</html>
