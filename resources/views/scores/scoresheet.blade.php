@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-2xl font-bold mb-6 hidden md:block">Marks / Scores</h2>

        <!-- Filters -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div>
                <label for="class_id" class="block text-sm font-medium text-gray-700">Class</label>
                <select id="class_id" name="class_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    @foreach($classes as $class)
                        <option value="{{ $class->id }}">{{ $class->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="subject_id" class="block text-sm font-medium text-gray-700">Subject</label>
                <select id="subject_id" name="subject_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    @foreach($subjects as $subject)
                        <option value="{{ $subject->id }}">{{ $subject->name }}</option>
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

        <form id="scoreForm" method="POST" action="{{ route('scores.store-bulk') }}">
            @csrf
            <!-- Scores Table (Desktop) -->
            <div class="overflow-x-auto hidden md:block">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Admission No.
                            </th>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Name
                            </th>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                CA1 (20)
                            </th>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                CA2 (20)
                            </th>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Exam (60)
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200" id="scoresTableBody">
                        <!-- Rows will be populated by JavaScript -->
                    </tbody>
                </table>
            </div>

            <!-- Mobile Scores Cards -->
            <div id="mobileScoresContainer" class="md:hidden space-y-4">
                <!-- Cards will be populated by JavaScript -->
            </div>


            <!-- Action Buttons -->
            <div class="mt-6 flex justify-between">
                <div>
                    <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700">
                        <i class="fas fa-save"></i> Save Changes
                    </button>
                </div>
                <div class="space-x-2">
                    <button type="button" id="importScores" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                        <i class="fas fa-file-import"></i> Import Scoresheet
                    </button>
                    <button type="button" id="exportScores" class="bg-purple-600 text-white px-4 py-2 rounded-md hover:bg-purple-700">
                        <i class="fas fa-file-export"></i> Export Scoresheet
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Toast container -->
<div id="toastContainer" class="fixed top-6 right-6 z-50 space-y-2"></div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const classSelect = document.getElementById('class_id');
    const subjectSelect = document.getElementById('subject_id');
    const termSelect = document.getElementById('term_id');
    const searchInput = document.getElementById('search');
    const fetchButton = document.getElementById('fetchStudents');
    const tableBody = document.getElementById('scoresTableBody');
    const mobileContainer = document.getElementById('mobileScoresContainer');
    const scoreForm = document.getElementById('scoreForm');


    // Fetch students and their scores
    async function fetchStudents() {
        const params = new URLSearchParams({
            class_id: classSelect.value,
            subject_id: subjectSelect.value,
            term_id: termSelect.value,
            search: searchInput.value
        });

        try {
            const response = await fetch(`{{ url('/scores/students') }}?${params}`, {
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
            renderStudentsTable(data);
        } catch (error) {
            console.error('Error fetching students:', error);
            showToast('Failed to fetch students. Please try again.', 'error');
        }
    }

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

        // Close handler
        toast.querySelector('button')?.addEventListener('click', () => removeToast(toast));

        container.appendChild(toast);

        // Trigger enter animation
        requestAnimationFrame(() => {
            toast.classList.remove('translate-x-4', 'opacity-0');
            toast.classList.add('translate-x-0', 'opacity-100');
        });

        // Auto remove
        const timer = setTimeout(() => removeToast(toast), duration);

        function removeToast(el) {
            clearTimeout(timer);
            el.classList.add('translate-x-4', 'opacity-0');
            setTimeout(() => el.remove(), 300);
        }
    }

    // Render students table
    function renderStudentsTable(students) {
        tableBody.innerHTML = '';
        mobileContainer.innerHTML = '';
        
        if (students.length === 0) {
            mobileContainer.innerHTML = '<div class="text-center py-8 text-gray-500">No students found.</div>';
            tableBody.innerHTML = '<tr><td colspan="5" class="text-center py-4 text-gray-500">No students found.</td></tr>';
            return;
        }
        
        students.forEach(student => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    ${student.adm_no}
                    <input type="hidden" name="scores[${student.id}][student_id]" value="${student.id}">
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    ${student.full_name}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <input type="number" name="scores[${student.id}][ca1_score]" 
                           value="${student.score?.ca1_score || ''}"
                           class="w-20 rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                           min="0" max="20" step="0.01">
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <input type="number" name="scores[${student.id}][ca2_score]" 
                           value="${student.score?.ca2_score || ''}"
                           class="w-20 rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                           min="0" max="20" step="0.01">
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <input type="number" name="scores[${student.id}][exam_score]" 
                           value="${student.score?.exam_score || ''}"
                           class="w-20 rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                           min="0" max="60">
                </td>
            `;
            tableBody.appendChild(row);

            // ---------------------------------------------------------
            // Mobile Card View Rendering
            // ---------------------------------------------------------
            const card = document.createElement('div');
            card.className = "bg-gray-50 p-4 rounded-lg shadow-sm border border-gray-100";
            card.innerHTML = `
                <div class="flex justify-between items-start mb-3">
                    <div>
                        <h3 class="font-bold text-gray-900">${student.full_name}</h3>
                        <p class="text-xs text-gray-500">${student.adm_no}</p>
                        <input type="hidden" name="scores[${student.id}][student_id]" value="${student.id}">
                    </div>
                </div>
                <div class="grid grid-cols-3 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">CA1</label>
                        <input type="number" name="scores[${student.id}][ca1_score]" 
                               value="${student.score?.ca1_score || ''}"
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-center"
                               placeholder="-" min="0" max="20" step="0.01">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">CA2</label>
                        <input type="number" name="scores[${student.id}][ca2_score]" 
                               value="${student.score?.ca2_score || ''}"
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-center"
                               placeholder="-" min="0" max="20" step="0.01">
                    </div>
                     <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Exam</label>
                        <input type="number" name="scores[${student.id}][exam_score]" 
                               value="${student.score?.exam_score || ''}"
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-center font-bold"
                               placeholder="-" min="0" max="60">
                    </div>
                </div>
            `;
            mobileContainer.appendChild(card);
        });

    }

    // Event Listeners
    fetchButton.addEventListener('click', fetchStudents);
    
    ['class_id', 'subject_id', 'term_id'].forEach(id => {
        document.getElementById(id).addEventListener('change', fetchStudents);
    });

    scoreForm.addEventListener('submit', async function(e) {
        e.preventDefault();

        function buildNestedScores(form) {
            const data = {};
            for (const [key, value] of new FormData(form)) {
                // key looks like scores[<id>][field]
                const match = key.match(/^scores\[(.+?)\]\[(.+?)\]$/);
                if (match) {
                    const id = match[1];
                    const field = match[2];
                    data[id] = data[id] || {};
                    data[id][field] = value;
                }
            }
            return data;
        }

        try {
            const payload = {
                class_id: classSelect.value,
                subject_id: subjectSelect.value,
                term_id: termSelect.value,
                scores: buildNestedScores(scoreForm)
            };

            const response = await fetch('{{ route('scores.store-bulk') }}', {
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
                showToast('Scores saved successfully!', 'success');
                fetchStudents(); // Refresh the table
            } else {
                const json = await response.json().catch(() => ({}));
                console.error('Save failed', json);
                throw new Error('Failed to save scores');
            }
        } catch (error) {
            console.error('Error saving scores:', error);
            showToast('Failed to save scores. Please try again.', 'error');
        }
    });

    // Initial load
    fetchStudents();
});
</script>
@endpush