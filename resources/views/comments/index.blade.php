@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-2xl font-bold mb-6">Student Comments</h2>

        <!-- Filters -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <div>
                <label for="class_id" class="block text-sm font-medium text-gray-700">Class</label>
                <select id="class_id" name="class_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    @foreach($classes as $class)
                        <option value="{{ $class->id }}">{{ $class->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="term_id" class="block text-sm font-medium text-gray-700">Term</label>
                <select id="term_id" name="term_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    @foreach($terms as $term)
                        <option value="{{ $term->id }}">{{ $term->term_name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Search bar -->
        <div class="flex justify-between items-center mb-6">
            <div class="flex-1 mr-4">
                <input type="text" id="search" placeholder="Search by admission number or name..." 
                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
            </div>
            <button type="button" id="fetchStudents" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                <i class="fas fa-search"></i> Search
            </button>
        </div>

        <form id="commentsForm" method="POST" action="{{ route('comments.store-bulk') }}">
            @csrf
            <!-- Comments Section -->
            <div id="commentsContainer" class="space-y-6">
                <!-- Students with comment sections will be populated here -->
            </div>

            <!-- Action Buttons -->
            <div class="mt-6 flex justify-between">
                <div>
                    <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700">
                        <i class="fas fa-save"></i> Save Comments
                    </button>
                </div>
            </div>
        </form>

        <!-- Stats -->
        <div class="mt-4 pt-4 border-t border-gray-200">
            <p class="text-sm text-gray-600">No. of Students: <span id="studentCount">0</span></p>
        </div>
    </div>
</div>

<!-- Toast container -->
<div id="toastContainer" class="fixed top-6 right-6 z-50 space-y-2"></div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const classSelect = document.getElementById('class_id');
    const termSelect = document.getElementById('term_id');
    const searchInput = document.getElementById('search');
    const fetchButton = document.getElementById('fetchStudents');
    const commentsContainer = document.getElementById('commentsContainer');
    const commentsForm = document.getElementById('commentsForm');
    const studentCount = document.getElementById('studentCount');
    let allStudents = [];

    // Toast helper
    function showToast(message, type = 'success', duration = 4000) {
        const container = document.getElementById('toastContainer');
        if (!container) return;

        const id = `toast-${Date.now()}`;
        const bgClass = type === 'error' ? 'bg-red-50' : 'bg-green-50';
        const borderClass = type === 'error' ? 'border-red-500' : 'border-green-500';

        const toast = document.createElement('div');
        toast.id = id;
        toast.className = `max-w-sm w-full ${bgClass} border-l-4 ${borderClass} rounded-md shadow-md p-3 flex items-start space-x-2 transform translate-x-4 opacity-0 transition-all duration-300`;

        toast.innerHTML = `
            <div class="flex-1 text-sm text-gray-800">${message}</div>
            <button type="button" aria-label="close" class="text-gray-500 hover:text-gray-700 ml-2">&times;</button>
        `;

        toast.querySelector('button')?.addEventListener('click', () => removeToast(toast));
        container.appendChild(toast);

        requestAnimationFrame(() => {
            toast.classList.remove('translate-x-4', 'opacity-0');
            toast.classList.add('translate-x-0', 'opacity-100');
        });

        const timer = setTimeout(() => removeToast(toast), duration);

        function removeToast(el) {
            clearTimeout(timer);
            el.classList.add('translate-x-4', 'opacity-0');
            setTimeout(() => el.remove(), 300);
        }
    }

    // Fetch students and their comments
    async function fetchStudents() {
        const params = new URLSearchParams({
            class_id: classSelect.value,
            term_id: termSelect.value,
            search: searchInput.value
        });

        try {
            const response = await fetch(`{{ url('/comments/students') }}?${params}`, {
                credentials: 'same-origin',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (!response.ok) {
                const json = await response.json().catch(() => ({}));
                console.error('Fetch students failed', json);
                throw new Error('Failed to fetch students');
            }

            const data = await response.json();
            allStudents = data;
            renderStudentsComments();
        } catch (error) {
            console.error('Error fetching students:', error);
            showToast('Failed to fetch students. Please try again.', 'error');
        }
    }

    // Render students with comment sections
    function renderStudentsComments() {
        commentsContainer.innerHTML = '';
        
        allStudents.forEach(student => {
            const lastComment = student.comments && student.comments.length > 0 ? student.comments[0] : null;
            
            const studentSection = document.createElement('div');
            studentSection.className = 'bg-gray-50 p-4 rounded-lg border border-gray-200';
            studentSection.innerHTML = `
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">${student.full_name}</h3>
                        <p class="text-sm text-gray-500">Admission No: ${student.adm_no}</p>
                    </div>
                </div>
                
                ${lastComment ? `
                    <div class="bg-blue-50 p-3 rounded mb-4 border border-blue-200">
                        <p class="text-sm text-gray-600 mb-1"><strong>Last Comment:</strong> ${lastComment.body}</p>
                        <p class="text-xs text-gray-500">By ${lastComment.author_name} on ${lastComment.created_at}</p>
                    </div>
                ` : ''}
                
                <div>
                    <label for="comment_${student.id}" class="block text-sm font-medium text-gray-700 mb-2">Add Comment</label>
                    <textarea 
                        id="comment_${student.id}"
                        name="comments[${student.id}][body]"
                        rows="3"
                        placeholder="Enter your comment here..."
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                    ></textarea>
                </div>
            `;
            
            commentsContainer.appendChild(studentSection);
        });

        studentCount.textContent = allStudents.length;
    }

    // Event Listeners
    fetchButton.addEventListener('click', fetchStudents);
    
    ['class_id', 'term_id'].forEach(id => {
        document.getElementById(id).addEventListener('change', fetchStudents);
    });

    commentsForm.addEventListener('submit', async function(e) {
        e.preventDefault();

        function buildComments(form) {
            const data = {};
            for (const [key, value] of new FormData(form)) {
                const match = key.match(/^comments\[(.+?)\]\[(.+?)\]$/);
                if (match && value.trim()) {
                    const studentId = match[1];
                    const field = match[2];
                    data[studentId] = data[studentId] || {};
                    data[studentId][field] = value;
                }
            }
            return data;
        }

        try {
            const payload = {
                class_id: classSelect.value,
                term_id: termSelect.value,
                comments: buildComments(commentsForm)
            };

            if (Object.keys(payload.comments).length === 0) {
                showToast('Please enter at least one comment', 'error');
                return;
            }

            const response = await fetch('{{ route('comments.store-bulk') }}', {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(payload)
            });

            if (response.ok) {
                showToast('Comments saved successfully!', 'success');
                fetchStudents();
                // Clear textarea values
                document.querySelectorAll('textarea').forEach(ta => ta.value = '');
            } else {
                const json = await response.json().catch(() => ({}));
                console.error('Save failed', json);
                throw new Error('Failed to save comments');
            }
        } catch (error) {
            console.error('Error saving comments:', error);
            showToast('Failed to save comments. Please try again.', 'error');
        }
    });

    // Initial load
    fetchStudents();
});
</script>
@endpush

