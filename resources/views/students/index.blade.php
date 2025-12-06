@extends('layouts.app')

@section('title', 'Students')
@section('page-title', 'Students')
@section('page-description', 'Manage student records and information')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex items-center justify-between py-6">
        <h1 class="text-2xl font-semibold text-gray-900">Students</h1>
        <div class="flex items-center gap-3">
            <form method="GET" action="{{ route('students.index') }}" class="flex items-center gap-2">
                <input type="text" name="q" value="{{ isset($q) ? $q : request('q') }}" placeholder="Search by name, admission no or class" class="px-3 py-2 border rounded-md" />
                <select name="class_id" class="px-3 py-2 border rounded-md">
                    <option value="">All classes</option>
                    @foreach($classes as $class)
                        <option value="{{ $class->id }}" {{ (isset($classId) && $classId == $class->id) || request('class_id') == $class->id ? 'selected' : '' }}>{{ $class->name }}</option>
                    @endforeach
                </select>
                <button type="submit" class="ml-2 px-3 py-2 bg-indigo-600 text-white rounded-md">Search</button>
                @if((request()->has('q') && request('q') !== '') || request()->has('class_id') && request('class_id') !== '')
                    <a href="{{ route('students.index') }}" class="ml-2 px-3 py-2 bg-gray-200 text-gray-700 rounded-md">Clear</a>
                @endif
            </form>
            @if(auth()->user()->isAdmin())
            <a href="{{ route('students.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md">Add Student</a>
            @endif
        </div>
    </div>

    @php $user = auth()->user(); @endphp
    @if($user && ($user->role === 'admin' || $user->isTeacher()))
    <div class="mb-4 flex gap-2">
        <button id="promote-selected-btn" class="px-4 py-2 bg-green-600 text-white rounded-md" style="display:none;">Promote Selected</button>
        <span id="selected-count" class="text-sm text-gray-600" style="display:none;">Selected: <strong>0</strong></span>
    </div>
    @endif

    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    @php $user = auth()->user(); @endphp
                    @if($user && ($user->role === 'admin' || $user->isTeacher()))
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"><input type="checkbox" id="select-all" /></th>
                    @endif
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Adm No</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Class</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Session</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($students as $student)
                <tr>
                    @php $user = auth()->user(); @endphp
                    @if($user && ($user->role === 'admin' || $user->isTeacher()))
                    <td class="px-6 py-4 whitespace-nowrap"><input type="checkbox" class="student-checkbox" value="{{ $student->id }}" /></td>
                    @endif
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $student->adm_no }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $student->full_name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $student->classRoom->name ?? '-' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $student->session->name ?? '-' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right">
                        <a href="{{ route('students.edit', $student) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                        @if(auth()->user()->isAdmin())
                        <form action="{{ route('students.destroy', $student) }}" method="POST" class="inline-block js-confirm-delete" data-confirm="Delete this student?">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                        </form>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $students->links() }}
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAllCheckbox = document.getElementById('select-all');
    const studentCheckboxes = document.querySelectorAll('.student-checkbox');
    const promoteBtn = document.getElementById('promote-selected-btn');
    const selectedCount = document.getElementById('selected-count');

    if (!selectAllCheckbox || studentCheckboxes.length === 0) return;

    function updateUI() {
        const checked = Array.from(studentCheckboxes).filter(c => c.checked).length;
        if (selectedCount) selectedCount.querySelector('strong').textContent = checked;
        if (promoteBtn) promoteBtn.style.display = checked > 0 ? 'inline-block' : 'none';
        if (selectedCount) selectedCount.style.display = checked > 0 ? 'inline-block' : 'none';
        selectAllCheckbox.checked = checked === studentCheckboxes.length && checked > 0;
    }

    selectAllCheckbox.addEventListener('change', function() {
        studentCheckboxes.forEach(cb => cb.checked = this.checked);
        updateUI();
    });

    studentCheckboxes.forEach(cb => cb.addEventListener('change', updateUI));

    promoteBtn.addEventListener('click', async function() {
        const ids = Array.from(studentCheckboxes).filter(c => c.checked).map(c => c.value);
        if (ids.length === 0) {
            if (window.showToast) window.showToast('No students selected', 'warning');
            else alert('No students selected');
            return;
        }

        if (!confirm(`Promote ${ids.length} student(s) to the next class?`)) return;

        try {
            const res = await fetch("{{ route('students.bulk-promote') }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ student_ids: ids })
            });

            const body = await res.json();
            if (!res.ok) {
                if (window.showToast) window.showToast(body.message || 'Failed to promote', 'error');
                else alert(body.message || 'Failed to promote');
                return;
            }

            // Show summary
            const msg = `${body.promoted} promoted, ${body.failed} failed`;
            if (window.showToast) window.showToast(msg, 'success');
            else alert(msg);

            // Reload page after short delay
            setTimeout(() => location.reload(), 1500);
        } catch (e) {
            console.error('Bulk promote error', e);
            if (window.showToast) window.showToast('Failed to promote students', 'error');
            else alert('Failed to promote students');
        }
    });
});
</script>
@endpush
@endsection
