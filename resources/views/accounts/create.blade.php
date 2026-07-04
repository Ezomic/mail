@extends('layouts.app')

@section('title', 'Add custom account')

@section('content')
    <h1 class="text-xl font-semibold mb-4">Add a custom IMAP/SMTP account</h1>

    <form method="POST" action="{{ route('accounts.store') }}" class="bg-white border rounded p-6 max-w-xl space-y-4">
        @csrf

        <div>
            <label class="block text-sm font-medium mb-1">Email address</label>
            <input type="email" name="email_address" value="{{ old('email_address') }}" class="w-full border rounded px-3 py-2" required>
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Display name</label>
            <input type="text" name="display_name" value="{{ old('display_name') }}" class="w-full border rounded px-3 py-2">
        </div>

        <div class="grid grid-cols-3 gap-3">
            <div class="col-span-2">
                <label class="block text-sm font-medium mb-1">IMAP host</label>
                <input type="text" name="imap_host" placeholder="mail.example.com" value="{{ old('imap_host') }}" class="w-full border rounded px-3 py-2" required>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Port</label>
                <input type="number" name="imap_port" placeholder="993" value="{{ old('imap_port', 993) }}" class="w-full border rounded px-3 py-2" required>
            </div>
        </div>
        <div class="grid grid-cols-3 gap-3">
            <div>
                <label class="block text-sm font-medium mb-1">Encryption</label>
                <select name="imap_encryption" class="w-full border rounded px-3 py-2">
                    <option value="ssl">SSL</option>
                    <option value="tls">TLS</option>
                </select>
            </div>
            <div class="col-span-2">
                <label class="block text-sm font-medium mb-1">IMAP username</label>
                <input type="text" name="imap_username" value="{{ old('imap_username') }}" class="w-full border rounded px-3 py-2" required>
            </div>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">IMAP password</label>
            <input type="password" name="imap_password" class="w-full border rounded px-3 py-2" required>
        </div>

        <hr>

        <div class="grid grid-cols-3 gap-3">
            <div class="col-span-2">
                <label class="block text-sm font-medium mb-1">SMTP host</label>
                <input type="text" name="smtp_host" placeholder="mail.example.com" value="{{ old('smtp_host') }}" class="w-full border rounded px-3 py-2" required>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Port</label>
                <input type="number" name="smtp_port" placeholder="587" value="{{ old('smtp_port', 587) }}" class="w-full border rounded px-3 py-2" required>
            </div>
        </div>
        <div class="grid grid-cols-3 gap-3">
            <div>
                <label class="block text-sm font-medium mb-1">Encryption</label>
                <select name="smtp_encryption" class="w-full border rounded px-3 py-2">
                    <option value="tls">TLS</option>
                    <option value="ssl">SSL</option>
                </select>
            </div>
            <div class="col-span-2">
                <label class="block text-sm font-medium mb-1">SMTP username</label>
                <input type="text" name="smtp_username" value="{{ old('smtp_username') }}" class="w-full border rounded px-3 py-2" required>
            </div>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">SMTP password</label>
            <input type="password" name="smtp_password" class="w-full border rounded px-3 py-2" required>
        </div>

        <button class="px-4 py-2 rounded bg-blue-600 text-white">Add account</button>
    </form>
@endsection
