<x-guest-layout>
    <x-auth-session-status class="mb-5" :status="session('status')" />
    
    <h2 class="text-2xl font-bold mb-1" style="color: #2C2C2C;">Selamat Datang</h2>
    <p class="text-sm mb-6" style="color: #9E9790;">Masuk ke akun Anda untuk melanjutkan</p>

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <div>
            <x-input-label for="email" :value="__('Alamat Email')" />
            <x-text-input id="email" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="email@contoh.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-1" />
        </div>

        <div>
            <x-input-label for="password" :value="__('Kata Sandi')" />
            <x-text-input id="password" type="password" name="password" required autocomplete="current-password" placeholder="••••••••" />
            <x-input-error :messages="$errors->get('password')" class="mt-1" />
        </div>

        <div class="flex items-center justify-between">
            <label for="remember_me" class="inline-flex items-center gap-2 cursor-pointer">
                <input id="remember_me" type="checkbox" class="rounded text-teal-600 border-gray-300 shadow-sm" name="remember" style="accent-color: #1A6B6B;">
                <span class="text-sm" style="color: #6B6560;">Ingat Saya</span>
            </label>
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="text-sm font-medium hover:underline" style="color: #1A6B6B;">Lupa password?</a>
            @endif
        </div>

        <x-primary-button class="w-full justify-center py-3">
            Masuk ke Sistem
        </x-primary-button>
    </form>
</x-guest-layout>
