@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-2xl font-bold mb-6">Psychomotor Skills Assessment</h2>

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

        <form id="psychomotorSkillsForm" method="POST" action="{{ route('psychomotor-skills.store-bulk') }}">
            @csrf
            <!-- Psychomotor Skills Table (Desktop) -->
            <div class="overflow-x-auto hidden md:block">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Name
                            </th>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Admission No.
                            </th>
                            @foreach($psychomotorSkills as $skill)
                                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider text-center">
                                    {{ $skill->name }}
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200" id="psychomotorSkillsTableBody">
                        <!-- Rows will be populated by JavaScript -->
                    </tbody>
                </table>
            </div>

            <!-- Mobile Psychomotor Skills Cards -->
            <div id="mobilePsychomotorContainer" class="md:hidden space-y-4">
                <!-- Cards will be populated by JavaScript -->
            </div>


            <!-- Action Buttons -->
            <div class="mt-6 flex justify-between">
                <div>
                    <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700">
                        <i class="fas fa-save"></i> Save Changes
                    </button>
                </div>
            </div>
        </form>

        <!-- Filter stats -->
        <div class="mt-4 pt-4 border-t border-gray-200">
            <p class="text-sm text-gray-600">No. of Students: <span id="studentCount">0</span></p>
        </div>

        <!-- Rating Guide -->
        <div class="mt-4 p-4 bg-blue-50 border border-blue-200 rounded-md">
            <h3 class="font-semibold text-blue-900 mb-2">Rating Scale</h3>
            <div class="text-sm text-blue-800">
                <span class="font-medium">5:</span> Excellent &nbsp;&nbsp;
                <span class="font-medium">4:</span> Good &nbsp;&nbsp;
                <span class="font-medium">3:</span> Fair &nbsp;&nbsp;
                <span class="font-medium">2:</span> Poor &nbsp;&nbsp;
                <span class="font-medium">1:</span> Very Poor
            </div>
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
    const tableBody = document.getElementById('psychomotorSkillsTableBody');
    const mobileContainer = document.getElementById('mobilePsychomotorContainer');
    const psychomotorSkillsForm = document.getElementById('psychomotorSkillsForm');

    const studentCount = document.getElementById('studentCount');
    let allStudents = [];
    const psychomotorSkills = {!! json_encode($psychomotorSkills) !!};

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

    // Fetch students and their psychomotor skills
    async function fetchStudents() {
        const params = new URLSearchParams({
            class_id: classSelect.value,
            term_id: termSelect.value,
            search: searchInput.value
        });

        try {
            const response = await fetch(`{{ url('/psychomotor-skills/students') }}?${params}`, {
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
            renderStudentsTable();
        } catch (error) {
            console.error('Error fetching students:', error);
            showToast('Failed to fetch students. Please try again.', 'error');
        }
    }

    // Render students table
    function renderStudentsTable() {
        tableBody.innerHTML = '';
        mobileContainer.innerHTML = '';
        
        if (allStudents.length === 0) {
            mobileContainer.innerHTML = '<div class="text-center py-8 text-gray-500">No students found.</div>';
            tableBody.innerHTML = '<tr><td colspan="100%" class="text-center py-4 text-gray-500">No students found.</td></tr>';
            return;
        }
        
        allStudents.forEach(student => {
            const row = document.createElement('tr');
            let skillsHtml = '';

            psychomotorSkills.forEach(skill => {
                const score = student.skills[skill.id]?.score || '';
                skillsHtml += `
                    <td class="px-6 py-4 whitespace-nowrap text-center">
                        <input type="number" name="skills[${student.id}][${skill.id}]" 
                               value="${score}"
                               class="w-16 rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-center"
                               min="0" max="5" step="1">
                    </td>
                `;
            });

            row.innerHTML = `
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    ${student.full_name}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    ${student.adm_no}
                </td>
                ${skillsHtml}
            `;
            tableBody.appendChild(row);

            // ---------------------------------------------------------
            // Mobile Card View Rendering
            // ---------------------------------------------------------
            const card = document.createElement('div');
            card.className = "bg-gray-50 p-4 rounded-lg shadow-sm border border-gray-100";
            
            let mobileSkillsHtml = '<div class="grid grid-cols-2 gap-3 mt-3">';
            psychomotorSkills.forEach(skill => {
                const score = student.skills[skill.id]?.score || '';
                 mobileSkillsHtml += `
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1 truncate" title="${skill.name}">${skill.name}</label>
                        <input type="number" name="skills[${student.id}][${skill.id}]" 
                               value="${score}"
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-center"
                               min="0" max="5" step="1">
                    </div>
                `;
            });
            mobileSkillsHtml += '</div>';

            card.innerHTML = `
                <div class="flex justify-between items-start">
                    <div>
                        <h3 class="font-bold text-gray-900">${student.full_name}</h3>
                        <p class="text-xs text-gray-500">${student.adm_no}</p>
                    </div>
                </div>
                ${mobileSkillsHtml}
            `;
            mobileContainer.appendChild(card);
        });


        studentCount.textContent = allStudents.length;
    }

    // Event Listeners
    fetchButton.addEventListener('click', fetchStudents);
    
    ['class_id', 'term_id'].forEach(id => {
        document.getElementById(id).addEventListener('change', fetchStudents);
    });

    psychomotorSkillsForm.addEventListener('submit', async function(e) {
        e.preventDefault();

        function buildNestedSkills(form) {
            const data = {};
            for (const [key, value] of new FormData(form)) {
                const match = key.match(/^skills\[(.+?)\]\[(.+?)\]$/);
                if (match) {
                    const studentId = match[1];
                    const skillId = match[2];
                    data[studentId] = data[studentId] || {};
                    data[studentId][skillId] = value;
                }
            }
            return data;
        }

        try {
            const payload = {
                class_id: classSelect.value,
                term_id: termSelect.value,
                skills: buildNestedSkills(psychomotorSkillsForm)
            };

            const response = await fetch('{{ route('psychomotor-skills.store-bulk') }}', {
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
                showToast('Psychomotor skills saved successfully!', 'success');
                fetchStudents();
            } else {
                const json = await response.json().catch(() => ({}));
                console.error('Save failed', json);
                throw new Error('Failed to save psychomotor skills');
            }
        } catch (error) {
            console.error('Error saving psychomotor skills:', error);
            showToast('Failed to save psychomotor skills. Please try again.', 'error');
        }
    });

    // Initial load
    fetchStudents();
});
</script>
@endpush
