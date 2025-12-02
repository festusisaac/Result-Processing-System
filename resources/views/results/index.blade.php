@extends('layouts.app')

@section('title', 'View Results')
@section('page-title', 'View Results')
@section('page-description', 'Browse and manage student results')

@section('content')
<style>
    .toast {
        position: fixed;
        top: 20px;
        right: 20px;
        max-width: 400px;
        padding: 16px 20px;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        animation: slideIn 0.3s ease-out;
        z-index: 9999;
        font-size: 14px;
        background: #10b981; /* unified green background */
        color: white;
    }
    /* Keep individual classes but use the same green tone for consistency */
    .toast.success { background: #10b981; color: white; }
    .toast.info    { background: #10b981; color: white; }
    .toast.warning { background: #10b981; color: white; }
    .toast.error   { background: #10b981; color: white; }
    @keyframes slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    @keyframes slideOut {
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }
    .toast.removing {
        animation: slideOut 0.3s ease-out forwards;
    }
</style>

<div class="container mx-auto px-4 py-8">
    <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">
        <!-- Left Sidebar: Class and Student Selection -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-md p-6">
                <!-- Class Selection -->
                <div class="mb-6">
                    <label for="classSelect" class="block text-sm font-semibold text-gray-700 mb-2">Class</label>
                    <select id="classSelect" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Select Class</option>
                        @foreach($classes as $class)
                            <option value="{{ $class->id }}">{{ $class->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Term Selection -->
                <div class="mb-6">
                    <label for="termSelect" class="block text-sm font-semibold text-gray-700 mb-2">Term</label>
                    <select id="termSelect" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Select Term</option>
                        @foreach($terms as $term)
                            <option value="{{ $term->id }}">{{ $term->term_name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Students List -->
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Students</label>
                    <div id="studentsList" class="border border-gray-300 rounded-lg overflow-hidden">
                        <div class="bg-gray-100 px-3 py-2 text-sm text-gray-600">Select class and term</div>
                    </div>
                </div>

                <!-- Number of Students -->
                <div class="text-sm text-gray-600">
                    No. of Students: <span id="studentCount">0</span>
                </div>
            </div>
        </div>

        <!-- Right Content: Result Details -->
        <div class="lg:col-span-4">
            <div class="bg-white rounded-lg shadow-md p-6">
                <!-- Student Header -->
                <div class="mb-6 pb-6 border-b border-gray-200">
                    <div class="flex justify-between items-start">
                        <div id="studentHeader">
                            <h2 class="text-2xl font-bold text-gray-800">Select a Student</h2>
                            <p class="text-gray-500">No student selected</p>
                        </div>
                        <img id="studentPhoto" src="" alt="Student Photo" class="w-24 h-24 rounded-lg object-cover border border-gray-300 hidden">
                    </div>
                </div>

                <!-- Subject Scores Table -->
                <div id="resultTableContainer" class="overflow-x-auto hidden">
                    <table class="w-full text-sm">
                        <thead class="bg-blue-100">
                            <tr>
                                <th class="px-4 py-2 text-left font-semibold text-gray-800">Subject</th>
                                <th class="px-4 py-2 text-center font-semibold text-gray-800">CA1</th>
                                <th class="px-4 py-2 text-center font-semibold text-gray-800">CA2</th>
                                <th class="px-4 py-2 text-center font-semibold text-gray-800">Exam</th>
                                <th class="px-4 py-2 text-center font-semibold text-gray-800">Total</th>
                                <th class="px-4 py-2 text-center font-semibold text-gray-800">Score</th>
                            </tr>
                        </thead>
                        <tbody id="resultTableBody">
                        </tbody>
                    </table>
                </div>

                <div id="noResultMessage" class="text-center text-gray-500 py-8">
                    Select a student to view results
                </div>

                <!-- Action Buttons -->
                <div class="mt-6 border-t border-gray-200 pt-4">
                    <div class="flex items-center justify-between mb-4">
                        <label class="flex items-center gap-2 text-sm text-gray-700">
                            <input id="selectAll" type="checkbox" class="rounded">
                            <span>Select All Students</span>
                        </label>
                    </div>
                    <div id="actionButtons" class="flex gap-4 justify-end hidden">
                        <!-- To Mail button removed per request -->
                        @if(auth()->check() && (auth()->user()->role === 'admin' || auth()->user()->isTeacher()))
                        <button id="calculateBtn" onclick="calculateResults()" class="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" /></svg>
                            Calculate Results
                        </button>
                        @endif
                        <button onclick="printResults()" class="flex items-center gap-2 px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 9V3h12v6M6 21h12v-6H6v6zM6 14h12"/></svg>
                            Print
                        </button>

                        <button onclick="printSelected()" class="flex items-center gap-2 px-4 py-2 bg-purple-500 text-white rounded-lg hover:bg-purple-600">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 9V3h12v6M6 21h12v-6H6v6zM6 14h12"/></svg>
                            Print Selected
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Print Modal -->
<div id="printModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-lg p-6 max-w-2xl w-full mx-4">
        <h3 class="text-xl font-bold mb-4">Print Result</h3>
        <div id="printContent" class="mb-4"></div>
        <div class="flex justify-end gap-4">
            <button onclick="closePrintModal()" class="px-4 py-2 bg-gray-300 text-gray-800 rounded-lg hover:bg-gray-400">Cancel</button>
            <button onclick="executePrint()" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">Print</button>
        </div>
    </div>
</div>

<script>
    const classSelect = document.getElementById('classSelect');
    const termSelect = document.getElementById('termSelect');
    const studentsList = document.getElementById('studentsList');
    const resultTableContainer = document.getElementById('resultTableContainer');
    const resultTableBody = document.getElementById('resultTableBody');
    const studentHeader = document.getElementById('studentHeader');
    const studentPhoto = document.getElementById('studentPhoto');
    const noResultMessage = document.getElementById('noResultMessage');
    const actionButtons = document.getElementById('actionButtons');
    const studentCount = document.getElementById('studentCount');

    let currentStudent = null;
    let allStudents = [];
    let selectedIds = new Set();

    // Toast notification system
    function showToast(message, type = 'info', duration = 4000) {
        const toast = document.createElement('div');
        toast.className = `toast ${type}`;
        toast.textContent = message;

        // Force inline green background to avoid external CSS overrides
        toast.style.backgroundColor = '#10b981';
        toast.style.color = '#ffffff';

        document.body.appendChild(toast);
        
        if (duration > 0) {
            setTimeout(() => {
                toast.classList.add('removing');
                setTimeout(() => toast.remove(), 300);
            }, duration);
        }
        return toast;
    }

    // Load students when class and term are selected
    async function loadStudents() {
        if (!classSelect.value || !termSelect.value) {
            studentsList.innerHTML = '<div class="bg-gray-100 px-3 py-2 text-sm text-gray-600">Select class and term</div>';
            allStudents = [];
            studentCount.textContent = '0';
            return;
        }

        try {
            const response = await fetch(`{{ route('results.data') }}?class_id=${classSelect.value}&term_id=${termSelect.value}`);
            const data = await response.json();
            allStudents = data.students;
            studentCount.textContent = allStudents.length;

            // Render student list
            renderStudentsList();
        } catch (error) {
            console.error('Error loading students:', error);
            studentsList.innerHTML = '<div class="bg-red-100 px-3 py-2 text-sm text-red-600">Error loading students</div>';
        }
    }

    function renderStudentsList() {
        if (allStudents.length === 0) {
            studentsList.innerHTML = '<div class="bg-gray-100 px-3 py-2 text-sm text-gray-600">No students found</div>';
            return;
        }

        let html = '';
        allStudents.forEach((student, idx) => {
            html += `
                <div class="flex items-center border-b border-gray-200 last:border-b-0 hover:bg-blue-50 px-3 py-2 text-sm ${currentStudent?.id === student.id ? 'bg-blue-100 font-semibold' : ''}">
                    <input type="checkbox" class="student-checkbox mr-3" data-index="${idx}" value="${student.id}" ${selectedIds.has(String(student.id)) ? 'checked' : ''}>
                    <div class="flex-1 cursor-pointer" onclick="selectStudent(${idx})">
                        <div class="font-semibold text-gray-800">${student.adm_no}</div>
                        <div class="text-gray-600">${student.full_name}</div>
                    </div>
                </div>
            `;
        });
        studentsList.innerHTML = html;
    }

    function selectStudent(index) {
        currentStudent = allStudents[index];
        renderStudentsList();
        displayStudentResult();
    }

    function displayStudentResult() {
        if (!currentStudent) return;

        // Update header
        studentHeader.innerHTML = `
            <div>
                <h2 class="text-2xl font-bold text-gray-800">${currentStudent.full_name}</h2>
                <p class="text-gray-500">Adm No: ${currentStudent.adm_no}</p>
            </div>
        `;

        // Show photo if available
        if (currentStudent.photo) {
            studentPhoto.src = currentStudent.photo;
            studentPhoto.classList.remove('hidden');
        } else {
            studentPhoto.classList.add('hidden');
        }

        // Organize scores by subject group
        const scoresByGroup = {};
        currentStudent.scores.forEach(score => {
            const groupName = score.subject?.subject_group?.name || 'Other';
            if (!scoresByGroup[groupName]) {
                scoresByGroup[groupName] = [];
            }
            scoresByGroup[groupName].push(score);
        });

        // Render table
        if (currentStudent.scores.length === 0) {
            resultTableContainer.classList.add('hidden');
            noResultMessage.classList.remove('hidden');
            actionButtons.classList.add('hidden');
            return;
        }

        resultTableBody.innerHTML = '';
        let totalScore = 0;
        let subjectCount = 0;

        Object.entries(scoresByGroup).forEach(([groupName, scores]) => {
            // Add group header
            const groupRow = document.createElement('tr');
            groupRow.className = 'bg-blue-50 cursor-pointer hover:bg-blue-100';
            groupRow.innerHTML = `
                <td colspan="6" class="px-4 py-2 font-semibold text-blue-700 flex items-center gap-2">
                    <i class="fas fa-chevron-down text-sm"></i>
                    ${groupName}
                </td>
            `;
            resultTableBody.appendChild(groupRow);

            // Add subject rows
            scores.forEach(score => {
                const ca1 = score.ca1_score || 0;
                const ca2 = score.ca2_score || 0;
                const exam = score.exam_score || 0;
                const total = ca1 + ca2 + exam;
                totalScore += total;
                subjectCount++;

                const tr = document.createElement('tr');
                tr.className = 'border-b border-gray-200 hover:bg-gray-50';
                tr.innerHTML = `
                    <td class="px-4 py-2 text-gray-800">${score.subject?.name || 'N/A'}</td>
                    <td class="px-4 py-2 text-center text-gray-700">${ca1}</td>
                    <td class="px-4 py-2 text-center text-gray-700">${ca2}</td>
                    <td class="px-4 py-2 text-center text-gray-700">${exam}</td>
                    <td class="px-4 py-2 text-center font-semibold text-gray-800">${total}</td>
                    <td class="px-4 py-2 text-center text-gray-700">${total}</td>
                `;
                resultTableBody.appendChild(tr);
            });
        });

        resultTableContainer.classList.remove('hidden');
        noResultMessage.classList.add('hidden');
        actionButtons.classList.remove('hidden');
    }

    // Event listeners
    classSelect.addEventListener('change', loadStudents);
    termSelect.addEventListener('change', loadStudents);
    termSelect.addEventListener('change', refreshPublishButton);

    document.addEventListener('DOMContentLoaded', function() {
        refreshPublishButton();
    });

    async function refreshPublishButton() {
        const btn = document.getElementById('publishToggleBtn');
        const txt = document.getElementById('publishBtnText');
        if (!btn) return;
        const termId = termSelect.value;
        if (!termId) { btn.style.display = 'none'; return; }

        try {
            const res = await fetch(`/results/terms/${termId}/published-status`, { headers: { 'Accept': 'application/json' } });
            if (!res.ok) { btn.style.display = 'none'; return; }
            const body = await res.json();
            btn.style.display = 'inline-flex';
            txt.textContent = body.published ? 'Unpublish Results' : 'Publish Results';
        } catch (e) {
            console.warn('Failed to fetch publish status', e);
            btn.style.display = 'none';
        }
    }

    async function togglePublish() {
        const termId = termSelect.value;
        if (!termId) return showToast('Select a term first', 'warning');
        const tokenMeta = document.querySelector('meta[name="csrf-token"]');
        const token = tokenMeta ? tokenMeta.getAttribute('content') : '';
        try {
            const res = await fetch(`/results/terms/${termId}/publish`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({})
            });
            const body = await res.json();
            if (res.ok) {
                showToast(body.message || 'Publish toggled', 'success');
                const txt = document.getElementById('publishBtnText');
                txt.textContent = body.published ? 'Unpublish Results' : 'Publish Results';
            } else {
                showToast(body.message || 'Failed to toggle publish', 'error');
            }
        } catch (e) {
            console.error(e);
            showToast('Failed to toggle publish', 'error');
        }
    }

    // Select All checkbox handler and student-checkbox change handling
    const selectAllCheckbox = document.getElementById('selectAll');
    selectAllCheckbox.addEventListener('change', function(e) {
        const checked = e.target.checked;
        if (checked) {
            // select all ids
            allStudents.forEach(s => selectedIds.add(String(s.id)));
            // if no current student, show first student's result inline
            if (!currentStudent && allStudents.length > 0) {
                currentStudent = allStudents[0];
            }
        } else {
            // clear selections
            selectedIds.clear();
            // if selectAll was unchecked, clear current student display
            currentStudent = null;
        }
        renderStudentsList();
        if (currentStudent) {
            displayStudentResult();
        } else {
            resultTableContainer.classList.add('hidden');
            noResultMessage.classList.remove('hidden');
            actionButtons.classList.add('hidden');
        }
    });

    // Handle individual checkbox changes and keep selectAll state in sync
    studentsList.addEventListener('change', function(e) {
        const target = e.target;
        if (target && target.classList && target.classList.contains('student-checkbox')) {
            const id = String(target.value);
            if (target.checked) {
                selectedIds.add(id);
            } else {
                selectedIds.delete(id);
            }

            const total = document.querySelectorAll('.student-checkbox').length;
            const checkedCount = document.querySelectorAll('.student-checkbox:checked').length;
            selectAllCheckbox.checked = (total > 0 && checkedCount === total);
            // do not change currently displayed student when toggling checkboxes
        }
    });

    // Action functions
    async function calculateResults() {
        if (!termSelect.value) {
            showToast('Please select a term first', 'warning');
            return;
        }

        const btn = document.getElementById('calculateBtn');
        const originalText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Calculating...';

        try {
            const tokenMeta = document.querySelector('meta[name="csrf-token"]');
            const token = tokenMeta ? tokenMeta.getAttribute('content') : '';
            
            const response = await fetch('{{ route("results.calculate_summaries") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    term_id: termSelect.value
                })
            });

            const data = await response.json();

            if (response.ok) {
                showToast(data.message, 'success');
            } else {
                showToast(data.message || 'Calculation failed', 'error');
            }
        } catch (error) {
            console.error('Error calculating results:', error);
            showToast('An error occurred while calculating results', 'error');
        } finally {
            btn.disabled = false;
            btn.innerHTML = originalText;
        }
    }

    function sendToMail() {
        if (!currentStudent) return;
        showToast(`Sending to mail: ${currentStudent.full_name}`, 'info');
        // TODO: Implement email functionality
    }

    function printResults() {
        if (!currentStudent) return;
        if (!termSelect.value) {
            showToast('Please select a term', 'warning');
            return;
        }
        // Open preview page in a new window
        const urlBase = `{{ url('/reports/students') }}`;
        const previewUrl = `${urlBase}/${currentStudent.id}/preview?term_id=${termSelect.value}`;
        const printWindow = window.open(previewUrl, '_blank');
        
        if (printWindow) {
            showToast('Opening print preview...', 'info');
        } else {
            showToast('Popup blocked. Please allow popups for this site.', 'error');
        }
    }



    // Print selected students by fetching their preview HTML and combining into one print page
    async function printSelected() {
        const checkboxes = Array.from(document.querySelectorAll('.student-checkbox')).filter(cb => cb.checked);
        if (checkboxes.length === 0) {
            showToast('Please select at least one student to print', 'warning');
            return;
        }
        if (!termSelect.value) {
            showToast('Please select a term', 'warning');
            return;
        }

        const ids = checkboxes.map(cb => cb.value);
        const urlBase = `{{ url('/reports/students') }}`;
        const previews = [];
        const failed = [];

        showToast(`Loading ${ids.length} student(s)...`, 'info');

        try {
            // Fetch all previews first
            for (const id of ids) {
                try {
                    const res = await fetch(`${urlBase}/${id}/preview?term_id=${termSelect.value}`);
                    if (!res.ok) {
                        failed.push(id);
                        console.warn('Failed to fetch preview for ' + id + ': ' + res.status);
                        continue;
                    }
                    let text = await res.text();
                    
                    // Strip out all script tags to prevent auto-print
                    text = text.replace(/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/gi, '');
                    
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(text, 'text/html');
                    let headHtml = doc.head ? doc.head.innerHTML : '';
                    let bodyHtml = doc.body ? doc.body.innerHTML : text;
                    
                    // Remove any inline event handlers that might trigger print
                    bodyHtml = bodyHtml.replace(/\s*on\w+\s*=\s*"[^"]*"/gi, '');
                    bodyHtml = bodyHtml.replace(/\s*on\w+\s*=\s*'[^']*'/gi, '');
                    
                    previews.push({ head: headHtml, body: bodyHtml, id: id });
                } catch (fetchErr) {
                    console.warn('Failed to fetch student ' + id + ': ' + fetchErr.message);
                    failed.push(id);
                }
            }

            if (previews.length === 0) {
                showToast('Could not fetch any student previews. Check term selection and student data.', 'error');
                return;
            }

            // Build combined document and open in new window
            const combinedWin = window.open('', '_blank');
            if (!combinedWin) {
                alert('Popup blocked. Please allow popups for this site to print multiple results.');
                return;
            }

            // Use styles from the first preview head
            const headContent = previews[0].head || '';
            combinedWin.document.open();
            combinedWin.document.write(`<!doctype html>
<html>
<head>
${headContent}
<style>
    body { margin: 0; padding: 0; }
    .print-controls { 
        position: fixed; 
        top: 0; 
        left: 0; 
        right: 0; 
        background: #333; 
        color: white; 
        padding: 10px; 
        z-index: 9999;
        text-align: center;
    }
    .print-controls button {
        background: #007bff;
        color: white;
        border: none;
        padding: 8px 16px;
        margin: 0 5px;
        border-radius: 4px;
        cursor: pointer;
        font-size: 14px;
    }
    .print-controls button:hover {
        background: #0056b3;
    }
    body { padding-top: 50px; }
    @media print {
        .print-controls { display: none !important; }
        body { padding-top: 0; }
    }
</style>
</head>
<body>
<div class="print-controls">
    <button onclick="window.print(); return false;">🖨️ Print Now</button>
    <button onclick="window.close(); return false;">✕ Close</button>
</div>`);

            previews.forEach((p, i) => {
                combinedWin.document.write(`<div class="student-report" style="page-break-after: always;">${p.body}</div>`);
            });

            combinedWin.document.write('</body></html>');
            combinedWin.document.close();

            // Prevent automatic print dialogs - let user click button
            combinedWin.onbeforeprint = function(e) {
                // Allow print
            };

            let message = `Prepared ${previews.length} student(s) for printing`;
            if (failed.length > 0) {
                message += ` (${failed.length} could not be loaded)`;
            }
            showToast(message, failed.length > 0 ? 'warning' : 'success');

        } catch (e) {
            console.error(e);
            showToast(`Failed to prepare print preview: ${e.message}`, 'error');
        }
    }

    function closePrintModal() {
        document.getElementById('printModal').classList.add('hidden');
    }

    function executePrint() {
        window.print();
        closePrintModal();
    }
</script>
@endsection
