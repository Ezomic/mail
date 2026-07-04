@extends('layouts.app')

@section('title', 'Accounts')

@section('content')
    <h1 class="text-xl font-semibold mb-4">Connected accounts</h1>

    <div class="flex gap-2 mb-6">
        <a href="{{ route('auth.google.redirect') }}" class="px-4 py-2 rounded bg-white border shadow-sm">Connect Gmail</a>
        <a href="{{ route('auth.microsoft.redirect') }}" class="px-4 py-2 rounded bg-white border shadow-sm">Connect Outlook / Hotmail</a>
        <a href="{{ route('accounts.create') }}" class="px-4 py-2 rounded bg-white border shadow-sm">Add custom IMAP/SMTP</a>
    </div>

    <div class="bg-white rounded border divide-y">
        @forelse ($accounts as $account)
            <div class="p-4 flex items-center justify-between">
                <div>
                    <div class="font-medium">{{ $account->email_address }}
                        <span class="text-xs uppercase text-gray-400 ml-2">{{ $account->provider }}</span>
                        @unless ($account->is_active)
                            <span class="text-xs uppercase text-gray-400 ml-2">(inactive)</span>
                        @endunless
                    </div>
                    <div class="text-sm text-gray-500">
                        Status: {{ $account->sync_status }}
                        @if ($account->last_synced_at)
                            · last synced {{ $account->last_synced_at->diffForHumans() }}
                        @endif
                        @if ($account->sync_error)
                            · <span class="text-red-600">{{ $account->sync_error }}</span>
                        @endif
                    </div>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('accounts.edit', $account) }}" class="text-sm px-3 py-1 rounded border">Edit</a>
                    <form method="POST" action="{{ route('accounts.sync', $account) }}">
                        @csrf
                        <button class="text-sm px-3 py-1 rounded border">Sync now</button>
                    </form>
                    <form method="POST" action="{{ route('accounts.destroy', $account) }}" onsubmit="return confirm('Remove this account?')">
                        @csrf @method('DELETE')
                        <button class="text-sm px-3 py-1 rounded border text-red-600">Remove</button>
                    </form>
                </div>
            </div>
        @empty
            <p class="p-4 text-gray-500">No accounts connected yet.</p>
        @endforelse
    </div>
@endsection
