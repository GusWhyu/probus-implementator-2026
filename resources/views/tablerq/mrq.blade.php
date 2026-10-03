@php
    if ($dataid == 1) {
        $status = "badge-error";
    } elseif ($dataid == 2) {
        $status = "badge-info";
    } elseif ($dataid == 3) {
        $status = "badge-warning";
    } else {
        $status = "badge-success";
    }

    if ($dataid == 1) {
        $st = "Urgent";
    } elseif ($dataid == 2) {
        $st = "Open";
    } elseif ($dataid == 3) {
        $st = "Progress";
    } else {
        $st = "Closed";
    }
@endphp
<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-4 lg:px-6">
            <div class=" overflow-hidden sm:rounded-lg">
                <div class="p-3">
                   <div class="flex flex-col">
                    <div class="flex flex-row justify-between">
                        <h1 class="font-bold text-xl text-black mt-2 ml-2">{{ $st }}</h1>
                        <a href="/" class="text-black hover:text-gray-700 text-3xl"><i class="fa-solid fa-circle-arrow-left"></i></a>
                    </div>
                    <div class="grid md:grid-cols-4 grid-rows-1">
                        @foreach ($data as $rq)
                        <a href="/detailrequest/{{ $rq->id }}" class="mx-2">
                            <div class="card bg-white rounded-xl mt-3 overflow-hidden">
                                <div class="absolute right-0 m-3 flex items-center gap-1.5">
                                    <span class="text-xs font-bold text-gray-400 font-mono">#{{ $rq->id }}</span>
                                    <div class="badge {{ $status }} rounded-[2px] text-white">{{ $rq->status->name }}</div>
                                </div>
                                <div class="card-body">
                                    <h1 class="card-title font-bold">
                                        {{ $rq->judul }} 
                                    </h1>
                                </div>
                                <div class="card-actions justify-start pl-7">
                                    <div class="badge badge-secondary mb-3">{{ $rq->outlet->nm_out }}</div>
                                </div>
                                <div class="card-actions justify-start pl-7">
                                    <div class="badge badge-primary mb-3 text-white">{{ $rq->tag->name }}</div>
                                    <div class="badge badge-neutral mb-3 text-white">{{ $rq->kategori->name }}</div>
                                </div>
                                <div class="justify-start pl-7">
                                    <div class="mb-3 text-xs"><i class="fa-solid fa-calendar"></i> Update: {{ $rq->updated_at->toFormattedDayDateString() }} ({{ $rq->updated_at->diffForHumans() }})</div>
                                </div>
                                @if ($rq->approval_status == 'approved')
                                <div class="bg-emerald-500 text-white px-5 py-2 flex items-center justify-between text-xs font-medium">
                                    <div class="flex items-center gap-2">
                                        <i class="fa-solid fa-circle-check text-white text-sm"></i>
                                        <span>Approved by <b class="font-bold">{{ $rq->approver->name ?? 'Admin' }}</b></span>
                                    </div>
                                </div>
                                @elseif ($rq->approval_status == 'rejected')
                                <div class="bg-rose-500 text-white px-5 py-2 flex items-center justify-between text-xs font-medium">
                                    <div class="flex items-center gap-2">
                                        <i class="fa-solid fa-circle-xmark text-white text-sm"></i>
                                        <span>Rejected by <b class="font-bold">{{ $rq->approver->name ?? 'Admin' }}</b></span>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </a>
                        @endforeach
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
