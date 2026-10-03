<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="overflow-hidden sm:rounded-lg">
                <div class="p-6">
                    <div class="flex flex-col md:px-40">
                        <x-auth-validation-errors class="mb-4" :errors="$errors" />
                        <div class="bg-white rounded-[28px] shadow-md card">
                            <a href="{{ url()->previous() }}" class="text-5xl text-black mt-2 ml-2 hover:text-gray-700"><i class="fa-solid fa-circle-arrow-left"></i></a>
                            <div class="card-body">
                                <h1 class="card-title text-3xl font-extrabold">Edit Account Information</h1>
                                <form action="/editprofile/{{ Auth::user()->id }}" method="POST">
                                    @csrf
                                    <div class="mt-3">
                                        <p class="font-bold">Name</p>
                                        <input type="text" class="input rounded-xl input-bordered w-full" value="{{ $dataprofil->name }}" name="name" required>
                                    </div>
                                    <div class="mt-3">
                                        <p class="font-bold">User Email</p>
                                        <input type="text" class="input rounded-xl input-bordered w-full" value="{{ $dataprofil->email }}" name="email" required>
                                    </div>
                                    <div class="mt-3">
                                        <p class="font-bold">User Tag</p>
                                        <select name="tag" id="tag" class="select select-bordered w-full">
                                            @foreach ($tag as $tagitem)
                                                @if (old('tag', $dataprofil->tag_id) == $tagitem->id)
                                                    <option value="{{ $tagitem->id }}" selected>{{ $tagitem->name }}</option>
                                                @else
                                                    <option value="{{ $tagitem->id }}">{{ $tagitem->name }}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>       
                                    <div class="mt-3">
                                        <button type="submit" class="btn btn-neutral bg-black w-full">EDIT</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>