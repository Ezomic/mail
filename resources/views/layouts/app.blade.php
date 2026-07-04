<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Mail')</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 text-gray-900">
    <div class="min-h-screen flex">
        <aside class="w-60 bg-white border-r border-gray-200 p-4 flex flex-col gap-1">
            <a href="{{ route('inbox.index') }}" class="text-lg font-semibold mb-4">📬 Mail</a>

            <a href="{{ route('inbox.index') }}" class="px-3 py-2 rounded hover:bg-gray-100 flex items-center justify-between">
                <span>Inbox</span>
                <span id="unread-badge" class="text-xs bg-blue-600 text-white rounded-full px-2 py-0.5 hidden"></span>
            </a>
            <a href="{{ route('inbox.index', ['folder' => 'SENT']) }}" class="px-3 py-2 rounded hover:bg-gray-100">Sent</a>
            <a href="{{ route('drafts.index') }}" class="px-3 py-2 rounded hover:bg-gray-100">Drafts</a>
            <a href="{{ route('inbox.index', ['folder' => 'TRASH']) }}" class="px-3 py-2 rounded hover:bg-gray-100">Trash</a>
            <a href="{{ route('inbox.index', ['archived' => 1]) }}" class="px-3 py-2 rounded hover:bg-gray-100">Archived</a>
            <a href="{{ route('triage.index') }}" class="px-3 py-2 rounded hover:bg-gray-100">🧹 Process Inbox</a>

            <a href="{{ route('compose.create') }}" class="mt-2 px-3 py-2 rounded bg-blue-600 text-white text-center">Compose</a>
            <a href="{{ route('accounts.index') }}" class="px-3 py-2 rounded hover:bg-gray-100">Accounts</a>

            @auth
                @php $sidebarAccounts = auth()->user()->mailAccounts()->get(); @endphp
                @if ($sidebarAccounts->isNotEmpty())
                    <div class="mt-4 pt-3 border-t text-xs uppercase tracking-wide text-gray-400 px-3">Accounts</div>
                    <div class="flex flex-col gap-1">
                        @foreach ($sidebarAccounts as $sidebarAccount)
                            @php $sidebarUnread = $sidebarAccount->unreadCount(); @endphp
                            <a href="{{ route('inbox.index', ['account' => $sidebarAccount->id]) }}" class="px-3 py-1.5 rounded hover:bg-gray-100 flex items-center gap-2 text-sm">
                                <span class="w-2.5 h-2.5 rounded-full flex-shrink-0" style="background-color: {{ $sidebarAccount->color }}"></span>
                                <span class="truncate flex-1">{{ $sidebarAccount->display_name ?: $sidebarAccount->email_address }}</span>
                                @if ($sidebarUnread > 0)
                                    <span class="text-xs text-gray-400">{{ $sidebarUnread }}</span>
                                @endif
                            </a>
                        @endforeach
                    </div>
                @endif
            @endauth

            <form method="POST" action="{{ route('logout') }}" class="mt-auto">
                @csrf
                <button class="px-3 py-2 rounded hover:bg-gray-100 w-full text-left text-sm text-gray-500">Log out</button>
            </form>
        </aside>

        <main class="flex-1 p-6">
            @if (session('status'))
                <div class="mb-4 rounded bg-green-100 text-green-800 px-4 py-2 text-sm">{{ session('status') }}</div>
            @endif
            @if (session('error'))
                <div class="mb-4 rounded bg-red-100 text-red-800 px-4 py-2 text-sm">{{ session('error') }}</div>
            @endif

            @yield('content')
        </main>
    </div>

    @auth
        <script>
            (function () {
                function poll() {
                    fetch('{{ route('inbox.unreadCount') }}', { headers: { 'Accept': 'application/json' } })
                        .then((r) => r.json())
                        .then((data) => {
                            const badge = document.getElementById('unread-badge');
                            if (!badge) return;
                            if (data.unread > 0) {
                                badge.textContent = data.unread > 99 ? '99+' : data.unread;
                                badge.classList.remove('hidden');
                            } else {
                                badge.classList.add('hidden');
                            }
                        })
                        .catch(() => {});
                }
                poll();
                setInterval(poll, 30000);
            })();
        </script>
    @endauth

    @yield('scripts')
</body>
</html>
