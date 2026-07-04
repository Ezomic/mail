@extends('layouts.app')

@section('title', 'Process Inbox')

@section('content')
    <div class="flex items-center justify-between mb-4">
        <h1 class="text-xl font-semibold">Process Inbox</h1>

        @if ($accounts->isNotEmpty())
            <form method="GET" class="flex gap-2">
                <select name="account" class="border rounded px-3 py-2 text-sm" onchange="this.form.submit()">
                    @foreach ($accounts as $acc)
                        <option value="{{ $acc->id }}" @selected($account && $account->id === $acc->id)>{{ $acc->email_address }}</option>
                    @endforeach
                </select>
            </form>
        @endif
    </div>

    @if (! $account)
        <p class="text-gray-500">Connect an account first.</p>
    @elseif (! $email)
        <div class="bg-white rounded border p-10 text-center">
            <p class="text-lg font-medium mb-1">📭 Inbox zero!</p>
            <p class="text-gray-500 text-sm">Nothing left to process in {{ $account->email_address }}.</p>

            @if ($skippedCount > 0)
                <form method="POST" action="{{ route('triage.resetSkipped') }}" class="mt-4">
                    @csrf
                    <input type="hidden" name="account" value="{{ $account->id }}">
                    <button class="text-sm underline text-gray-500">Show {{ $skippedCount }} skipped conversation(s) again</button>
                </form>
            @endif
        </div>
    @else
        <div class="flex items-center justify-between text-sm text-gray-500 mb-2">
            <span>{{ $remaining }} conversation(s) left in Inbox</span>
            @if ($skippedCount > 0)
                <span>{{ $skippedCount }} skipped this session</span>
            @endif
        </div>

        <div class="bg-white rounded border p-6">
            <div class="flex items-center justify-between mb-1">
                <div>
                    <span class="font-semibold">{{ $email->from_name ?: $email->from_address }}</span>
                    <span class="text-sm text-gray-500">&lt;{{ $email->from_address }}&gt;</span>
                </div>
                <div class="text-xs text-gray-400">{{ $email->sent_at?->format('M j, Y g:i A') }}</div>
            </div>

            <div class="font-medium mb-4">{{ $email->subject }}</div>

            <div class="prose max-w-none max-h-96 overflow-y-auto border-t pt-4">
                {!! $email->body_html ?: nl2br(e($email->body_text ?: '(no preview available)')) !!}
            </div>

            <div class="mt-4 pt-4 border-t">
                <a href="{{ route('inbox.show', $email) }}" class="text-sm text-gray-500 underline">View full conversation &amp; reply</a>
            </div>
        </div>

        @if ($suggestedFolder)
            <form method="POST" action="{{ route('triage.move', $email) }}" class="mt-4">
                @csrf
                <input type="hidden" name="folder" value="{{ $suggestedFolder }}">
                <button class="w-full px-4 py-3 rounded bg-green-600 text-white font-medium text-left flex items-center justify-between" title="Shortcut: Enter">
                    <span>✨ Suggested: mark read &amp; move to <strong>{{ \App\Models\MailFolder::displayName($suggestedFolder) }}</strong></span>
                    <span class="text-xs opacity-75">based on mail from this sender</span>
                </button>
            </form>
        @endif

        <div class="mt-3 flex items-center gap-2">
            <form method="POST" action="{{ route('triage.delete', $email) }}" onsubmit="return confirm('Delete this conversation?')">
                @csrf
                <button class="px-4 py-2 rounded border text-red-600 font-medium" title="Shortcut: D">Delete</button>
            </form>

            <form method="POST" action="{{ route('triage.move', $email) }}" class="flex gap-2">
                @csrf
                <select name="folder" class="border rounded px-3 py-2 text-sm" required>
                    <option value="" disabled {{ $suggestedFolder ? '' : 'selected' }}>Move to…</option>
                    @foreach ($folders as $f)
                        <option value="{{ $f }}" @selected($f === $suggestedFolder)>{{ \App\Models\MailFolder::displayName($f) }}</option>
                    @endforeach
                </select>
                <button class="px-4 py-2 rounded bg-blue-600 text-white font-medium">Mark read &amp; move</button>
            </form>

            <form method="POST" action="{{ route('triage.skip', $email) }}">
                @csrf
                <button class="px-4 py-2 rounded border text-gray-500" title="Shortcut: S">Skip for now</button>
            </form>
        </div>

        <p class="text-xs text-gray-400 mt-3">
            Keyboard shortcuts: <kbd>D</kbd> delete, <kbd>S</kbd> skip
            @if ($suggestedFolder)
                , <kbd>Enter</kbd> accept suggestion
            @endif
        </p>
    @endif
@endsection

@section('scripts')
    <script>
        document.addEventListener('keydown', function (e) {
            const tag = e.target.tagName;
            if (tag === 'SELECT' || tag === 'INPUT' || tag === 'TEXTAREA') return;

            if (e.key === 'd' || e.key === 'D') {
                document.querySelector('form[action$="/delete"] button')?.click();
            } else if (e.key === 's' || e.key === 'S') {
                document.querySelector('form[action$="/skip"] button')?.click();
            } else if (e.key === 'Enter') {
                document.querySelector('form[action$="/move"] input[name="folder"]')?.closest('form')?.querySelector('button')?.click();
            }
        });
    </script>
@endsection
