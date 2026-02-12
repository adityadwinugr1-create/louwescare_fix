<x-guest-layout>
    <<form method="POST" action="{{ route('password.store') }}">
    @csrf

    <input type="hidden" name="token" value="{{ $request->route('token') }}">

    <div class="mb-4">
        <x-input-label for="email" value="Email" />
        <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', $request->email)" required autofocus />
        <x-input-error :messages="$errors->get('email')" class="mt-2" />
    </div>

    <div class="mb-4">
        <x-input-label for="owner_pin" value="PIN Otorisasi Owner" />
        <x-text-input id="owner_pin" class="block mt-1 w-full" type="password" name="owner_pin" required placeholder="Masukkan PIN Rahasia" />
        <x-input-error :messages="$errors->get('owner_pin')" class="mt-2" />
    </div>

    <div class="mb-4">
        <x-input-label for="password" value="Password Baru" />
        <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required />
        <x-input-error :messages="$errors->get('password')" class="mt-2" />
    </div>

    <div class="mb-4">
        <x-input-label for="password_confirmation" value="Konfirmasi Password Baru" />
        <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required />
    </div>

    <div class="flex items-center justify-end mt-4">
        <x-primary-button class="w-full justify-center bg-blue-600">
            {{ __('Reset Password') }}
        </x-primary-button>
    </div>
</form>
</x-guest-layout>
