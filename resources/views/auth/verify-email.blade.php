<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Email - Study Planner</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-[#FAF8F5] min-h-screen flex items-center justify-center p-4">

    <div class="w-full max-w-[420px] bg-white rounded-[32px] border border-[#F0EAE1] p-8 shadow-sm">

        <!-- Logo Header -->
        <div class="flex justify-center mb-6">
            <div class="w-16 h-16 bg-[#4A1B36] rounded-full flex items-center justify-center text-white text-2xl font-bold">
                S
            </div>
        </div>

        <!-- Title -->
        <div class="text-center mb-6">
            <h1 class="text-2xl font-bold text-[#4A1B36] mb-2">Verify Your Email</h1>
            <p class="text-sm text-gray-500 leading-relaxed">
                Thanks for signing up! We've sent a verification link to
                <span class="font-semibold text-gray-700 break-all">{{ auth()->user()->email }}</span>.
                Please click it to activate your account.
            </p>
        </div>

        @if (session('status') === 'verification-link-sent')
            <div class="flex items-start gap-2 bg-green-50 border border-green-200 text-green-700 text-xs rounded-2xl px-4 py-3 mb-5">
                <i class="fa-solid fa-circle-check mt-0.5"></i>
                <span>A new verification link has been sent to your email address.</span>
            </div>
        @endif

        <!-- Resend Button -->
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit"
                class="w-full bg-[#4A1B36] hover:bg-[#381428] text-white font-semibold py-3.5 rounded-full flex items-center justify-center gap-2 transition duration-200 shadow-md">
                <i class="fa-regular fa-paper-plane text-sm"></i>
                <span>Resend Verification Email</span>
            </button>
        </form>

        <!-- Divider -->
        <div class="relative flex py-6 items-center">
            <div class="flex-grow border-t border-gray-200"></div>
            <span class="flex-shrink mx-4 text-[11px] font-semibold text-gray-400 uppercase tracking-wider">OR</span>
            <div class="flex-grow border-t border-gray-200"></div>
        </div>

        <!-- Sign out -->
        <div class="text-center">
            <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                class="text-xs font-medium text-[#4A1B36] hover:underline">
                <i class="fa-solid fa-arrow-right-from-bracket mr-1"></i> Sign Out
            </a>
            <form id="logout-form" method="POST" action="{{ route('logout') }}" style="display:none">@csrf</form>
        </div>

    </div>

</body>
</html>
