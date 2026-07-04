@extends('layouts.app')

@section('title', 'Drafts')

@section('content')
    <h1 class="text-xl font-semibold mb-4">Drafts</h1>

    <div class="bg-white rounded border divide-y">
        @forelse ($drafts as $draft)
            <div class="p-4 flex items-center justify-between">
                <a href="{{ route('compose.create', ['draft' => $draft->id]) }}" class="flex-1 min-w-0">
                    <div class="font-medium truncate">{{ $draft->subject ?: '(no subject)' }}</div>
                    <div class="text-sm text-gray-500 truncate">To: {{ $draft->to_addresses ?: '(no recipient yet)' }}</div>
                    <div class="text-xs text-gray-400 mt-1">Last saved {{ $draft->updated_at->diffForHumans() }}</div>
                </a>
                <form method="POST" action="{{ route('drafts.destroy', $draft) }}" onsubmit="return confirm('Discard this draft?')">
                    @csrf @method('DELETE')
                    <button class="text-sm px-3 py-1 rounded border text-red-600">Discard</button>
                </form>
            </div>
        @empty
            <p class="p-4 text-gray-500">No saved drafts.</p>
        @endforelse
    </div>
@endsection
