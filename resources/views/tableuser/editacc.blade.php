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
                            <p class="text-center text-xl font-bold">Edit user</p>
                        </div>
                        <form method="POST" action="/editacc/{{ $user->id }}">
                            @csrf
                
                            <!-- Name -->
                            <div class="mt-5">
                                <x-label for="name" :value="__('Name')" />
                
                                <x-input id="name" class="block mt-1 w-full" type="text" name="name" value="{{ $user->name }}" required autofocus />
                            </div>
                
                            <!-- Email Address -->
                            <div class="mt-4">
                                <x-label for="email" :value="__('Email')" />
                
                                <x-input id="email" class="block mt-1 w-full" type="email" name="email" value="{{ $user->email }}" required />
                            </div>
                            
                            <!-- Tag -->
                            <div class="mt-5">
                                <x-label for="tag" :value="__('Tag')" />
                                <select name="tag" id="tag" class="rounded-lg w-full mt-1 shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    @foreach ($tag as $item)
                                    @if (old('tag', $user->tag_id) == $item->id)
                                    <option value="{{ $item->id }}" selected>{{ $item->name }}</option>    
                                    @else
                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                    @endif 
                                    @endforeach
                                </select>
                            </div>
                                <!-- User Type -->
                            <div class="mt-5 {{ $user->id == 1 ? 'hidden' : '' }}">
                                <x-label for="usertype" :value="__('User Type')" />
                                <select name="usertype" id="usertype" class="rounded-lg w-full mt-1 shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <option value="admin" {{ $user->usertype == 'admin' ? 'selected' : '' }}>admin</option>
                                    <option value="supervisor" {{ $user->usertype == 'supervisor' ? 'selected' : '' }}>supervisor</option>
                                    <option value="user" {{ $user->usertype == 'user' ? 'selected' : '' }}>user</option>
                                </select>
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