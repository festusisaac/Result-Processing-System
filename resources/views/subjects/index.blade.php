@extends('layouts.app')

@section('title', 'Subjects')
@section('page-title', 'Subjects')
@section('page-description', 'Manage subjects and curriculum')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-semibold text-gray-900">Subjects</h1>
        <div class="flex space-x-2">
            <a href="{{ route('subjects.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Add Subject
            </a>
            <a href="{{ route('subjects.export') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                Export
            </a>
            <button type="button" onclick="document.getElementById('import-modal').classList.remove('hidden')" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                Import
            </button>
        </div>
    </div>

    <!-- Search and Filter -->
    <div class="mt-4 bg-white p-4 rounded-md shadow">
        <div class="flex flex-col sm:flex-row gap-4">
            <div class="flex-1">
                <input type="text" id="search-input" placeholder="Search subjects..." value="{{ $searchQuery ?? '' }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div class="sm:w-48">
                <select id="class-filter" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">All Classes</option>
                    @foreach($classes as $class)
                    <option value="{{ $class->id }}" {{ $selectedClassId == $class->id ? 'selected' : '' }}>{{ $class->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    @if(session('error'))
    <div class="mt-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
        <span class="block sm:inline">{{ session('error') }}</span>
    </div>
    @endif

    <div id="subjects-container" class="mt-6">
        @include('subjects.partials.subjects-list')
    </div>
</div>

<!-- Import Modal -->
<div id="import-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Import Subjects</h3>
            <form action="{{ route('subjects.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-4">
                    <label for="file" class="block text-sm font-medium text-gray-700">Select Excel File</label>
                    <input type="file" name="file" id="file" accept=".xlsx,.xls" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div class="flex justify-end space-x-2">
                    <button type="button" onclick="document.getElementById('import-modal').classList.add('hidden')" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">Import</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('search-input');
    const classFilter = document.getElementById('class-filter');

    function updateSubjects() {
        const query = searchInput.value;
        const classId = classFilter.value;
        
        const params = new URLSearchParams();
        if (query) params.append('q', query);
        if (classId) params.append('class_id', classId);
        
        fetch(`{{ route('subjects.search') }}?${params.toString()}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    updateSubjectsList(data.groupedSubjects);
                } else {
                    console.error('Error:', data.message);
                    document.getElementById('subjects-container').innerHTML = `
                        <div class="bg-white shadow overflow-hidden sm:rounded-md">
                            <div class="px-4 py-4 sm:px-6 text-center text-red-500">
                                ${data.message || 'An error occurred while searching subjects'}
                            </div>
                        </div>
                    `;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('subjects-container').innerHTML = `
                    <div class="bg-white shadow overflow-hidden sm:rounded-md">
                        <div class="px-4 py-4 sm:px-6 text-center text-red-500">
                            An error occurred while searching subjects
                        </div>
                    </div>
                `;
            });
    }

    let searchTimeout;
    searchInput.addEventListener('keyup', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(updateSubjects, 300); // 300ms delay
    });
    classFilter.addEventListener('change', updateSubjects);

    function updateSubjectsList(groupedSubjects) {
        const container = document.getElementById('subjects-container');
        let html = '';

        if (Object.keys(groupedSubjects).length === 0) {
            html = '<div class="bg-white shadow overflow-hidden sm:rounded-md"><div class="px-4 py-4 sm:px-6 text-center text-gray-500">No subjects found.</div></div>';
        } else {
            for (const [groupName, subjects] of Object.entries(groupedSubjects)) {
                html += `
                    <div class="bg-white shadow overflow-hidden sm:rounded-md mb-6">
                        <div class="bg-gray-50 px-4 py-3 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900">${groupName}</h3>
                        </div>
                        <ul class="divide-y divide-gray-200">
                `;

                subjects.forEach(subject => {
                    html += `
                        <li>
                            <div class="px-4 py-4 sm:px-6">
                                <div class="flex items-center justify-between">
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-indigo-600 truncate">${subject.name}</p>
                                        <p class="mt-1 text-sm text-gray-500">
                                            Teacher: ${subject.teacher ? subject.teacher.name : 'Not Assigned'} |
                                            Class: ${subject.classRoom ? subject.classRoom.name : 'Not Assigned'}
                                        </p>
                                    </div>
                                    <div class="ml-4 flex-shrink-0 flex">
                                        <a href="/subjects/${subject.id}/edit" class="mr-2 inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                            Edit
                                        </a>
                                        <form action="/subjects/${subject.id}" method="POST" class="inline js-confirm-delete" data-confirm="Are you sure you want to delete this subject?">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex items-center px-3 py-2 border border-red-300 shadow-sm text-sm font-medium rounded-md text-red-700 bg-white hover:bg-red-50">
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </li>
                    `;
                });

                html += `
                        </ul>
                    </div>
                `;
            }
        }

        container.innerHTML = html;
    }
});
</script>
@endsection
