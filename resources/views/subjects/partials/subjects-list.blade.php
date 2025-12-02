@if($groupedSubjects->isEmpty())
<div class="bg-white shadow overflow-hidden sm:rounded-md">
    <div class="px-4 py-4 sm:px-6 text-center text-gray-500">
        No subjects found. Create one to get started.
    </div>
</div>
@else
@foreach($groupedSubjects as $groupName => $subjects)
<div class="bg-white shadow overflow-hidden sm:rounded-md mb-6">
    <div class="bg-gray-50 px-4 py-3 border-b border-gray-200">
        <h3 class="text-lg font-medium text-gray-900">{{ $groupName }}</h3>
    </div>
    <ul class="divide-y divide-gray-200">
        @foreach($subjects as $subject)
        <li>
            <div class="px-4 py-4 sm:px-6">
                <div class="flex items-center justify-between">
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-indigo-600 truncate">{{ $subject->name }}</p>
                        <p class="mt-1 text-sm text-gray-500">
                            Teacher: {{ $subject->teacher ? $subject->teacher->name : 'Not Assigned' }} |
                            Class: {{ $subject->classRoom ? $subject->classRoom->name : 'Not Assigned' }}
                        </p>
                    </div>
                    <div class="ml-4 flex-shrink-0 flex">
                        <a href="{{ route('subjects.edit', $subject) }}" class="mr-2 inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            Edit
                        </a>
                        <form action="{{ route('subjects.destroy', $subject) }}" method="POST" class="inline js-confirm-delete" data-confirm="Are you sure you want to delete this subject?">
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
        @endforeach
    </ul>
</div>
@endforeach
@endif
