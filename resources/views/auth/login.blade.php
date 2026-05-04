<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Maintenance AC System Monitoring dan Service Report untuk manajemen servis, perawatan, dan pelaporan teknisi.">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="{{ url('/') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ secure_asset('images/logo_intan-removebg-preview.png') }}?v=2">
    <link rel="shortcut icon" href="{{ secure_asset('images/logo_intan-removebg-preview.png') }}?v=2">
    <link rel="apple-touch-icon" href="{{ secure_asset('images/logo_intan-removebg-preview.png') }}?v=2">
    <meta name="theme-color" content="#0f172a">
    <meta name="application-name" content="Maintenance AC">
    <meta property="og:title" content="Login - Maintenance AC">
    <meta property="og:description" content="Maintenance AC System Monitoring dan Service Report untuk manajemen servis dan pelaporan teknisi.">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url('/') }}">
    <meta property="og:site_name" content="Maintenance AC">
    <meta property="og:image" content="{{ asset('images/logo_intan-removebg-preview.png') }}">
    <meta property="og:image:alt" content="Logo Maintenance AC">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Login - Maintenance AC">
    <meta name="twitter:description" content="Maintenance AC System Monitoring dan Service Report untuk manajemen servis dan pelaporan teknisi.">
    <meta name="twitter:image" content="{{ asset('images/logo_intan-removebg-preview.png') }}">
    <script type="application/ld+json">
        {
            "@@context": "https://schema.org",
            "@@type": "WebSite",
            "name": "Maintenance AC",
            "url": "{{ url('/') }}",
            "potentialAction": {
                "@@type": "LoginAction",
                "target": "{{ url('/') }}"
            },
            "publisher": {
                "@@type": "Organization",
                "name": "Intan Kemilau",
                "logo": {
                    "@@type": "ImageObject",
                    "url": "{{ asset('images/logo_intan-removebg-preview.png') }}"
                }
            }
        }
    </script>
    <title>Login - Maintenance AC</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: { 50:'#fff7ed',100:'#ffedd5',200:'#fed7aa',300:'#fdba74',400:'#fb923c',500:'#f97316',600:'#ea580c',700:'#c2410c',800:'#9a3412',900:'#7c2d12' },
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: Calibri, 'Segoe UI', Tahoma, Arial, sans-serif; }
        .bg-pattern {
            background-color: #0f172a;
            background-image:
                radial-gradient(at 20% 30%, rgba(249,115,22,0.15) 0px, transparent 50%),
                radial-gradient(at 80% 70%, rgba(59,130,246,0.1) 0px, transparent 50%),
                radial-gradient(at 50% 50%, rgba(249,115,22,0.05) 0px, transparent 50%);
        }
        .glass-card {
            background: rgba(255,255,255,0.05);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255,255,255,0.1);
        }
        @@keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
        .float-animation { animation: float 3s ease-in-out infinite; }

        @@media (max-width: 640px) {
            .glass-card {
                backdrop-filter: none;
                background: rgba(15,23,42,0.78);
            }

            .float-animation {
                animation: none;
            }

            .mobile-decor {
                display: none;
            }
        }
    </style>
</head>
<body class="bg-pattern min-h-screen flex items-center justify-center p-4">

    {{-- Floating decorative elements --}}
    <div class="mobile-decor fixed top-10 left-10 w-20 h-20 bg-primary-500/10 rounded-full blur-xl float-animation"></div>
    <div class="mobile-decor fixed bottom-20 right-20 w-32 h-32 bg-blue-500/10 rounded-full blur-xl float-animation" style="animation-delay: 1s;"></div>
    <div class="mobile-decor fixed top-1/3 right-1/4 w-16 h-16 bg-primary-400/10 rounded-full blur-xl float-animation" style="animation-delay: 2s;"></div>

    <div class="w-full max-w-md relative z-10">
        {{-- Logo --}}
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-primary-500 to-primary-700 rounded-2xl shadow-lg shadow-primary-500/30 mb-4">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-white">Maintenance AC</h1>
            <p class="text-gray-400 text-sm mt-1">Sistem Monitoring & Service Report</p>
        </div>

        {{-- Login Card --}}
        <div class="glass-card rounded-2xl p-8">
            @if($errors->any())
            <div class="bg-red-500/10 border border-red-500/30 text-red-400 px-4 py-3 rounded-xl mb-6 text-sm">
                @foreach($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
            @endif

            <form method="POST" action="/login">
                @csrf
                <div class="mb-5">
                    <label class="block text-gray-300 text-sm font-medium mb-2">Username</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        <input type="text" name="username" value="{{ old('username') }}" required autofocus
                            class="w-full bg-white/5 border border-white/10 text-white rounded-xl pl-11 pr-4 py-3 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent placeholder-gray-500 transition"
                            placeholder="Masukkan username">
                    </div>
                </div>

                <div class="mb-6">
                    <label class="block text-gray-300 text-sm font-medium mb-2">Password</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        </div>
                        <input id="password" type="password" name="password" required
                            class="w-full bg-white/5 border border-white/10 text-white rounded-xl pl-11 pr-12 py-3 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent placeholder-gray-500 transition"
                            placeholder="Masukkan password">
                        <button id="togglePassword" type="button" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-gray-400 hover:text-gray-200 transition" aria-label="Tampilkan password" aria-pressed="false">
                            <svg id="eyeOpen" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <svg id="eyeClosed" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.27-2.943-9.542-7a9.961 9.961 0 012.042-3.368M9.88 9.88A3 3 0 0114.12 14.12"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6.228 6.228A9.956 9.956 0 0112 5c4.478 0 8.27 2.943 9.542 7a9.97 9.97 0 01-4.347 5.162M3 3l18 18"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="flex items-center justify-between mb-6">
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" name="remember" class="w-4 h-4 rounded border-white/20 bg-white/5 text-primary-500 focus:ring-primary-500 focus:ring-offset-0">
                        <span class="text-sm text-gray-400 ml-2">Ingat saya</span>
                    </label>
                </div>

                <button type="submit"
                    class="w-full bg-gradient-to-r from-primary-500 to-primary-600 hover:from-primary-600 hover:to-primary-700 text-white font-semibold py-3 px-4 rounded-xl transition-all duration-200 shadow-lg shadow-primary-500/25 hover:shadow-primary-500/40 hover:-translate-y-0.5 active:translate-y-0">
                    Masuk
                </button>
            </form>
        </div>

        <p class="text-center text-gray-600 text-xs mt-6">&copy; {{ date('Y') }} Maintenance AC System</p>
    </div>

    <script>
        const passwordInput = document.getElementById('password');
        const togglePasswordBtn = document.getElementById('togglePassword');
        const eyeOpen = document.getElementById('eyeOpen');
        const eyeClosed = document.getElementById('eyeClosed');

        togglePasswordBtn.addEventListener('click', function () {
            const isPassword = passwordInput.type === 'password';
            passwordInput.type = isPassword ? 'text' : 'password';
            eyeOpen.classList.toggle('hidden', isPassword);
            eyeClosed.classList.toggle('hidden', !isPassword);
            togglePasswordBtn.setAttribute('aria-pressed', isPassword ? 'true' : 'false');
            togglePasswordBtn.setAttribute('aria-label', isPassword ? 'Sembunyikan password' : 'Tampilkan password');
        });
    </script>
</body>
</html>
