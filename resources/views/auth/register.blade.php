<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - StudyPlan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-[#FAF8F5] min-h-screen flex items-center justify-center p-4">

    <div class="w-full max-w-[420px] bg-white rounded-[32px] border border-[#F0EAE1] p-8 shadow-sm">
        
        <!-- Header Title -->
        <div class="text-center mb-6">
            <h1 class="text-2xl font-bold text-[#4A1B36] mb-1">Join StudyPlan</h1>
            <p class="text-xs text-gray-500 leading-relaxed">Create an account to start organizing your studies.</p>
        </div>

        <!-- Form Register Laravel -->
        <form method="POST" action="{{ route('register') }}" class="space-y-4">
            @csrf

            <!-- Full Name Field -->
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1.5">Full Name</label>
                <div class="relative flex items-center">
                    <span class="absolute left-4 text-gray-400">
                        <i class="fa-regular fa-user text-base"></i>
                    </span>
                    <input type="text" name="name" value="{{ old('name') }}" required autofocus
                        placeholder="Jane Doe"
                        class="w-full bg-[#F5F2ED] text-gray-800 text-sm rounded-2xl pl-11 pr-4 py-3 outline-none focus:ring-2 focus:ring-[#4A1B36]/30 transition">
                </div>
                @error('name')
                    <span class="text-xs text-red-500 mt-1 block px-1">{{ $message }}</span>
                @enderror
            </div>

            <!-- Email Address Field -->
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1.5">Email Address</label>
                <div class="relative flex items-center">
                    <span class="absolute left-4 text-gray-400">
                        <i class="fa-regular fa-envelope text-base"></i>
                    </span>
                    <input type="email" name="email" value="{{ old('email') }}" required
                        placeholder="jane@example.com"
                        class="w-full bg-[#F5F2ED] text-gray-800 text-sm rounded-2xl pl-11 pr-4 py-3 outline-none focus:ring-2 focus:ring-[#4A1B36]/30 transition">
                </div>
                @error('email')
                    <span class="text-xs text-red-500 mt-1 block px-1">{{ $message }}</span>
                @enderror
            </div>

            <!-- Password Field -->
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1.5">Password</label>
                <div class="relative flex items-center">
                    <span class="absolute left-4 text-gray-400">
                        <i class="fa-solid fa-lock text-sm"></i>
                    </span>
                    <input type="password" id="password" name="password" required
                        placeholder="••••••••"
                        class="w-full bg-[#F5F2ED] text-gray-800 text-sm rounded-2xl pl-11 pr-11 py-3 outline-none focus:ring-2 focus:ring-[#4A1B36]/30 transition">
                    <button type="button" onclick="togglePassword()" class="absolute right-4 text-gray-400 hover:text-gray-600">
                        <i class="fa-regular fa-eye text-sm" id="eyeIcon"></i>
                    </button>
                </div>
                @error('password')
                    <span class="text-xs text-red-500 mt-1 block px-1">{{ $message }}</span>
                @enderror
            </div>

            <!-- Hidden Confirm Password (Required for Breeze) -->
            <input type="hidden" id="password_confirmation" name="password_confirmation">

            <!-- Terms & Privacy Checkbox -->
            <div class="flex items-start gap-2.5 pt-1">
                <input type="checkbox" id="terms" required class="mt-0.5 w-4 h-4 rounded border-gray-300 text-[#4A1B36] focus:ring-[#4A1B36] cursor-pointer">
                <label for="terms" class="text-[11px] text-gray-600 leading-snug cursor-pointer">
                    I agree to the <a href="#" class="text-[#4A1B36] font-semibold hover:underline">Terms of Service</a> and <a href="#" class="text-[#4A1B36] font-semibold hover:underline">Privacy Policy</a>.
                </label>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="w-full bg-[#4A1B36] hover:bg-[#381428] text-white font-semibold py-3.5 rounded-full flex items-center justify-center gap-2 transition duration-200 mt-2 shadow-md text-sm">
                <span>Sign Up</span>
                <i class="fa-solid fa-arrow-right text-xs"></i>
            </button>
        </form>

        <!-- Divider -->
        <div class="relative flex py-5 items-center">
            <div class="flex-grow border-t border-gray-200"></div>
            <span class="flex-shrink mx-4 text-[10px] font-semibold text-gray-400 uppercase tracking-wider">OR</span>
            <div class="flex-grow border-t border-gray-200"></div>
        </div>

        <!-- Social Buttons Stacked -->
        <div class="space-y-2.5">
            <a href="#" class="w-full flex items-center justify-center gap-2.5 bg-[#F5F2ED] hover:bg-[#ECE6DE] text-gray-800 text-xs font-semibold py-3 px-4 rounded-full transition">
                <svg class="w-4 h-4" viewBox="0 0 24 24">
                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                    <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/>
                    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/>
                </svg>
                Continue with Google
            </a>
            
            <a href="#" class="w-full flex items-center justify-center gap-2.5 bg-[#229ED9] hover:bg-[#1C8CBF] text-white text-xs font-semibold py-3 px-4 rounded-full transition shadow-sm">
                <i class="fa-brands fa-telegram text-base"></i>
                Continue with Telegram
            </a>
        </div>

        <!-- Footer Link to Login -->
        <div class="text-center mt-6">
            <p class="text-xs text-gray-500">
                Already have an account? 
                <a href="{{ route('login') }}" class="text-[#4A1B36] font-semibold hover:underline">Log in</a>
            </p>
        </div>

    </div>

    <!-- Script Show/Hide Password & Auto Fill Confirm Password -->
    <script>
        const passwordInput = document.getElementById('password');
        const confirmInput = document.getElementById('password_confirmation');

        // Otomatis menyamakan value password_confirmation agar validasi Breeze lulus
        passwordInput.addEventListener('input', function() {
            confirmInput.value = this.value;
        });

        function togglePassword() {
            const eyeIcon = document.getElementById('eyeIcon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.classList.remove('fa-eye');
                eyeIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                eyeIcon.classList.remove('fa-eye-slash');
                eyeIcon.classList.add('fa-eye');
            }
        }
    </script>
</body>
</html>