@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-2xl font-bold mb-6">Attendance</h2>

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

        <form id="attendanceForm" method="POST" action="{{ route('attendance.store-bulk') }}">
            @csrf
            <!-- Attendance Table -->
            <div class="overflow-x-auto">
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
                                No.OfAbsent
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200" id="attendanceTableBody">
                        <!-- Rows will be populated by JavaScript -->
                    </tbody>
                </table>
            </div>

            <!-- Action Buttons -->
            <div class="mt-6 flex justify-between">
                <div>
                    <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700">
                        <i class="fas fa-save"></i> Save Changes
                    </button>
                </div>
                <div class="space-x-2">
                    <button type="button" id="importAttendance" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                        <i class="fas fa-file-import"></i> Import Attendance
                    </button>
                    <button type="button" id="exportAttendance" class="bg-purple-600 text-white px-4 py-2 rounded-md hover:bg-purple-700">
                        <i class="fas fa-file-export"></i> Export Attendance
                    </button>
                </div>
            </div>
        </form>

        <!-- Filter stats -->
        <div class="mt-4 pt-4 border-t border-gray-200">
            <label for="filterStatus" class="block text-sm font-medium text-gray-700 mb-2">Filter</label>
            <select id="filterStatus" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                <option value="">All</option>
                <option value="absent">With Absences</option>
                <option value="present">Perfect Attendance</option>
            </select>
            <p class="mt-2 text-sm text-gray-600">No. of Students: <span id="studentCount">0</span></p>
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
    const tableBody = document.getElementById('attendanceTableBody');
    const attendanceForm = document.getElementById('attendanceForm');
    const filterStatus = document.getElementById('filterStatus');
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

    // Fetch students and their attendance
    async function fetchStudents() {
        const params = new URLSearchParams({
            class_id: classSelect.value,
            term_id: termSelect.value,
            search: searchInput.value
        });

        try {
            const response = await fetch(`{{ url('/attendance/students') }}?${params}`, {
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
            filterTable();
        } catch (error) {
            console.error('Error fetching students:', error);
            showToast('Failed to fetch students. Please try again.', 'error');
        }
    }

    // Filter and render table
    function filterTable() {
        const filter = filterStatus.value;
        let filtered = allStudents;

        if (filter === 'absent') {
            filtered = allStudents.filter(s => (s.attendance?.no_of_absences || 0) > 0);
        } else if (filter === 'present') {
            filtered = allStudents.filter(s => (s.attendance?.no_of_absences || 0) === 0);
        }

        renderStudentsTable(filtered);
        studentCount.textContent = filtered.length;
    }

    // Render students table
    function renderStudentsTable(students) {
        tableBody.innerHTML = '';
        
        students.forEach(student => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    ${student.adm_no}
                    <input type="hidden" name="attendance[${student.id}][student_id]" value="${student.id}">
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    ${student.full_name}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <input type="number" name="attendance[${student.id}][no_of_absences]" 
                           value="${student.attendance?.no_of_absences || ''}"
                           class="w-20 rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                           min="0" step="1">
                </td>
            `;
            tableBody.appendChild(row);
        });
    }

    // Event Listeners
    fetchButton.addEventListener('click', fetchStudents);
    
    ['class_id', 'term_id'].forEach(id => {
        document.getElementById(id).addEventListener('change', fetchStudents);
    });

    filterStatus.addEventListener('change', filterTable);

    attendanceForm.addEventListener('submit', async function(e) {
        e.preventDefault();

        function buildNestedAttendance(form) {
            const data = {};
            for (const [key, value] of new FormData(form)) {
                const match = key.match(/^attendance\[(.+?)\]\[(.+?)\]$/);
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
                term_id: termSelect.value,
                attendance: buildNestedAttendance(attendanceForm)
            };

            const response = await fetch('{{ route('attendance.store-bulk') }}', {
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
                showToast('Attendance saved successfully!', 'success');
                fetchStudents();
            } else {
                const json = await response.json().catch(() => ({}));
                console.error('Save failed', json);
                throw new Error('Failed to save attendance');
            }
        } catch (error) {
            console.error('Error saving attendance:', error);
            showToast('Failed to save attendance. Please try again.', 'error');
        }
    });

    // Initial load
    fetchStudents();
});
</script>
@endpush
