<x-guest-layout>
    <div class="w-full sm:max-w-md px-6">
        <div class="mb-8 text-center">
            <h2 class="text-3xl font-bold text-gray-800">Login</h2>
            <p class="text-gray-500 mt-2 text-sm">Masuk ke Sistem Manajemen Pesanan</p>
        </div>

        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form method="POST" action="{{ route('login') }}">
    @csrf

    <div class="mb-4">
        <x-input-label for="email" value="Email" />
        <x-text-input id="email" class="block mt-1 w-full border-gray-300 bg-transparent shadow-none" type="email" name="email" :value="old('email')" required autofocus />
        <x-input-error :messages="$errors->get('email')" class="mt-2" />
    </div>

    <div class="mb-4">
        <x-input-label for="password" value="Password" />
        <x-text-input id="password" class="block mt-1 w-full border-gray-300 bg-transparent shadow-none" type="password" name="password" required />
        <x-input-error :messages="$errors->get('password')" class="mt-2" />
    </div>

    <div class="flex items-center justify-between mt-4 mb-6">
        <label for="remember_me" class="inline-flex items-center">
            <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-blue-600 shadow-none focus:ring-blue-500" name="remember">
            <span class="ms-2 text-sm text-gray-600">Ingat saya</span>
        </label>

        @if (Route::has('password.request'))
            <a class="text-sm text-blue-600 hover:text-blue-800 font-medium" href="{{ route('password.request') }}">
                Lupa password?
            </a>
        @endif
    </div>

    <div class="flex items-center justify-end mt-4">
        <x-primary-button class="w-full justify-center bg-blue-600 hover:bg-blue-700">
            {{ __('Log in') }}
        </x-primary-button>
    </div>
</form>
    </div>
</x-guest-layout>