@extends('layouts.app')

@section('title', $email->subject)

@section('content')
    <div class="flex items-center justify-between mb-3">
        <a href="{{ route('inbox.index') }}" class="text-sm text-gray-500">&larr; Back to inbox</a>

        <div class="flex gap-2 text-sm">
            @if ($email->is_archived)
                <form method="POST" action="{{ route('inbox.unarchive', $email) }}">
                    @csrf
                    <button class="px-3 py-1 rounded border">Move to inbox</button>
                </form>
            @else
                <form method="POST" action="{{ route('inbox.archive', $email) }}">
                    @csrf
                    <button class="px-3 py-1 rounded border">Archive</button>
                </form>
            @endif
            <form method="POST" action="{{ route('inbox.markUnread', $email) }}">
                @csrf
                <button class="px-3 py-1 rounded border">Mark unread</button>
            </form>
            @if (count($availableFolders) > 1)
                <form method="POST" action="{{ route('inbox.move', $email) }}" class="flex gap-1">
                    @csrf
                    <select name="folder" class="border rounded px-2 py-1 text-sm" required>
                        <option value="" disabled {{ $suggestedFolder ? '' : 'selected' }}>Move to…</option>
                        @foreach ($availableFolders as $f)
                            @unless ($f === $email->folder)
                                <option value="{{ $f }}" @selected($f === $suggestedFolder)>
                                    {{ \App\Models\MailFolder::displayName($f) }}{{ $f === $suggestedFolder ? ' (suggested)' : '' }}
                                </option>
                            @endunless
                        @endforeach
                    </select>
                    <button class="px-3 py-1 rounded border">Move</button>
                </form>
            @endif
            <form method="POST" action="{{ route('inbox.destroy', $email) }}" onsubmit="return confirm('Delete this conversation?')">
                @csrf @method('DELETE')
                <button class="px-3 py-1 rounded border text-red-600">Delete</button>
            </form>
        </div>
    </div>

    <h1 class="text-xl font-semibold mb-3">{{ $email->subject }}</h1>

    <div class="space-y-4">
        @foreach ($messages as $message)
            @include('inbox._message', ['message' => $message])
        @endforeach
    </div>
@endsection
