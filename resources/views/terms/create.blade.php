@extends('layouts.app')

@section('title', 'Create Term')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="py-6">
        <h1 class="text-2xl font-semibold text-gray-900">Create Term</h1>
    </div>

    <div class="bg-white shadow sm:rounded-lg p-6">
        <form action="{{ route('terms.store') }}" method="POST">
            @csrf
            <div class="max-w-md mx-auto">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Term</label>
                    <select name="name" id="term-name" class="mt-1 block w-full border-gray-300 rounded-md">
                        <option value="FIRST" {{ old('name') == 'FIRST TERM' ? 'selected' : '' }}>FIRST TERM</option>
                        <option value="SECOND" {{ old('name') == 'SECOND TERM' ? 'selected' : '' }}>SECOND TERM</option>
                        <option value="THIRD" {{ old('name') == 'THIRD TERM' ? 'selected' : '' }}>THIRD TERM</option>
                    </select>
                </div>

                {{-- Active session is used automatically. No session selector required. --}}

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Term Begins</label>
                    <input type="date" name="begins_at" value="{{ old('begins_at') }}" class="mt-1 block w-full border-gray-300 rounded-md">
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Term Ends</label>
                    <input type="date" name="ends_at" value="{{ old('ends_at') }}" class="mt-1 block w-full border-gray-300 rounded-md">
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">No. of Times School Opens</label>
                    <input type="number" name="times_open" value="{{ old('times_open') }}" min="0" class="mt-1 block w-full border-gray-300 rounded-md">
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Terminal Duration</label>
                    <input type="text" name="terminal_duration" value="{{ old('terminal_duration') }}" class="mt-1 block w-full border-gray-300 rounded-md" placeholder="e.g. 2 weeks">
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700">Next Term Begins</label>
                    <input type="date" name="next_term_begins" value="{{ old('next_term_begins') }}" class="mt-1 block w-full border-gray-300 rounded-md">
                </div>

                <div class="text-center">
                    <button class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-md">
                        <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
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

    const fields = {
        begins_at: document.querySelector('input[name="begins_at"]'),
        ends_at: document.querySelector('input[name="ends_at"]'),
        times_open: document.querySelector('input[name="times_open"]'),
        terminal_duration: document.querySelector('input[name="terminal_duration"]'),
        next_term_begins: document.querySelector('input[name="next_term_begins"]')
    };

    async function loadTerm() {
        const name = termSelect.value;
        const params = new URLSearchParams({ name: name });
        const url = `{{ route('terms.fetch') }}?${params.toString()}`;

        try {
            const res = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            if (!res.ok) throw new Error('Network response was not ok');
            const data = await res.json();

            // populate fields
            fields.begins_at.value = data.begins_at ? data.begins_at.split('T')[0] : '';
            fields.ends_at.value = data.ends_at ? data.ends_at.split('T')[0] : '';
            fields.times_open.value = data.times_open ?? '';
            fields.terminal_duration.value = data.terminal_duration ?? '';
            fields.next_term_begins.value = data.next_term_begins ? data.next_term_begins.split('T')[0] : '';

        } catch (err) {
            console.error('Failed to load term data', err);
        }
    }

    // load on page load
    loadTerm();

    termSelect.addEventListener('change', loadTerm);
});
</script>
@endpush
