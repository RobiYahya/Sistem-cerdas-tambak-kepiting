<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - AquaCrab IoT Monitoring System</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <script src="{{ asset('js/login.js') }}" defer></script>
</head>
<body class="min-h-screen bg-page font-sans text-ink antialiased">
<div class="grid min-h-screen grid-cols-1 lg:grid-cols-[59fr_41fr]">

    {{-- ========== PANEL HIJAU (hanya tampil di laptop/desktop, >= 1024px) ========== --}}
    <aside class="hidden min-h-screen flex-col bg-forest px-[clamp(40px,4.4vw,64px)] pt-[52px] pb-10 lg:flex">
        <div class="flex items-center gap-3.5">
            <div class="grid size-10 place-items-center rounded-full bg-lime-bright text-lg font-bold text-forest">A</div>
            <div>
                <div class="text-[19px] leading-tight font-semibold text-lime-bright">AquaCrab</div>
                <div class="mt-0.5 text-[11px] tracking-[1px] text-white uppercase">Water Monitoring System</div>
            </div>
        </div>

        <div class="mt-[clamp(32px,5vh,56px)]">
            <div class="flex items-center gap-3 text-xs font-semibold tracking-[2.6px] text-lime-bright uppercase">
                <i class="h-[3px] w-7 rounded-full bg-lime-bright"></i>Real-Time IoT
            </div>
            <h1 class="mt-[18px] text-[clamp(28px,2.6vw,38px)] leading-[1.45] font-bold tracking-[-0.5px] text-white">
                Pantau Kualitas Air<br>Secara Real-Time
            </h1>
            <p class="mt-[18px] max-w-[440px] text-sm leading-[1.7] text-white">
                Sistem pemantauan IoT terpadu untuk parameter air laut, notifikasi otomatis, dan inferensi fuzzy.
            </p>
        </div>

        <div class="mt-[clamp(28px,4vh,44px)] w-full max-w-[440px] self-center overflow-hidden rounded-[26px] border border-forest-line bg-forest-deep">
            <div class="grid h-[clamp(280px,38vh,380px)] place-items-center rounded-[26px] border-b border-forest-line bg-forest-card">
                @include('partials.crab-pixel')
            </div>
            <div class="grid grid-cols-3 pt-3.5 pb-3">
                <div class="px-2 py-1 text-center">
                    <strong class="block text-sm font-semibold text-lime-bright">Air Laut</strong>
                    <span class="mt-1 block text-[11.5px] text-white">Pemantauan</span>
                </div>
                <div class="border-l border-forest-line px-2 py-1 text-center">
                    <strong class="block text-sm font-semibold text-lime-bright">Notifikasi</strong>
                    <span class="mt-1 block text-[11.5px] text-white">Otomatis</span>
                </div>
                <div class="border-l border-forest-line px-2 py-1 text-center">
                    <strong class="block text-sm font-semibold text-lime-bright">Fuzzy</strong>
                    <span class="mt-1 block text-[11.5px] text-white">Inferensi</span>
                </div>
            </div>
        </div>

        <div class="mt-auto flex items-center gap-2 pt-8 text-[13px] text-[#4f7a5f]">
            <i class="size-[7px] rounded-full bg-lime-bright"></i>AquaCrab · IoT Monitoring System
        </div>
    </aside>

    {{-- ========== FORM LOGIN ========== --}}
    <main class="flex min-h-screen items-center justify-center px-4 py-6 lg:px-[clamp(24px,4vw,56px)] lg:py-10">
        <section class="w-full max-w-[480px] rounded-[28px] bg-white px-[22px] pt-9 pb-7 shadow-[0_10px_40px_rgba(21,48,36,0.08)]
                        max-[380px]:rounded-[22px] max-[380px]:px-4 max-[380px]:pt-[30px] max-[380px]:pb-6
                        lg:rounded-[40px] lg:px-[clamp(32px,3.4vw,52px)] lg:pt-14 lg:pb-11 lg:shadow-[0_24px_60px_rgba(21,48,36,0.10)]">

            <div class="border-b border-[#e9ecef] pb-7 text-center lg:pb-[34px]">
                <span class="mx-auto mb-4 grid size-[60px] place-items-center rounded-full border-2 border-[#d8f0ad] bg-[#eaf8d3] text-[26px] text-[#cfe7a9] lg:mb-[18px]">
                    <i class="bi bi-lock"></i>
                </span>
                <h2 class="text-[28px] font-bold tracking-[-0.3px] max-[380px]:text-[25px]">Login</h2>
                <p class="mt-2 text-[14.5px] text-[#5b6b80]">Masuk sebagai admin atau operator</p>
            </div>

            <form method="POST" action="{{ route('login.submit') }}" class="mt-7 lg:mt-9" novalidate>
                @csrf

                <div class="mb-5 lg:mb-6">
                    <label for="username" class="mb-2.5 block text-sm font-semibold">Username</label>
                    <div class="relative flex h-[50px] items-center rounded-[14px] border border-[#eef0f2] bg-[#f8f9fa] transition focus-within:border-brand focus-within:bg-white focus-within:ring-4 focus-within:ring-brand/15">
                        <i class="bi bi-person pointer-events-none absolute left-4 text-lg text-gray-500"></i>
                        <input type="text" id="username" name="username" placeholder="Masukkan username"
                               value="{{ old('username') }}" autocomplete="username" required
                               class="h-full w-full bg-transparent px-12 text-[15px] text-ink outline-none placeholder:text-[#8a94a3]">
                    </div>
                </div>

                <div class="mb-5 lg:mb-6">
                    <label for="password" class="mb-2.5 block text-sm font-semibold">Password</label>
                    <div class="relative flex h-[50px] items-center rounded-[14px] border border-[#eef0f2] bg-[#f8f9fa] transition focus-within:border-brand focus-within:bg-white focus-within:ring-4 focus-within:ring-brand/15">
                        <i class="bi bi-lock pointer-events-none absolute left-4 text-lg text-gray-500"></i>
                        <input type="password" id="password" name="password" placeholder="Masukkan password"
                               autocomplete="current-password" required
                               class="h-full w-full bg-transparent px-12 text-[15px] text-ink outline-none placeholder:text-[#8a94a3]">
                        <button type="button" id="togglePassword" aria-label="Tampilkan password"
                                class="absolute top-1/2 right-2 grid size-[38px] -translate-y-1/2 cursor-pointer place-items-center rounded-[10px] text-lg text-gray-500 hover:bg-[#eef0f2]">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                </div>

                <div class="mt-1 flex items-center justify-between gap-3 lg:mt-2.5">
                    <label class="inline-flex cursor-pointer items-center gap-3 text-[15px] text-gray-500 select-none">
                        <input type="checkbox" name="remember" class="peer sr-only">
                        <span class="grid size-[18px] place-items-center rounded-[5px] border border-[#dfe3e7] bg-[#fafbfb] text-[13px] text-transparent transition peer-checked:border-forest peer-checked:bg-forest peer-checked:text-white peer-focus-visible:ring-[3px] peer-focus-visible:ring-brand/35">
                            <i class="bi bi-check-lg"></i>
                        </span>
                        Ingat saya
                    </label>
                    <a href="#" class="text-sm font-semibold text-forest hover:underline">Lupa Password?</a>
                </div>

                <button type="submit"
                        class="mt-7 inline-flex h-14 w-full cursor-pointer items-center justify-center gap-2.5 rounded-[14px] bg-forest text-base font-semibold text-white shadow-[0_10px_22px_rgba(21,48,36,0.22)] transition hover:bg-[#1b3d2e] active:translate-y-px">
                    Masuk <i class="bi bi-arrow-right text-xl"></i>
                </button>
            </form>

            <div class="mt-[26px] text-center">
                <span class="inline-flex items-center gap-3 text-[13px] text-[#8c96a3] before:h-px before:w-[18px] before:bg-[#e3e7ea] before:content-[''] after:h-px after:w-[18px] after:bg-[#e3e7ea] after:content-['']">
                    Butuh bantuan? Hubungi administrator.
                </span>
            </div>
        </section>
    </main>

</div>
</body>
</html>