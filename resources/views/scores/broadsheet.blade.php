@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="bg-white rounded-lg shadow-md p-6">
        <h1 class="text-3xl font-bold text-center mb-8">Broadsheet (Class View)</h1>

        <!-- Filters Section -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6 items-end">
            <div>
                <label for="class_id" class="block text-sm font-medium text-gray-700 mb-1">Class</label>
                <select id="class_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    <option value="">-- Select Class --</option>
                    @foreach($classes as $class)
                        <option value="{{ $class->id }}">{{ $class->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="term_id" class="block text-sm font-medium text-gray-700 mb-1">Term</label>
                <select id="term_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    <option value="">-- Select Term --</option>
                    @foreach($terms as $term)
                        <option value="{{ $term->id }}">{{ $term->term_name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex flex-col space-y-2">
                <label class="flex items-center space-x-2">
                    <input type="checkbox" id="cumulative" class="rounded border-gray-300">
                    <span class="text-sm text-gray-700">Cumulative</span>
                </label>
                <button type="button" id="viewBtn" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 font-medium">
                    <i class="fas fa-eye mr-2"></i> View
                </button>
            </div>
        </div>

        <!-- Class and Term Header -->
        <div class="text-center mb-6 text-xl font-bold text-gray-800" id="headerInfo" style="display: none;">
            <span id="classTermInfo"></span>
        </div>

        <!-- Broadsheet Table -->
        <div class="overflow-x-auto mb-6 border rounded-lg" id="tableContainer" style="display: none;">
            <table class="w-full divide-y divide-gray-200" id="broadsheetTable">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider sticky left-0 bg-gray-50 z-10">Admission No.</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider sticky left-24 bg-gray-50 z-10">Name</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200" id="broadsheetBody"></tbody>
            </table>
        </div>

        <!-- Summary Stats -->
        <div class="flex justify-between mb-6" id="statsContainer" style="display: none;">
            <div class="text-left">
                <span class="font-medium text-gray-700">No. of Passes&nbsp;&nbsp;</span>
                <span class="ml-2 font-bold text-gray-900" id="totalPasses">0</span>
            </div>
            <div class="text-left">
                <span class="font-medium text-gray-700">No. of Fails:&nbsp;&nbsp;</span>
                <span class="ml-2 font-bold text-gray-900" id="totalFails">0</span>
            </div>
            <div class="text-left">
                <span class="font-medium text-gray-700">Total:&nbsp;&nbsp;</span>
                <span class="ml-2 font-bold text-gray-900" id="totalStudents">0</span>
            </div>
        </div>

        <!-- Export Buttons -->
        <div class="flex justify-end space-x-2" id="exportContainer" style="display: none;">
            <button type="button" id="exportBtn" class="bg-orange-500 text-white px-4 py-2 rounded-md hover:bg-orange-600 font-medium flex items-center space-x-2">
                <i class="fas fa-download"></i>
                <span>Export</span>
            </button>
            <button type="button" id="exportXlsBtn" class="bg-red-500 text-white px-4 py-2 rounded-md hover:bg-red-600 font-medium flex items-center space-x-2">
                <i class="fas fa-file-excel"></i>
                <span>XLS Export</span>
            </button>
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
    const cumulativeCheckbox = document.getElementById('cumulative');
    const viewBtn = document.getElementById('viewBtn');
    const headerInfo = document.getElementById('headerInfo');
    const classTermInfo = document.getElementById('classTermInfo');
    const tableContainer = document.getElementById('tableContainer');
    const broadsheetTable = document.getElementById('broadsheetTable');
    const broadsheetBody = document.getElementById('broadsheetBody');
    const statsContainer = document.getElementById('statsContainer');
    const exportContainer = document.getElementById('exportContainer');
    const exportBtn = document.getElementById('exportBtn');
    const exportXlsBtn = document.getElementById('exportXlsBtn');

    function getClassLabel() {
        const opt = classSelect.selectedOptions[0];
        return opt ? opt.text : '';
    }

    function getTermLabel() {
        const opt = termSelect.selectedOptions[0];
        return opt ? opt.text : '';
    }

    function showToast(message, type = 'success', duration = 4000) {
        const container = document.getElementById('toastContainer');
        if (!container) return;

        const bgClass = type === 'error' ? 'bg-red-50' : 'bg-green-50';
        const borderClass = type === 'error' ? 'border-red-500' : 'border-green-500';
        const toast = document.createElement('div');
        toast.className = `max-w-sm w-full ${bgClass} border-l-4 ${borderClass} rounded-md shadow-md p-3 flex items-start space-x-2`;
        toast.innerHTML = `
            <div class="flex-1 text-sm text-gray-800">${message}</div>
            <button type="button" class="text-gray-500 hover:text-gray-700 ml-2">&times;</button>
        `;
        toast.querySelector('button').addEventListener('click', () => toast.remove());
        container.appendChild(toast);
        setTimeout(() => toast.remove(), duration);
    }

    async function loadBroadsheet() {
        if (!classSelect.value) {
            showToast('Please select a class', 'error');
            return;
        }

        const params = new URLSearchParams({
            class_id: classSelect.value,
            term_id: termSelect.value || '',
            session_id: '',
            cumulative: cumulativeCheckbox.checked ? 1 : 0
        });

        try {
            const response = await fetch(`{{ url('/broadsheet/data') }}?${params}`, {
                credentials: 'same-origin',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (!response.ok) throw new Error('Failed to fetch broadsheet data');

            const data = await response.json();
            renderBroadsheet(data);
            showToast('Broadsheet loaded successfully', 'success');
        } catch (error) {
            console.error('Error:', error);
            showToast('Failed to load broadsheet. Please try again.', 'error');
        }
    }

    function renderBroadsheet(data) {
        const { broadsheet, subjects, summary } = data;

        const classLabel = getClassLabel();
        const termLabel = getTermLabel();
        const headerText = termLabel ? `${classLabel} (${termLabel})` : classLabel;
        classTermInfo.textContent = headerText;
        headerInfo.style.display = 'block';

        const thead = broadsheetTable.querySelector('thead tr');
        thead.querySelectorAll('th[data-subject], th[data-summary]').forEach(h => h.remove());

        const summaryHeaders = [
            { text: 'Average %', tooltip: 'Average Score' },
            { text: 'Position', tooltip: 'Class Position' },
            { text: 'Remarks', tooltip: 'Remarks' }
        ];

        if (!subjects || subjects.length === 0) {
            const colspan = 2 + summaryHeaders.length; // Admission + Name + summaries
            broadsheetBody.innerHTML = `<tr><td class="px-4 py-6 text-center text-sm text-gray-700" colspan="${colspan}">No subjects assigned to this class. Please assign subjects to the class and try again.</td></tr>`;
            tableContainer.style.display = 'block';
            statsContainer.style.display = 'none';
            exportContainer.style.display = 'none';
            showToast('No subjects found for selected class', 'error');
            return;
        }
        subjects.forEach(subject => {
            const th = document.createElement('th');
            th.className = 'px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider';
            th.dataset.subject = subject.id;
            th.textContent = subject.name;
            thead.appendChild(th);
        });

        summaryHeaders.forEach(header => {
            const th = document.createElement('th');
            th.className = 'px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider bg-gray-100';
            th.dataset.summary = '1';
            th.title = header.tooltip;
            th.textContent = header.text;
            thead.appendChild(th);
        });

        broadsheetBody.innerHTML = '';
        broadsheet.forEach((row, index) => {
            const tr = document.createElement('tr');
            const position = row.position ?? (index + 1);
            const remarks = row.average > 0 ? 'PASSED' : 'FAILED';

            tr.innerHTML = `
                <td class="px-4 py-3 text-sm text-gray-900 sticky left-0 bg-white z-10">${row.adm_no}</td>
                <td class="px-4 py-3 text-sm text-gray-900 sticky left-24 bg-white z-10">${row.full_name}</td>
                ${row.scores.map(score => `<td class="px-4 py-3 text-sm text-gray-900 text-center">${score}</td>`).join('')}
                <td class="px-4 py-3 text-sm font-medium text-gray-900 bg-gray-50 text-center">${row.average}</td>
                <td class="px-4 py-3 text-sm font-medium text-gray-900 bg-gray-50 text-center">${position}</td>
                <td class="px-4 py-3 text-sm font-medium text-gray-900 bg-gray-50 text-center">${remarks}</td>
            `;
            broadsheetBody.appendChild(tr);
        });

        document.getElementById('totalPasses').textContent = summary.total_passes;
        document.getElementById('totalFails').textContent = summary.total_fails;
        document.getElementById('totalStudents').textContent = summary.total_students;

        tableContainer.style.display = 'block';
        statsContainer.style.display = 'flex';
        exportContainer.style.display = 'flex';
    }

    exportBtn.addEventListener('click', function() {
        const params = new URLSearchParams({
            class_id: classSelect.value,
            term_id: termSelect.value || '',
            session_id: '',
            cumulative: cumulativeCheckbox.checked ? 1 : 0
        });
        window.location.href = `{{ url('/broadsheet/export') }}?${params}&format=pdf`;
    });

    exportXlsBtn.addEventListener('click', function() {
        const params = new URLSearchParams({
            class_id: classSelect.value,
            term_id: termSelect.value || '',
            session_id: '',
            cumulative: cumulativeCheckbox.checked ? 1 : 0
        });
        window.location.href = `{{ url('/broadsheet/export') }}?${params}&format=xlsx`;
    });

    viewBtn.addEventListener('click', loadBroadsheet);

    [classSelect, termSelect, cumulativeCheckbox].forEach(el => {
        el.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') loadBroadsheet();
        });
    });
});
</script>
@endpush
