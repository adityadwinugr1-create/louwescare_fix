<x-guest-layout>
<meta name="csrf-token" content="{{ csrf_token() }}"> 
    <div class="w-full sm:max-w-md px-6">
        <div class="mb-8 text-center">
            <img src="{{ asset('assets/icons/logolouwes.png') }}" alt="Logo Louwes Care" class="w-24 h-auto mx-auto mb-4">
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
                <x-primary-button id="login-btn" class="w-full justify-center bg-blue-600 hover:bg-blue-700" type="button">
                    {{ __('Log in') }}
                </x-primary-button>
            </div>

            {{-- GEOLOCATION JS - ADMIN LOCATION VERIFICATION --}}
            @if(config('geo.enabled', true))
            <script>
            document.addEventListener('DOMContentLoaded', function() {
                const loginBtn = document.getElementById('login-btn');
                const form = loginBtn.closest('form');
                
                loginBtn.addEventListener('click', async function(e) {
                    e.preventDefault();
                    
// Skip geolocation check sepenuhnya (langsung submit)
// Skip geo - form.submit();\n                    // return;\n                    
                    
                    // Show loading
                    loginBtn.disabled = true;
                    loginBtn.innerHTML = 'Memverifikasi lokasi...';
                    
                    if (!navigator.geolocation) {
                        alert('{{ config("geo.messages.location_unavailable") }}');
                        loginBtn.disabled = false;
                        loginBtn.innerHTML = 'Log in';
                        return;
                    }
                    
                    navigator.geolocation.getCurrentPosition(
                        async (position) => {
                            const lat = position.coords.latitude;
                            const lng = position.coords.longitude;
                            
                            try {
                                const response = await fetch('{{ route("geo.verify") }}', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                                    },
                                    body: JSON.stringify({ lat, lng })
                                });
                                
                                const data = await response.json();
                                
                                if (data.success) {
                                    console.log('Location OK:', data.message);
                                    form.submit();
                                } else {
                                    alert(data.message);
                                    loginBtn.disabled = false;
                                    loginBtn.innerHTML = 'Log in';
                                }
                            } catch (error) {
                                alert('{{ config("geo.messages.location_unavailable") }}');
                                loginBtn.disabled = false;
                                loginBtn.innerHTML = 'Log in';
                            }
                        },
                        (error) => {
                            if (error.code === 1) {
                                alert('{{ config("geo.messages.location_denied") }}');
                            } else {
                                alert('{{ config("geo.messages.location_unavailable") }}');
                            }
                            loginBtn.disabled = false;
                            loginBtn.innerHTML = 'Log in';
                        },
                        {
                            enableHighAccuracy: true,
                            timeout: 10000,
                            maximumAge: 60000
                        }
                    );
                });
            });
            </script>
            @endif
        </form>
    </div>
</x-guest-layout>