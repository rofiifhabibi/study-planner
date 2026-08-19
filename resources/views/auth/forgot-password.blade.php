<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - Study Planner</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-[#FAF8F5] min-h-screen flex items-center justify-center p-4">

    <div class="w-full max-w-[420px] bg-white rounded-[32px] border border-[#F0EAE1] p-8 shadow-sm relative">
        
        <!-- Back Button Top Left -->
        <a href="{{ route('login') }}" class="absolute top-6 left-6 w-10 h-10 bg-[#F5F2ED] hover:bg-[#ECE6DE] text-[#4A1B36] rounded-full flex items-center justify-center transition">
            <i class="fa-solid fa-arrow-left text-sm"></i>
        </a>

        <!-- Key/Lock Icon Header -->
        <div class="flex justify-center mt-4 mb-6">
            <div class="w-16 h-16 bg-[#F5EBF2] text-[#4A1B36] rounded-2xl flex items-center justify-center text-xl">
                <i class="fa-solid fa-key"></i>
            </div>
        </div>

        <!-- Title & Subtitle -->
        <div class="text-center mb-8">
            <h1 class="text-2xl font-bold text-[#4A1B36] mb-2">Forgot Password</h1>
            <p class="text-xs text-gray-500 leading-relaxed">Enter your email to receive a reset link.</p>
        </div>

        <!-- Status Alert (Jika link reset berhasil dikirim) -->
        @if (session('status'))
            <div class="mb-4 text-xs font-medium text-green-600 bg-green-50 p-3 rounded-xl border border-green-200 text-center">
                {{ session('status') }}
            </div>
        @endif

        <!-- Form Forgot Password Laravel -->
        <form method="POST" action="{{ route('password.email') }}" class="space-y-6">
            @csrf

            <!-- Email Field -->
            <div>
                <div class="relative flex items-center">
                    <span class="absolute left-4 text-gray-400">
                        <i class="fa-regular fa-envelope text-lg"></i>
                    </span>
                    <input type="email" name="email" value="{{ old('email') }}" required autofocus
                        placeholder="Email address"
                        class="w-full bg-[#F5F2ED] text-gray-800 text-sm rounded-2xl pl-11 pr-4 py-3.5 outline-none focus:ring-2 focus:ring-[#4A1B36]/30 transition">
                </div>
                @error('email')
                    <span class="text-xs text-red-500 mt-1.5 block px-1">{{ $message }}</span>
                @enderror
            </div>

            <!-- Submit Button -->
            <button type="submit" class="w-full bg-[#4A1B36] hover:bg-[#381428] text-white font-semibold py-3.5 rounded-full transition duration-200 shadow-md text-sm">
                Send Reset Link
            </button>
        </form>

        <!-- Back to Login Link Bottom -->
        <div class="text-center mt-6">
            <a href="{{ route('login') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-[#4A1B36] hover:underline">
                <i class="fa-solid fa-arrow-left text-[10px]"></i>
                Back to Login
            </a>
        </div>

    </div>

</body>
</html>