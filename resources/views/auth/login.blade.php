<x-guest-layout>
    <x-auth-card>
        <x-slot name="logo">
            <a href="/">
                <x-application-logo class="w-20 h-20 fill-current text-black" />
            </a>
            
        </x-slot>
        <div class="text-center text-black font-bold text-xl">
            <p>PROBUS IMPLEMENTATOR</p>
        </div><br>
        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <!-- Validation Errors -->
        <x-auth-validation-errors class="mb-4" :errors="$errors" />
        
        <form method="POST" action="{{ route('login') }}">
            @csrf

            <!-- Email Address -->
            <div>
                {{-- <x-label for="email" :value="__('Email')" /> --}}

                <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus placeholder="Email"/>
            </div>

            <!-- Password -->
            <div class="mt-4">
                {{-- <x-label for="password" :value="__('Password')" /> --}}

                <x-input id="password" class="block mt-1 w-full"
                                type="password"
                                name="password"
                                required autocomplete="current-password" 
                                placeholder="Password"/>
                <a class="ml-1 justify-end text-abu rounded-[2px] text-xs hover:font-bold cursor-pointer" id="seepass"><i class="fa-solid fa-eye"></i> Show Password</a>
            </div>

            <!-- Remember Me -->
            {{-- <div class="block mt-4">
                <label for="seepass" class="inline-flex items-center">
                    <input id="seepass" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" name="seepass" onclick="myFunction">
                    <span class="ml-2 text-sm text-gray-600">{{ __('See Password') }}</span>
                </label>
            </div> --}}

            <div class="flex items-center justify-center mt-4">
                {{-- @if (Route::has('password.request'))
                    <a class="underline text-sm text-black hover:text-gray-900" href="{{ route('password.request') }}">
                        {{ __('Forgot your password?') }}
                    </a>
                @endif --}}

                <x-button class="text-center w-full justify-center shadow-md">
                    {{ __('Log in') }}
                </x-button>
            </div>
        </form>
    </x-auth-card>
    
</x-guest-layout>
