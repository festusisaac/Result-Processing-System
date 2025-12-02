@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-3xl font-bold mb-6">Report Settings</h2>

        {{-- Tab Navigation --}}
        <div class="border-b border-gray-200 mb-6">
            <nav class="-mb-px flex space-x-8">
                <button type="button" class="tab-btn border-b-2 border-blue-600 py-4 px-1 text-base font-medium text-blue-600" data-tab="basic">
                    Basic Information
                </button>
                <button type="button" class="tab-btn border-b-2 border-transparent py-4 px-1 text-base font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300" data-tab="grading">
                    Grading & Remarks
                </button>
                <button type="button" class="tab-btn border-b-2 border-transparent py-4 px-1 text-base font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300" data-tab="comments">
                    Comments
                </button>
                <button type="button" class="tab-btn border-b-2 border-transparent py-4 px-1 text-base font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300" data-tab="promotion">
                    Promotion Status
                </button>
            </nav>
        </div>

        <form method="POST" action="{{ route('report-settings.store') }}" enctype="multipart/form-data">
            @csrf

            {{-- Basic Information Tab --}}
            <div class="tab-content" id="basic-tab">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-lg font-medium text-gray-700">Result Title</label>
                        <input type="text" name="result_title" value="{{ $settings['result_title'] ?? '' }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm p-2">
                    </div>

                    <div>
                        <label class="block text-lg font-medium text-gray-700">Promotion Status (Default Fallback)</label>
                        <input type="text" name="promotion_status" value="{{ $settings['promotion_status'] ?? '' }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm p-2" placeholder="e.g. PROMOTED TO NEXT CLASS" />
                        <small class="text-gray-600">Used as fallback if no score-based rules match.</small>
                    </div>

                    <div class="lg:col-span-2">
                        <label class="block text-lg font-medium text-gray-700">School Logo</label>
                        <div class="mt-1 flex items-center gap-4">
                            <input type="file" name="school_logo" accept="image/*" class="block rounded-md" />
                            <div class="flex items-center gap-4">
                                @if(!empty($settings['school_logo']))
                                    <img src="{{ asset('storage/' . $settings['school_logo']) }}" alt="School Logo" class="h-24 w-24 object-contain border rounded" />
                                @else
                                    <div class="h-24 w-24 bg-gray-100 border rounded flex items-center justify-center text-gray-500 text-sm">No logo</div>
                                @endif

                                <div class="flex flex-col">
                                    @if(!empty($settings['school_logo']))
                                        <label class="inline-flex items-center text-sm text-gray-700">
                                            <input type="checkbox" name="remove_school_logo" value="1" class="mr-2"> Remove logo
                                        </label>
                                    @endif
                                    <small class="text-gray-600">Max 2MB. Recommended square image (PNG/JPG).</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="lg:col-span-2">
                        <label class="block text-lg font-medium text-gray-700">School Address</label>
                        <textarea name="school_address" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">{{ $settings['school_address'] ?? '' }}</textarea>
                    </div>

                    <div class="lg:col-span-2">
                        <label class="block text-lg font-medium text-gray-700">School Motto</label>
                        <input type="text" name="school_motto" value="{{ $settings['school_motto'] ?? '' }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm p-2" />
                    </div>



                    <div class="lg:col-span-2">
                        <label class="block text-lg font-medium text-gray-700">Principal's Signature</label>
                        <div class="mt-1 flex items-center gap-4">
                            <input type="file" name="principal_signature" accept="image/*" class="block rounded-md" />
                            <div class="flex items-center gap-4">
                                @if(!empty($settings['principal_signature']))
                                    <img src="{{ asset('storage/' . $settings['principal_signature']) }}" alt="Principal Signature" class="h-16 w-32 object-contain border rounded" />
                                @else
                                    <div class="h-16 w-32 bg-gray-100 border rounded flex items-center justify-center text-gray-500 text-sm">No signature</div>
                                @endif

                                <div class="flex flex-col">
                                    @if(!empty($settings['principal_signature']))
                                        <label class="inline-flex items-center text-sm text-gray-700">
                                            <input type="checkbox" name="remove_principal_signature" value="1" class="mr-2"> Remove signature
                                        </label>
                                    @endif
                                    <small class="text-gray-600">Max 2MB. Recommended transparent PNG.</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="lg:col-span-2">
                        <label class="block text-lg font-medium text-gray-700">School Stamp</label>
                        <div class="mt-1 flex items-center gap-4">
                            <input type="file" name="school_stamp" accept="image/*" class="block rounded-md" />
                            <div class="flex items-center gap-4">
                                @if(!empty($settings['school_stamp']))
                                    <img src="{{ asset('storage/' . $settings['school_stamp']) }}" alt="School Stamp" class="h-24 w-24 object-contain border rounded" />
                                @else
                                    <div class="h-24 w-24 bg-gray-100 border rounded flex items-center justify-center text-gray-500 text-sm">No stamp</div>
                                @endif

                                <div class="flex flex-col">
                                    @if(!empty($settings['school_stamp']))
                                        <label class="inline-flex items-center text-sm text-gray-700">
                                            <input type="checkbox" name="remove_school_stamp" value="1" class="mr-2"> Remove stamp
                                        </label>
                                    @endif
                                    <small class="text-gray-600">Max 2MB. Recommended transparent PNG.</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Grading & Remarks Tab --}}
            <div class="tab-content hidden" id="grading-tab">
                <div class="space-y-6">
                    {{-- Grading Table --}}
                    <div>
                        <label class="block text-lg font-medium text-gray-700 mb-2">Grading Rules</label>
                        <div class="overflow-auto border rounded-md">
                            <table id="grading-table" class="min-w-full bg-white">
                                <thead>
                                    <tr class="text-left text-base">
                                        <th class="px-2 py-2 border">Score Range (min-max)</th>
                                        <th class="px-2 py-2 border">Grade</th>
                                        <th class="px-2 py-2 border w-24">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($gradingRules as $r)
                                        <tr>
                                            <td class="px-2 py-1 border">
                                                <div class="flex gap-2">
                                                    <input type="number" min="0" max="100" class="min-input mt-1 block w-1/2 rounded-md border-gray-300 p-1" value="{{ $r['min'] }}">
                                                    <input type="number" min="0" max="100" class="max-input mt-1 block w-1/2 rounded-md border-gray-300 p-1" value="{{ $r['max'] }}">
                                                </div>
                                            </td>
                                            <td class="px-2 py-1 border">
                                                <input type="text" class="grade-input mt-1 block w-full rounded-md border-gray-300 p-1" value="{{ $r['grade'] }}">
                                            </td>
                                            <td class="px-2 py-1 border text-center">
                                                <button type="button" class="remove-row px-2 py-1 text-sm text-red-600">Remove</button>
                                            </td>
                                        </tr>
                                    @endforeach
                                    <tr>
                                        <td class="px-2 py-1 border">
                                            <div class="flex gap-2">
                                                <input type="number" min="0" max="100" class="min-input mt-1 block w-1/2 rounded-md border-gray-300 p-1" placeholder="min">
                                                <input type="number" min="0" max="100" class="max-input mt-1 block w-1/2 rounded-md border-gray-300 p-1" placeholder="max">
                                            </div>
                                        </td>
                                        <td class="px-2 py-1 border">
                                            <input type="text" class="grade-input mt-1 block w-full rounded-md border-gray-300 p-1" placeholder="A">
                                        </td>
                                        <td class="px-2 py-1 border text-center">
                                            <button type="button" class="remove-row px-2 py-1 text-sm text-red-600">Remove</button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-2 flex items-center gap-2">
                            <button type="button" id="add-row" class="px-3 py-1 bg-green-600 text-white rounded-md">Add row</button>
                            <small class="text-gray-600">Enter ranges as min-max (e.g. 70-100).</small>
                        </div>
                        <textarea name="grading" id="grading-hidden" class="hidden">{{ $settings['grading'] ?? '' }}</textarea>
                    </div>

                    {{-- Remarks Table --}}
                    <div>
                        <label class="block text-lg font-medium text-gray-700 mb-2">Remarks</label>
                        <div class="overflow-auto border rounded-md">
                            <table id="remarks-table" class="min-w-full bg-white">
                                <thead>
                                    <tr class="text-left text-base">
                                        <th class="px-2 py-2 border">Score Range (min-max)</th>
                                        <th class="px-2 py-2 border">Remark</th>
                                        <th class="px-2 py-2 border w-24">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($remarksRules as $r)
                                        <tr>
                                            <td class="px-2 py-1 border">
                                                <div class="flex gap-2">
                                                    <input type="number" min="0" max="100" class="min-input mt-1 block w-1/2 rounded-md border-gray-300 p-1" value="{{ $r['min'] }}">
                                                    <input type="number" min="0" max="100" class="max-input mt-1 block w-1/2 rounded-md border-gray-300 p-1" value="{{ $r['max'] }}">
                                                </div>
                                            </td>
                                            <td class="px-2 py-1 border">
                                                <input type="text" class="remark-input mt-1 block w-full rounded-md border-gray-300 p-1" value="{{ $r['remark'] }}">
                                            </td>
                                            <td class="px-2 py-1 border text-center">
                                                <button type="button" class="remove-row px-2 py-1 text-sm text-red-600">Remove</button>
                                            </td>
                                        </tr>
                                    @endforeach
                                    <tr>
                                        <td class="px-2 py-1 border">
                                            <div class="flex gap-2">
                                                <input type="number" min="0" max="100" class="min-input mt-1 block w-1/2 rounded-md border-gray-300 p-1" placeholder="min">
                                                <input type="number" min="0" max="100" class="max-input mt-1 block w-1/2 rounded-md border-gray-300 p-1" placeholder="max">
                                            </div>
                                        </td>
                                        <td class="px-2 py-1 border">
                                            <input type="text" class="remark-input mt-1 block w-full rounded-md border-gray-300 p-1" placeholder="Excellent">
                                        </td>
                                        <td class="px-2 py-1 border text-center">
                                            <button type="button" class="remove-row px-2 py-1 text-sm text-red-600">Remove</button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-2 flex items-center gap-2">
                            <button type="button" id="add-remarks-row" class="px-3 py-1 bg-green-600 text-white rounded-md">Add row</button>
                            <small class="text-gray-600">Enter ranges as min-max (e.g. 70-100).</small>
                        </div>
                        <textarea name="remarks" id="remarks-hidden" class="hidden">{{ $settings['remarks'] ?? '' }}</textarea>
                    </div>
                </div>
            </div>

            {{-- Comments Tab --}}
            <div class="tab-content hidden" id="comments-tab">
                <div class="space-y-6">
                    {{-- Principal Comments Table --}}
                    <div>
                        <label class="block text-lg font-medium text-gray-700 mb-2">Principal's Comments</label>
                        <div class="overflow-auto border rounded-md">
                            <table id="principal-comments-table" class="min-w-full bg-white">
                                <thead>
                                    <tr class="text-left text-base">
                                        <th class="px-2 py-2 border">Score Range (min-max)</th>
                                        <th class="px-2 py-2 border">Comment</th>
                                        <th class="px-2 py-2 border w-24">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($principalRules as $r)
                                        <tr>
                                            <td class="px-2 py-1 border">
                                                <div class="flex gap-2">
                                                    <input type="number" min="0" max="100" class="min-input mt-1 block w-1/2 rounded-md border-gray-300 p-1" value="{{ $r['min'] }}">
                                                    <input type="number" min="0" max="100" class="max-input mt-1 block w-1/2 rounded-md border-gray-300 p-1" value="{{ $r['max'] }}">
                                                </div>
                                            </td>
                                            <td class="px-2 py-1 border">
                                                <input type="text" class="principal-comment-input mt-1 block w-full rounded-md border-gray-300 p-1" value="{{ $r['comment'] }}">
                                            </td>
                                            <td class="px-2 py-1 border text-center">
                                                <button type="button" class="remove-row px-2 py-1 text-sm text-red-600">Remove</button>
                                            </td>
                                        </tr>
                                    @endforeach
                                    <tr>
                                        <td class="px-2 py-1 border">
                                            <div class="flex gap-2">
                                                <input type="number" min="0" max="100" class="min-input mt-1 block w-1/2 rounded-md border-gray-300 p-1" placeholder="min">
                                                <input type="number" min="0" max="100" class="max-input mt-1 block w-1/2 rounded-md border-gray-300 p-1" placeholder="max">
                                            </div>
                                        </td>
                                        <td class="px-2 py-1 border">
                                            <input type="text" class="principal-comment-input mt-1 block w-full rounded-md border-gray-300 p-1" placeholder="Principal's comment">
                                        </td>
                                        <td class="px-2 py-1 border text-center">
                                            <button type="button" class="remove-row px-2 py-1 text-sm text-red-600">Remove</button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-2 flex items-center gap-2">
                            <button type="button" id="add-principal-row" class="px-3 py-1 bg-green-600 text-white rounded-md">Add row</button>
                            <small class="text-gray-600">Enter ranges as min-max (e.g. 70-100).</small>
                        </div>
                        <textarea name="principal_comment" id="principal-comments-hidden" class="hidden">{{ $settings['principal_comment'] ?? '' }}</textarea>
                    </div>

                    {{-- Class Teacher Comments Table --}}
                    <div>
                        <label class="block text-lg font-medium text-gray-700 mb-2">Class Teacher's Comments</label>
                        <div class="overflow-auto border rounded-md">
                            <table id="class-comments-table" class="min-w-full bg-white">
                                <thead>
                                    <tr class="text-left text-base">
                                        <th class="px-2 py-2 border">Score Range (min-max)</th>
                                        <th class="px-2 py-2 border">Comment</th>
                                        <th class="px-2 py-2 border w-24">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($classCommentsRules as $r)
                                        <tr>
                                            <td class="px-2 py-1 border">
                                                <div class="flex gap-2">
                                                    <input type="number" min="0" max="100" class="min-input mt-1 block w-1/2 rounded-md border-gray-300 p-1" value="{{ $r['min'] }}">
                                                    <input type="number" min="0" max="100" class="max-input mt-1 block w-1/2 rounded-md border-gray-300 p-1" value="{{ $r['max'] }}">
                                                </div>
                                            </td>
                                            <td class="px-2 py-1 border">
                                                <input type="text" class="class-comment-input mt-1 block w-full rounded-md border-gray-300 p-1" value="{{ $r['comment'] }}">
                                            </td>
                                            <td class="px-2 py-1 border text-center">
                                                <button type="button" class="remove-row px-2 py-1 text-sm text-red-600">Remove</button>
                                            </td>
                                        </tr>
                                    @endforeach
                                    <tr>
                                        <td class="px-2 py-1 border">
                                            <div class="flex gap-2">
                                                <input type="number" min="0" max="100" class="min-input mt-1 block w-1/2 rounded-md border-gray-300 p-1" placeholder="min">
                                                <input type="number" min="0" max="100" class="max-input mt-1 block w-1/2 rounded-md border-gray-300 p-1" placeholder="max">
                                            </div>
                                        </td>
                                        <td class="px-2 py-1 border">
                                            <input type="text" class="class-comment-input mt-1 block w-full rounded-md border-gray-300 p-1" placeholder="Excellent Result">
                                        </td>
                                        <td class="px-2 py-1 border text-center">
                                            <button type="button" class="remove-row px-2 py-1 text-sm text-red-600">Remove</button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-2 flex items-center gap-2">
                            <button type="button" id="add-class-row" class="px-3 py-1 bg-green-600 text-white rounded-md">Add row</button>
                            <small class="text-gray-600">Enter ranges as min-max (e.g. 70-100).</small>
                        </div>
                        <textarea name="class_teacher_comments" id="class-comments-hidden" class="hidden">{{ $settings['class_teacher_comments'] ?? '' }}</textarea>
                    </div>
                </div>
            </div>

            {{-- Promotion Status Tab --}}
            <div class="tab-content hidden" id="promotion-tab">
                <div>
                    <label class="block text-lg font-medium text-gray-700 mb-2">Promotion Status Rules</label>
                    <div class="overflow-auto border rounded-md">
                        <table id="promotion-status-table" class="min-w-full bg-white">
                            <thead>
                                <tr class="text-left text-base">
                                    <th class="px-2 py-2 border">Score Range (min-max)</th>
                                    <th class="px-2 py-2 border">Promotion Status</th>
                                    <th class="px-2 py-2 border w-24">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($promotionRules as $r)
                                    <tr>
                                        <td class="px-2 py-1 border">
                                            <div class="flex gap-2">
                                                <input type="number" min="0" max="100" class="min-input mt-1 block w-1/2 rounded-md border-gray-300 p-1" value="{{ $r['min'] }}">
                                                <input type="number" min="0" max="100" class="max-input mt-1 block w-1/2 rounded-md border-gray-300 p-1" value="{{ $r['max'] }}">
                                            </div>
                                        </td>
                                        <td class="px-2 py-1 border">
                                            <input type="text" class="promotion-status-input mt-1 block w-full rounded-md border-gray-300 p-1" value="{{ $r['status'] }}">
                                        </td>
                                        <td class="px-2 py-1 border text-center">
                                            <button type="button" class="remove-row px-2 py-1 text-sm text-red-600">Remove</button>
                                        </td>
                                    </tr>
                                @endforeach
                                <tr>
                                    <td class="px-2 py-1 border">
                                        <div class="flex gap-2">
                                            <input type="number" min="0" max="100" class="min-input mt-1 block w-1/2 rounded-md border-gray-300 p-1" placeholder="min">
                                            <input type="number" min="0" max="100" class="max-input mt-1 block w-1/2 rounded-md border-gray-300 p-1" placeholder="max">
                                        </div>
                                    </td>
                                    <td class="px-2 py-1 border">
                                        <input type="text" class="promotion-status-input mt-1 block w-full rounded-md border-gray-300 p-1" placeholder="PROMOTED TO NEXT CLASS">
                                    </td>
                                    <td class="px-2 py-1 border text-center">
                                        <button type="button" class="remove-row px-2 py-1 text-sm text-red-600">Remove</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-2 flex items-center gap-2">
                        <button type="button" id="add-promotion-row" class="px-3 py-1 bg-green-600 text-white rounded-md">Add row</button>
                        <small class="text-gray-600">Enter ranges as min-max (e.g. 70-100: PROMOTED TO NEXT CLASS, 40-69: PROMOTED ON TRIAL).</small>
                    </div>
                    <textarea name="promotion_status_rules" id="promotion-status-hidden" class="hidden">{{ $settings['promotion_status_rules'] ?? '' }}</textarea>
                </div>
            </div>

            <div class="mt-6 flex justify-end">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Save Settings</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Tab switching
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const tabName = this.dataset.tab;
            
            // Update button styles
            document.querySelectorAll('.tab-btn').forEach(b => {
                b.classList.remove('border-blue-600', 'text-blue-600');
                b.classList.add('border-transparent', 'text-gray-500');
            });
            this.classList.remove('border-transparent', 'text-gray-500');
            this.classList.add('border-blue-600', 'text-blue-600');
            
            // Show/hide content
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.add('hidden');
            });
            document.getElementById(tabName + '-tab').classList.remove('hidden');
        });
    });

    // Helper: ensure each data row has an error-row following it
    function ensureErrorRows(table) {
        const tbody = table.querySelector('tbody');
        const rows = Array.from(tbody.children);
        for (let i = 0; i < rows.length; i += 2) {
            const dataRow = rows[i];
            const next = rows[i+1];
            if (!next || !next.classList.contains('error-row')) {
                const errTr = document.createElement('tr');
                errTr.className = 'error-row hidden';
                errTr.innerHTML = `<td class="px-2 py-1 border text-red-600" colspan="3"><div class="row-error text-red-600 text-sm"></div></td>`;
                if (next) tbody.insertBefore(errTr, next);
                else tbody.appendChild(errTr);
            }
        }
    }

    const gradingTable = document.getElementById('grading-table');
    const addBtn = document.getElementById('add-row');
    const hidden = document.getElementById('grading-hidden');

    const remarksTable = document.getElementById('remarks-table');
    const addRemarksBtn = document.getElementById('add-remarks-row');
    const remarksHidden = document.getElementById('remarks-hidden');
    
    const classTable = document.getElementById('class-comments-table');
    const addClassBtn = document.getElementById('add-class-row');
    const classHidden = document.getElementById('class-comments-hidden');
    
    const principalTable = document.getElementById('principal-comments-table');
    const addPrincipalBtn = document.getElementById('add-principal-row');
    const principalHidden = document.getElementById('principal-comments-hidden');
    
    const promotionTable = document.getElementById('promotion-status-table');
    const addPromotionBtn = document.getElementById('add-promotion-row');
    const promotionHidden = document.getElementById('promotion-status-hidden');

    // Initialize error rows for all tables
    [gradingTable, remarksTable, classTable, principalTable, promotionTable].forEach(t => ensureErrorRows(t));

    function addRow(min = '', max = '', grade = '') {
        const tbody = gradingTable.querySelector('tbody');
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td class="px-2 py-1 border">
                <div class="flex gap-2">
                    <input type="number" min="0" max="100" class="min-input mt-1 block w-1/2 rounded-md border-gray-300 p-1" value="${min}" placeholder="min">
                    <input type="number" min="0" max="100" class="max-input mt-1 block w-1/2 rounded-md border-gray-300 p-1" value="${max}" placeholder="max">
                </div>
            </td>
            <td class="px-2 py-1 border">
                <input type="text" class="grade-input mt-1 block w-full rounded-md border-gray-300 p-1" value="${grade}" placeholder="A">
            </td>
            <td class="px-2 py-1 border text-center">
                <button type="button" class="remove-row px-2 py-1 text-sm text-red-600">Remove</button>
            </td>
        `;
        tbody.appendChild(tr);
        const errTr = document.createElement('tr');
        errTr.className = 'error-row hidden';
        errTr.innerHTML = `<td class="px-2 py-1 border text-red-600" colspan="3"><div class="row-error text-red-600 text-sm"></div></td>`;
        tbody.appendChild(errTr);
    }

    addBtn.addEventListener('click', function () {
        addRow('', '');
    });

    function addRemarksRow(min = '', max = '', remark = '') {
        const tbody = remarksTable.querySelector('tbody');
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td class="px-2 py-1 border">
                <div class="flex gap-2">
                    <input type="number" min="0" max="100" class="min-input mt-1 block w-1/2 rounded-md border-gray-300 p-1" value="${min}" placeholder="min">
                    <input type="number" min="0" max="100" class="max-input mt-1 block w-1/2 rounded-md border-gray-300 p-1" value="${max}" placeholder="max">
                </div>
            </td>
            <td class="px-2 py-1 border">
                <input type="text" class="remark-input mt-1 block w-full rounded-md border-gray-300 p-1" value="${remark}" placeholder="Excellent">
            </td>
            <td class="px-2 py-1 border text-center">
                <button type="button" class="remove-row px-2 py-1 text-sm text-red-600">Remove</button>
            </td>
        `;
        tbody.appendChild(tr);
        const errTr = document.createElement('tr');
        errTr.className = 'error-row hidden';
        errTr.innerHTML = `<td class="px-2 py-1 border text-red-600" colspan="3"><div class="row-error text-red-600 text-sm"></div></td>`;
        tbody.appendChild(errTr);
    }

    addRemarksBtn.addEventListener('click', function () {
        addRemarksRow('', '');
    });

    function addClassRow(min = '', max = '', comment = '') {
        const tbody = classTable.querySelector('tbody');
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td class="px-2 py-1 border">
                <div class="flex gap-2">
                    <input type="number" min="0" max="100" class="min-input mt-1 block w-1/2 rounded-md border-gray-300 p-1" value="${min}" placeholder="min">
                    <input type="number" min="0" max="100" class="max-input mt-1 block w-1/2 rounded-md border-gray-300 p-1" value="${max}" placeholder="max">
                </div>
            </td>
            <td class="px-2 py-1 border">
                <input type="text" class="class-comment-input mt-1 block w-full rounded-md border-gray-300 p-1" value="${comment}" placeholder="Excellent Result">
            </td>
            <td class="px-2 py-1 border text-center">
                <button type="button" class="remove-row px-2 py-1 text-sm text-red-600">Remove</button>
            </td>
        `;
        tbody.appendChild(tr);
        const errTr = document.createElement('tr');
        errTr.className = 'error-row hidden';
        errTr.innerHTML = `<td class="px-2 py-1 border text-red-600" colspan="3"><div class="row-error text-red-600 text-sm"></div></td>`;
        tbody.appendChild(errTr);
    }

    addClassBtn.addEventListener('click', function () {
        addClassRow('', '');
    });

    function addPrincipalRow(min = '', max = '', comment = '') {
        const tbody = principalTable.querySelector('tbody');
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td class="px-2 py-1 border">
                <div class="flex gap-2">
                    <input type="number" min="0" max="100" class="min-input mt-1 block w-1/2 rounded-md border-gray-300 p-1" value="${min}" placeholder="min">
                    <input type="number" min="0" max="100" class="max-input mt-1 block w-1/2 rounded-md border-gray-300 p-1" value="${max}" placeholder="max">
                </div>
            </td>
            <td class="px-2 py-1 border">
                <input type="text" class="principal-comment-input mt-1 block w-full rounded-md border-gray-300 p-1" value="${comment}" placeholder="Principal's comment">
            </td>
            <td class="px-2 py-1 border text-center">
                <button type="button" class="remove-row px-2 py-1 text-sm text-red-600">Remove</button>
            </td>
        `;
        tbody.appendChild(tr);
        const errTr = document.createElement('tr');
        errTr.className = 'error-row hidden';
        errTr.innerHTML = `<td class="px-2 py-1 border text-red-600" colspan="3"><div class="row-error text-red-600 text-sm"></div></td>`;
        tbody.appendChild(errTr);
    }

    addPrincipalBtn.addEventListener('click', function () {
        addPrincipalRow('', '');
    });

    function addPromotionRow(min = '', max = '', status = '') {
        const tbody = promotionTable.querySelector('tbody');
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td class="px-2 py-1 border">
                <div class="flex gap-2">
                    <input type="number" min="0" max="100" class="min-input mt-1 block w-1/2 rounded-md border-gray-300 p-1" value="${min}" placeholder="min">
                    <input type="number" min="0" max="100" class="max-input mt-1 block w-1/2 rounded-md border-gray-300 p-1" value="${max}" placeholder="max">
                </div>
            </td>
            <td class="px-2 py-1 border">
                <input type="text" class="promotion-status-input mt-1 block w-full rounded-md border-gray-300 p-1" value="${status}" placeholder="PROMOTED TO NEXT CLASS">
            </td>
            <td class="px-2 py-1 border text-center">
                <button type="button" class="remove-row px-2 py-1 text-sm text-red-600">Remove</button>
            </td>
        `;
        tbody.appendChild(tr);
        const errTr = document.createElement('tr');
        errTr.className = 'error-row hidden';
        errTr.innerHTML = `<td class="px-2 py-1 border text-red-600" colspan="3"><div class="row-error text-red-600 text-sm"></div></td>`;
        tbody.appendChild(errTr);
    }

    addPromotionBtn.addEventListener('click', function () {
        addPromotionRow('', '');
    });

    // Delegate remove for all tables
    document.addEventListener('click', function (e) {
        if (e.target && e.target.classList.contains('remove-row')) {
            const tr = e.target.closest('tr');
            if (tr) tr.remove();
        }
    });

    // Helper to sync table data into hidden textareas
    function syncHiddenFields() {
        // Grading
        const gradingRows = gradingTable.querySelectorAll('tbody tr');
        const gradingLines = [];
        gradingRows.forEach(function (r) {
            const min = r.querySelector('.min-input')?.value.trim();
            const max = r.querySelector('.max-input')?.value.trim();
            const grade = r.querySelector('.grade-input')?.value.trim();
            if (min !== undefined && max !== undefined && grade) {
                gradingLines.push(`${min}-${max}:${grade}`);
            }
        });
        hidden.value = gradingLines.join('\n');

        // Remarks
        const remarkRows = remarksTable.querySelectorAll('tbody tr');
        const remarkLines = [];
        remarkRows.forEach(function (r) {
            const min = r.querySelector('.min-input')?.value.trim();
            const max = r.querySelector('.max-input')?.value.trim();
            const remark = r.querySelector('.remark-input')?.value.trim();
            if (min !== undefined && max !== undefined && remark) {
                remarkLines.push(`${min}-${max}:${remark}`);
            }
        });
        remarksHidden.value = remarkLines.join('\n');

        // Class teacher comments
        const classRows = classTable.querySelectorAll('tbody tr');
        const classLines = [];
        classRows.forEach(function (r) {
            const min = r.querySelector('.min-input')?.value.trim();
            const max = r.querySelector('.max-input')?.value.trim();
            const comment = r.querySelector('.class-comment-input')?.value.trim();
            if (min !== undefined && max !== undefined && comment) {
                classLines.push(`${min}-${max}:${comment}`);
            }
        });
        classHidden.value = classLines.join('\n');

        // Principal comments
        const principalRows = principalTable.querySelectorAll('tbody tr');
        const principalLines = [];
        principalRows.forEach(function (r) {
            const min = r.querySelector('.min-input')?.value.trim();
            const max = r.querySelector('.max-input')?.value.trim();
            const comment = r.querySelector('.principal-comment-input')?.value.trim();
            if (min !== undefined && max !== undefined && comment) {
                principalLines.push(`${min}-${max}:${comment}`);
            }
        });
        principalHidden.value = principalLines.join('\n');

        // Promotion status
        const promotionRows = promotionTable.querySelectorAll('tbody tr');
        const promotionLines = [];
        promotionRows.forEach(function (r) {
            const min = r.querySelector('.min-input')?.value.trim();
            const max = r.querySelector('.max-input')?.value.trim();
            const status = r.querySelector('.promotion-status-input')?.value.trim();
            if (min !== undefined && max !== undefined && status) {
                promotionLines.push(`${min}-${max}:${status}`);
            }
        });
        promotionHidden.value = promotionLines.join('\n');
    }

    // Wire inputs to keep hidden fields in sync live
    [gradingTable, remarksTable, classTable, principalTable, promotionTable].forEach(function (tbl) {
        tbl.addEventListener('input', function () { syncHiddenFields(); });
        tbl.addEventListener('change', function () { syncHiddenFields(); });
    });

    // On form submit, run validation then ensure fields are synced
    const form = document.querySelector('form[action="{{ route('report-settings.store') }}"]');
    form.addEventListener('submit', function (e) {
        syncHiddenFields();
        
        const clientErrors = validateTables();
        if (clientErrors.length) {
            e.preventDefault();
            const first = document.querySelector('.error-row:not(.hidden) .row-error');
            if (first) first.scrollIntoView({behavior:'smooth', block:'center'});
            return false;
        }
    });

    // Client-side validation function
    function validateTables() {
        const errors = [];
        
        function validateTable(table, name) {
            const tbody = table.querySelector('tbody');
            const nodes = Array.from(tbody.querySelectorAll('tr'));
            const dataRows = [];
            
            for (let i = 0; i < nodes.length; i += 2) {
                const data = nodes[i];
                const errRow = nodes[i+1];
                const min = data.querySelector('.min-input')?.value.trim();
                const max = data.querySelector('.max-input')?.value.trim();
                const labelInput = data.querySelector('.grade-input, .remark-input, .class-comment-input, .principal-comment-input, .promotion-status-input');
                const label = labelInput ? labelInput.value.trim() : '';
                dataRows.push({data, errRow, min, max, label});
            }

            const ranges = [];
            dataRows.forEach((r, idx) => {
                r.errRow.classList.add('hidden');
                r.errRow.querySelector('.row-error').textContent = '';
                r.data.classList.remove('border-2', 'border-red-500');

                if (r.min === '' && r.max === '' && r.label === '') return;
                const rowNum = idx + 1;
                
                if (r.label === '') {
                    const msg = `Line ${rowNum}: label is required.`;
                    r.errRow.querySelector('.row-error').textContent = msg;
                    r.errRow.classList.remove('hidden');
                    r.data.classList.add('border-2', 'border-red-500');
                    errors.push(msg);
                    return;
                }
                
                if (!/^-?\d+$/.test(r.min) || !/^-?\d+$/.test(r.max)) {
                    const msg = `Line ${rowNum}: min and max must be integers.`;
                    r.errRow.querySelector('.row-error').textContent = msg;
                    r.errRow.classList.remove('hidden');
                    r.data.classList.add('border-2', 'border-red-500');
                    errors.push(msg);
                    return;
                }
                
                let min = parseInt(r.min, 10);
                let max = parseInt(r.max, 10);
                if (min > max) { const t = min; min = max; max = t; }
                
                if (min < 0 || max > 100) {
                    const msg = `Line ${rowNum}: ranges must be between 0 and 100.`;
                    r.errRow.querySelector('.row-error').textContent = msg;
                    r.errRow.classList.remove('hidden');
                    r.data.classList.add('border-2', 'border-red-500');
                    errors.push(msg);
                    return;
                }
                
                ranges.push({min, max, idx, rowNum, data: r.data, errRow: r.errRow});
            });

            ranges.sort((a,b)=>a.min - b.min);
            for (let i=1;i<ranges.length;i++){
                if (ranges[i].min <= ranges[i-1].max) {
                    const msg = `Ranges overlap between lines ${ranges[i-1].rowNum} and ${ranges[i].rowNum}`;
                    ranges[i].errRow.querySelector('.row-error').textContent = msg;
                    ranges[i].errRow.classList.remove('hidden');
                    ranges[i].data.classList.add('border-2', 'border-red-500');
                    ranges[i-1].data.classList.add('border-2', 'border-red-500');
                    errors.push(msg);
                }
            }
        }

        validateTable(gradingTable, 'grading');
        validateTable(remarksTable, 'remarks');
        validateTable(classTable, 'class_teacher_comments');
        validateTable(principalTable, 'principal_comment');
        validateTable(promotionTable, 'promotion_status_rules');
        return errors;
    }

    // Apply server-side errors (if any) to per-row UI
    const serverErrors = @json($errors->all());
    if (serverErrors && serverErrors.length) {
        serverErrors.forEach(function (msg) {
            const m = msg.match(/^(\w+):\s*Line\s*(\d+)\s*(.*)$/);
            if (m) {
                const field = m[1];
                const line = parseInt(m[2], 10);
                const text = m[3] || '';
                let table = null;
                
                if (field === 'grading') table = gradingTable;
                if (field === 'remarks') table = remarksTable;
                if (field === 'class_teacher_comments') table = classTable;
                if (field === 'principal_comment') table = principalTable;
                if (field === 'promotion_status_rules') table = promotionTable;
                
                if (table) {
                    const tbody = table.querySelector('tbody');
                    const dataRows = Array.from(tbody.querySelectorAll('tr'));
                    const idx = (line-1)*2;
                    const errRow = dataRows[idx+1];
                    if (errRow) {
                        errRow.classList.remove('hidden');
                        const node = errRow.querySelector('.row-error');
                        node.textContent = text;
                        const dataRow = dataRows[idx];
                        if (dataRow) dataRow.classList.add('border-2', 'border-red-500');
                    }
                }
            }
        });
    }
});
</script>
@endpush
