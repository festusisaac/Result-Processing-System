@extends('layouts.app')

@section('title', 'Result Management')
@section('page-title', 'Result Management')

@section('content')
<style>
    .rm-toast { position: fixed; top: 20px; right: 20px; background: #10b981; color: white; padding: 12px 16px; border-radius:6px; z-index:9999 }
    .rm-table { width:100%; border-collapse: collapse }
    .rm-table th, .rm-table td { padding:8px 10px; border-bottom:1px solid #eee }
</style>

<div class="container mx-auto py-8">
    <div class="bg-white rounded shadow p-6">
        <h2 class="text-xl font-semibold mb-4">Result Management</h2>
        <p class="text-sm text-gray-600 mb-4">Manage lifecycle of result batches. Use Publish to make results available to students.</p>

        <table class="rm-table">
            <thead>
                <tr>
                    <th>Term</th>
                    <th>Session</th>
                    <th>Students</th>
                    <th>Status</th>
                    <th>Published At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($terms as $term)
                <tr data-term-id="{{ $term->id }}">
                    <td>{{ $term->term_name }}</td>
                    <td>{{ $term->session?->name }}</td>
                    <td class="rm-student-count">{{ $term->student_count ?? 0 }}</td>
                    <td class="rm-status">{{ $term->result_status ?? 'DRAFT' }}</td>
                    <td class="rm-published-at">{{ optional($term->published_at)->toDayDateTimeString() }}</td>
                    <td>
                        <button class="btn-publish px-3 py-1 bg-indigo-600 text-white rounded" data-id="{{ $term->id }}" data-count="{{ $term->student_count ?? 0 }}">Publish</button>
                        <button class="btn-withdraw px-3 py-1 bg-yellow-500 text-white rounded" data-id="{{ $term->id }}">Withdraw</button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="mt-4">{{ $terms->links() }}</div>
    </div>
</div>

<!-- Confirmation Modal -->
<div id="rm-confirm" class="fixed inset-0 bg-black bg-opacity-40 hidden items-center justify-center">
    <div class="bg-white p-6 rounded shadow max-w-lg w-full">
        <h3 class="text-lg font-semibold" id="rm-confirm-title">Confirm</h3>
        <p class="mt-2 text-sm text-gray-700" id="rm-confirm-body">Are you sure?</p>
        <div class="mt-4 flex justify-end gap-2">
            <button id="rm-cancel" class="px-4 py-2 bg-gray-200 rounded">Cancel</button>
            <button id="rm-confirm-ok" class="px-4 py-2 bg-indigo-600 text-white rounded">Confirm</button>
        </div>
    </div>
</div>

<script>
    function rmToast(msg) { const d=document.createElement('div'); d.className='rm-toast'; d.textContent=msg; document.body.appendChild(d); setTimeout(()=>d.remove(),4000); }

    document.addEventListener('click', function(e){
        if (e.target.matches('.btn-publish')) {
            const id = e.target.getAttribute('data-id');
            const count = e.target.getAttribute('data-count') || 0;
            showRmConfirm('Publish Results', `Are you sure you want to publish results for this term? This will make results available to ${count} student(s).`, async () => {
                await fetch(`/reports/result-management/${id}/publish`, { method: 'POST', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept':'application/json' } })
                    .then(r => r.json()).then(b => { rmToast(b.message || 'Published'); updateRowStatus(id,'PUBLISHED'); })
                    .catch(()=>rmToast('Failed to publish'));
            });
        }

        if (e.target.matches('.btn-withdraw')) {
            const id = e.target.getAttribute('data-id');
            showRmConfirm('Withdraw Results', `Withdraw results for this term (will block student access). Are you sure?`, async () => {
                await fetch(`/reports/result-management/${id}/unpublish`, { method: 'POST', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept':'application/json' } })
                    .then(r => r.json()).then(b => { rmToast(b.message || 'Withdrawn'); updateRowStatus(id,'WITHDRAWN'); })
                    .catch(()=>rmToast('Failed to withdraw'));
            });
        }
    });

    function showRmConfirm(title, body, okCb) {
        const modal = document.getElementById('rm-confirm');
        document.getElementById('rm-confirm-title').textContent = title;
        document.getElementById('rm-confirm-body').textContent = body;
        modal.classList.remove('hidden'); modal.classList.add('flex');
        const okBtn = document.getElementById('rm-confirm-ok');
        const cancelBtn = document.getElementById('rm-cancel');
        const cleanup = () => { modal.classList.add('hidden'); modal.classList.remove('flex'); okBtn.removeEventListener('click', okClick); cancelBtn.removeEventListener('click', cancelClick); };
        const okClick = () => { okCb(); cleanup(); };
        const cancelClick = () => { cleanup(); };
        okBtn.addEventListener('click', okClick);
        cancelBtn.addEventListener('click', cancelClick);
    }

    function updateRowStatus(id, status) {
        const row = document.querySelector(`tr[data-term-id="${id}"]`);
        if (!row) return;
        row.querySelector('.rm-status').textContent = status;
        row.querySelector('.rm-published-at').textContent = new Date().toLocaleString();
    }
</script>

@endsection
