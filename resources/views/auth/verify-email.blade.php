<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Verify Email - Study Planner</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>*{margin:0;padding:0;box-sizing:border-box}body{font-family:'Inter',system-ui,sans-serif;min-height:100vh;display:flex;align-items:center;justify-content:center;background:#0f0f0f;color:#ececec}.wrap{width:100%;max-width:400px;padding:20px}.logo{text-align:center;margin-bottom:28px}.logo .ico{width:48px;height:48px;border-radius:50%;background:#10a37f;display:inline-flex;align-items:center;justify-content:center;margin-bottom:12px}.logo .ico svg{width:24px;height:24px;color:#fff}.logo h1{font-size:20px;font-weight:600;color:#ececec}.logo p{font-size:13px;color:#525252;margin-top:4px;max-width:280px;margin-left:auto;margin-right:auto}.btn{width:100%;padding:11px;border:none;border-radius:10px;background:#10a37f;color:#fff;font-size:14px;font-weight:600;font-family:inherit;cursor:pointer;transition:background .15s;margin-bottom:12px}.btn:hover{background:#0e8c6c}.links{text-align:center;margin-top:12px;font-size:13px}.links a{color:#10a37f;text-decoration:none;font-weight:500}.alert{padding:10px 14px;border-radius:10px;font-size:13px;margin-bottom:16px}.alert-success{background:#0a2e1f;color:#34d399;border:1px solid #064e32}.back{text-align:center;margin-top:20px}.back a{font-size:12px;color:#444;text-decoration:none}</style>
</head>
<body>
    <div class="wrap">
        <div class="logo"><div class="ico"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg></div><h1>Verify Email</h1><p>We've sent a verification link to your email.</p></div>
        @if(session('status'))<div class="alert alert-success">{{ session('status') }}</div>@endif
        <form method="POST" action="{{ route('verification.send') }}">@csrf<button type="submit" class="btn">Resend Verification Email</button></form>
        <div class="links"><a href="{{ route('logout') }}" onclick="event.preventDefault();document.getElementById('logout-form').submit();">Sign out</a></div>
        <form id="logout-form" method="POST" action="{{ route('logout') }}" style="display:none">@csrf @method('DELETE')</form>
        <div class="back"><a href="{{ route('chat') }}">&larr; Back to Chat</a></div>
    </div>
</body>
</html>
