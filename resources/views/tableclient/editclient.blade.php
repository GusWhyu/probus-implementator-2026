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
                            <p class="text-center text-xl font-bold">Edit outlet</p>
                        </div>
                        <form method="POST" action="/editclient/{{ $client->id }}">
                            @csrf
                
                            <!-- Name -->
                            <div class="mt-5">
                                <x-label for="name" :value="__('Name')" />
                
                                <x-input id="name" class="block mt-1 w-full" type="text" name="name" value="{{ $client->nm_out }}" required autofocus />
                            </div>
                            <div class="mt-5">
                                <x-label for="name" :value="__('Location')" />
                
                                <x-input id="lokasi" class="block mt-1 w-full" type="text" name="lokasi" value="{{ $client->lokasi }}" required autofocus />
                            </div>
                
                            <div class="flex items-center justify-end mt-4">
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