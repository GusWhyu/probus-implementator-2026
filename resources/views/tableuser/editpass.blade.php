<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="my-5">
                        <a href="{{ url()->previous() }}" class="bg-white font-bold text-black px-2 py-2 rounded-md border-black border-solid border-2 hover:bg-black hover:text-white transition delay-50 duration-300">« Back</a>
                    </div>
                    <x-auth-validation-errors class="mb-4" :errors="$errors" />
                    <div class="flex-col">
                        <div>
                            <p class="text-center text-xl font-bold">Edit Password</p>
                        </div>
                        <form method="POST" action="/editpass/{{ $data->id }}">
                            @csrf
                            <div class="mt-4">
                                <p>Account: {{ $data->email }} | {{ $data->name }}</p>
                            </div>

                            <!-- Password -->
                            <div class="mt-4">
                                <x-label for="password" :value="__('New Password')" />
                
                                <x-input id="password" class="block mt-1 w-full"
                                                type="password"
                                                name="password"
                                                required autocomplete="new-password" />
                            </div>
                
                            <!-- Confirm Password -->
                            <div class="mt-4">
                                <x-label for="password_confirmation" :value="__('Confirm New Password')" />
                
                                <x-input id="password_confirmation" class="block mt-1 w-full"
                                                type="password"
                                                name="password_confirmation" required />
                            </div>
                
                            <div class="flex items-center justify-end mt-4">
                                {{-- <a class="underline text-sm text-gray-600 hover:text-gray-900" href="{{ route('login') }}">
                                    {{ __('Already registered?') }}
                                </a> --}}
                
                                <x-button class="ml-4">
                                    {{ __('Edit') }}
                                </x-button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>