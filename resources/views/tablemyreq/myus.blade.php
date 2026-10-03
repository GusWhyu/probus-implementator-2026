<x-app-layout>
    {{-- <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot> --}}

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-4 lg:px-6">
            <div class=" overflow-hidden sm:rounded-lg">
                <div class="p-3">
                   <div class="flex flex-col">
                    <div class="flex flex-row justify-between">
                        <h1 class="font-bold text-xl text-black mt-2 ml-2">Update System</h1>
                        <a href="{{ url()->previous() }}" class="text-black hover:text-gray-700 text-3xl"><i class="fa-solid fa-circle-arrow-left"></i></a>
                    </div>
                    <div class="grid md:grid-cols-4 grid-rows-1">
                        @forelse ($data as $dt)
                        <a href="/detailus/{{ $dt->id }}" class="mx-2">
                            <div class="card bg-white rounded-xl mt-3">
                                <div class="card-body">
                                    <h1 class="card-title font-bold">
                                        {{ $dt->judul }} 
                                    </h1>
                                </div>
                                <div class="card-actions justify-start pl-7">
                                    <div class="badge badge-secondary mb-3">{{ $dt->outlet->nm_out ?? '-' }}</div>
                                </div>
                                <div class="card-actions justify-start pl-7">
                                    <div class="badge badge-neutral mb-3 text-white">{{ $dt->kategori->name ?? '-' }}</div>
                                </div>
                                <div class="justify-start pl-7">
                                    <div class="mb-3 text-xs"><i class="fa-solid fa-calendar"></i> Posted: {{ $dt->created_at->toFormattedDayDateString() }} ({{ $dt->created_at->diffForHumans() }})</div>
                                </div>
                            </div>
                        </a>
                        @empty
                        <div class="col-span-4 text-center py-10 text-gray-500">
                            <p class="text-lg">No Update System found.</p>
                        </div>
                        @endforelse
                    </div>
                    <div class="mt-6">
                        {{ $data->links() }}
                    </div>
                   </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
