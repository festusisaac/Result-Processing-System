@extends('layouts.app')

@section('title', 'Term Settings')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Toast notification -->
    <div id="toast" class="fixed right-4 top-4 transform transition-transform duration-300 ease-in-out translate-x-full">
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg shadow-lg">
            <span class="font-medium" id="toast-message"></span>
        </div>
    </div>

    <div class="py-6">
        <h1 class="text-2xl font-semibold text-gray-900">Term Settings</h1>
    </div>

    <div class="bg-white shadow sm:rounded-lg p-6">
        <form method="POST" action="{{ route('terms.store') }}" id="term-form">
            @csrf

            <div id="form-errors" class="mb-4 hidden">
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <ul class="list-disc list-inside"></ul>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Term</label>
                    <select name="term_name" id="term-name" class="mt-1 block w-full border-gray-300 rounded-md">
                        <option value="FIRST TERM" {{ $selected == 'FIRST TERM' ? 'selected' : '' }}>FIRST TERM</option>
                        <option value="SECOND TERM" {{ $selected == 'SECOND TERM' ? 'selected' : '' }}>SECOND TERM</option>
                        <option value="THIRD TERM" {{ $selected == 'THIRD TERM' ? 'selected' : '' }}>THIRD TERM</option>
                    </select>
                    @error('term_name') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Term Begins</label>
                    <input type="date" name="term_begins" id="term_begins" value="{{ old('term_begins', optional($terms->get($selected))->term_begins?->format('Y-m-d')) }}" class="mt-1 block w-full border-gray-300 rounded-md">
                    @error('term_begins') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Term Ends</label>
                    <input type="date" name="term_ends" id="term_ends" value="{{ old('term_ends', optional($terms->get($selected))->term_ends?->format('Y-m-d')) }}" class="mt-1 block w-full border-gray-300 rounded-md">
                    @error('term_ends') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">No. of Times School Opens</label>
                    <input type="number" name="school_opens" id="school_opens" min="0" value="{{ old('school_opens', $terms->get($selected)->school_opens ?? '') }}" class="mt-1 block w-full border-gray-300 rounded-md">
                    @error('school_opens') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Terminal Duration</label>
                    <input type="text" name="terminal_duration" id="terminal_duration" value="{{ old('terminal_duration', $terms->get($selected)->terminal_duration ?? '') }}" class="mt-1 block w-full border-gray-300 rounded-md" placeholder="e.g. 2 weeks">
                    @error('terminal_duration') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Next Term Begins</label>
                    <input type="date" name="next_term_begins" id="next_term_begins" value="{{ old('next_term_begins', optional($terms->get($selected))->next_term_begins?->format('Y-m-d')) }}" class="mt-1 block w-full border-gray-300 rounded-md">
                    @error('next_term_begins') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
                </div>

                <div class="text-center mt-4">
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-md">
                        Save Changes
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const termSelect = document.getElementById('term-name');
    const form = document.getElementById('term-form');
    const formErrors = document.getElementById('form-errors');
    const toast = document.getElementById('toast');
    const toastMessage = document.getElementById('toast-message');

    const fields = {
        term_begins: document.getElementById('term_begins'),
        term_ends: document.getElementById('term_ends'),
        school_opens: document.getElementById('school_opens'),
        terminal_duration: document.getElementById('terminal_duration'),
        next_term_begins: document.getElementById('next_term_begins')
    };

    function showToast(message) {
        try {
            if (toast && toastMessage) {
                toastMessage.textContent = message;
                toast.classList.remove('translate-x-full');
                setTimeout(() => {
                    toast.classList.add('translate-x-full');
                }, 3000);
                console.log('Term page toast shown:', message);
                return;
            }

            // Fallback to global showToast if available
            if (typeof window.showToast === 'function') {
                window.showToast(message, 'success');
                console.log('Fallback to global showToast:', message);
                return;
            }

            // Last-resort: create a minimal dynamic toast
            const fallback = document.createElement('div');
            fallback.className = 'fixed top-4 right-4 z-50 bg-green-500 text-white px-4 py-2 rounded shadow';
            fallback.textContent = message;
            document.body.appendChild(fallback);
            setTimeout(() => fallback.remove(), 4000);
            console.log('Created dynamic fallback toast:', message);
        } catch (e) {
            console.error('showToast error:', e, message);
            // try global as a last attempt
            if (typeof window.showToast === 'function') window.showToast(message, 'success');
        }
    }

    function showErrors(errors) {
        const ul = formErrors.querySelector('ul');
        ul.innerHTML = '';
        Object.values(errors).forEach(error => {
            const li = document.createElement('li');
            li.textContent = error[0];
            ul.appendChild(li);
        });
        formErrors.classList.remove('hidden');
    }

    function clearErrors() {
        formErrors.classList.add('hidden');
        formErrors.querySelector('ul').innerHTML = '';
    }

    async function loadTerm(name) {
        const url = `{{ route('terms.fetch') }}?term_name=${encodeURIComponent(name)}`;
        try {
            const res = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            if (!res.ok) throw new Error('Network response was not ok');
            const data = await res.json();
            if (!data.exists) {
                fields.term_begins.value = '';
                fields.term_ends.value = '';
                fields.school_opens.value = '';
                fields.terminal_duration.value = '';
                fields.next_term_begins.value = '';
                return;
            }

            const t = data.term;
            fields.term_begins.value = t.term_begins ? t.term_begins.split('T')[0] : '';
            fields.term_ends.value = t.term_ends ? t.term_ends.split('T')[0] : '';
            fields.school_opens.value = t.school_opens ?? '';
            fields.terminal_duration.value = t.terminal_duration ?? '';
            fields.next_term_begins.value = t.next_term_begins ? t.next_term_begins.split('T')[0] : '';
        } catch (err) {
            console.error('Failed to load term data', err);
            showToast('Failed to load term data');
        }
    }

    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        clearErrors();

        try {
            const response = await fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: new FormData(form)
            });

            const data = await response.json();
            console.log('Term save response', response.status, data);

            if (!response.ok) {
                if (response.status === 422) {
                    showErrors(data.errors);
                } else {
                    throw new Error(data.message || 'An error occurred');
                }
                return;
            }

            // Prefer the global toast utility if available (more reliable)
            if (typeof window.showToast === 'function') {
                window.showToast('Term settings saved successfully', 'success');
                console.log('Used global showToast for term save');
            } else {
                showToast('Term settings saved successfully');
                console.log('Used local showToast for term save');
            }
        } catch (error) {
            console.error('Submission error:', error);
            showToast('Failed to save term settings');
        }
    });

    termSelect.addEventListener('change', function () {
        loadTerm(this.value);
        clearErrors();
    });
});
</script>
@endpush
