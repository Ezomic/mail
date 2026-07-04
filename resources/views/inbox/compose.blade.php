@extends('layouts.app')

@section('title', 'Compose')

@section('content')
    <div class="flex items-center justify-between mb-4">
        <h1 class="text-xl font-semibold">Compose</h1>
        <span id="draft-status" class="text-xs text-gray-400"></span>
    </div>

    <form method="POST" action="{{ route('compose.store') }}" enctype="multipart/form-data" id="compose-form" class="bg-white border rounded p-6 max-w-2xl space-y-4">
        @csrf

        <input type="hidden" name="draft_id" id="draft_id" value="{{ $prefill['draft_id'] }}">
        <input type="hidden" name="in_reply_to" id="in_reply_to" value="{{ $prefill['in_reply_to'] }}">
        <input type="hidden" name="references" id="references" value="{{ $prefill['references'] }}">

        <div>
            <label class="block text-sm font-medium mb-1">Send from</label>
            <select name="mail_account_id" class="w-full border rounded px-3 py-2" required>
                @foreach ($accounts as $acc)
                    <option value="{{ $acc->id }}" @selected($prefill['mail_account_id'] == $acc->id)>{{ $acc->email_address }}</option>
                @endforeach
            </select>
        </div>

        <div class="relative">
            <label class="block text-sm font-medium mb-1">To</label>
            <input type="text" name="to" id="field-to" value="{{ $prefill['to'] }}" placeholder="someone@example.com, another@example.com" class="w-full border rounded px-3 py-2" autocomplete="off" required>
            <div class="contact-dropdown hidden absolute z-10 bg-white border rounded shadow mt-1 w-full max-h-48 overflow-auto"></div>
        </div>

        <div class="relative">
            <label class="block text-sm font-medium mb-1">Cc</label>
            <input type="text" name="cc" id="field-cc" value="{{ $prefill['cc'] }}" class="w-full border rounded px-3 py-2" autocomplete="off">
            <div class="contact-dropdown hidden absolute z-10 bg-white border rounded shadow mt-1 w-full max-h-48 overflow-auto"></div>
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Subject</label>
            <input type="text" name="subject" id="field-subject" value="{{ $prefill['subject'] }}" class="w-full border rounded px-3 py-2" required>
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Message</label>
            <textarea name="body" id="field-body" rows="12" class="w-full border rounded px-3 py-2" required>{{ $prefill['body'] }}</textarea>
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Attachments</label>
            <input type="file" name="attachments[]" multiple class="w-full border rounded px-3 py-2 text-sm">
        </div>

        <div class="flex items-center gap-2">
            <button class="px-4 py-2 rounded bg-blue-600 text-white">Send</button>
            <a href="{{ route('drafts.index') }}" class="text-sm text-gray-500">Saved drafts</a>
        </div>
    </form>
@endsection

@section('scripts')
    <script>
        (function () {
            const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
            const form = document.getElementById('compose-form');
            const draftIdField = document.getElementById('draft_id');
            const statusEl = document.getElementById('draft-status');

            // --- Draft autosave ---
            let saveTimer = null;

            function scheduleSave() {
                clearTimeout(saveTimer);
                saveTimer = setTimeout(saveDraft, 2000);
            }

            function saveDraft() {
                const to = document.getElementById('field-to').value.trim();
                const subject = document.getElementById('field-subject').value.trim();
                const body = document.getElementById('field-body').value.trim();

                if (!to && !subject && !body) {
                    return;
                }

                statusEl.textContent = 'Saving…';

                fetch('{{ route('drafts.autosave') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify({
                        draft_id: draftIdField.value || null,
                        mail_account_id: form.mail_account_id.value,
                        to: to,
                        cc: document.getElementById('field-cc').value,
                        subject: subject,
                        body: body,
                        in_reply_to: document.getElementById('in_reply_to').value,
                        references: document.getElementById('references').value,
                    }),
                })
                    .then((r) => r.json())
                    .then((data) => {
                        if (data.draft_id) {
                            draftIdField.value = data.draft_id;
                        }
                        statusEl.textContent = 'Draft saved';
                    })
                    .catch(() => { statusEl.textContent = ''; });
            }

            ['field-to', 'field-cc', 'field-subject', 'field-body'].forEach((id) => {
                document.getElementById(id).addEventListener('input', scheduleSave);
            });

            // --- Contact autocomplete on To/Cc ---
            function setupAutocomplete(inputId) {
                const input = document.getElementById(inputId);
                const dropdown = input.parentElement.querySelector('.contact-dropdown');
                let debounce = null;

                input.addEventListener('input', function () {
                    clearTimeout(debounce);
                    const parts = input.value.split(',');
                    const current = parts[parts.length - 1].trim();

                    if (current.length < 2) {
                        dropdown.classList.add('hidden');
                        return;
                    }

                    debounce = setTimeout(() => {
                        fetch('{{ route('contacts.search') }}?q=' + encodeURIComponent(current), {
                            headers: { 'Accept': 'application/json' },
                        })
                            .then((r) => r.json())
                            .then((contacts) => {
                                if (!contacts.length) {
                                    dropdown.classList.add('hidden');
                                    return;
                                }

                                dropdown.innerHTML = '';
                                contacts.forEach((c) => {
                                    const item = document.createElement('button');
                                    item.type = 'button';
                                    item.className = 'block w-full text-left px-3 py-2 text-sm hover:bg-gray-100';
                                    item.textContent = c.name ? `${c.name} <${c.email}>` : c.email;
                                    item.addEventListener('click', () => {
                                        parts[parts.length - 1] = ' ' + c.email;
                                        input.value = parts.map((p) => p.trim()).join(', ');
                                        dropdown.classList.add('hidden');
                                        input.focus();
                                        scheduleSave();
                                    });
                                    dropdown.appendChild(item);
                                });
                                dropdown.classList.remove('hidden');
                            })
                            .catch(() => {});
                    }, 200);
                });

                document.addEventListener('click', (e) => {
                    if (!input.parentElement.contains(e.target)) {
                        dropdown.classList.add('hidden');
                    }
                });
            }

            setupAutocomplete('field-to');
            setupAutocomplete('field-cc');
        })();
    </script>
@endsection
