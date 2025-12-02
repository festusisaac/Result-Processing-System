@extends('layouts.app')

@section('title', 'Student')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="py-6">
        <h1 class="text-2xl font-semibold text-gray-900">{{ $student->full_name }}</h1>
    </div>

    <div class="bg-white shadow sm:rounded-lg p-6">
        <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div>
                <dt class="text-sm font-medium text-gray-500">Admission No</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $student->adm_no }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Class</dt>
                <dd id="student-class" class="mt-1 text-sm text-gray-900">{{ $student->classRoom->name ?? '-' }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Session</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $student->session->name ?? '-' }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Date of birth</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $student->dob ?? '-' }}</dd>
            </div>
        </dl>

        <div class="mt-6">
            <a href="{{ route('students.edit', $student) }}" class="px-4 py-2 bg-blue-600 text-white rounded">Edit</a>
            @php
                $user = auth()->user();
                $promoteToName = optional($student->classRoom)->promoting_class_name ?? null;
                $promoteTarget = null;
                if ($promoteToName) {
                    $promoteTarget = \App\Models\ClassRoom::where('name', $promoteToName)->first();
                }
            @endphp
            @if($user && ($user->role === 'admin' || ($user->isTeacher && $user->isTeacher())))
                @if($promoteToName && $promoteTarget)
                    <button id="promote-btn" data-target-name="{{ $promoteTarget->name }}" class="ml-3 px-4 py-2 bg-green-600 text-white rounded">Promote to {{ $promoteTarget->name }}</button>
                @elseif($promoteToName && ! $promoteTarget)
                    <button id="promote-btn" disabled title="Target class '{{ $promoteToName }}' not found" class="ml-3 px-4 py-2 bg-gray-400 text-white rounded cursor-not-allowed">Promote (target missing)</button>
                @else
                    <button id="promote-btn" disabled title="No promoting class configured for this student's class" class="ml-3 px-4 py-2 bg-gray-400 text-white rounded cursor-not-allowed">Promote (not configured)</button>
                @endif
            @endif
            <a href="{{ route('students.index') }}" class="ml-3 text-gray-600">Back</a>
        </div>

        @push('scripts')
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            const promoteBtn = document.getElementById('promote-btn');
            if (!promoteBtn) return;

            const originalLabel = promoteBtn.textContent;

            function setLoading(on) {
                if (on) {
                    promoteBtn.disabled = true;
                    promoteBtn.dataset.origLabel = promoteBtn.textContent;
                    promoteBtn.innerHTML = '<span class="inline-block w-4 h-4 mr-2 border-2 border-white border-t-transparent rounded-full animate-spin"></span>Promoting...';
                } else {
                    promoteBtn.disabled = false;
                    promoteBtn.textContent = promoteBtn.dataset.origLabel || originalLabel;
                }
            }

            promoteBtn.addEventListener('click', async function() {
                if (!confirm('Promote this student to the next class?')) return;
                setLoading(true);
                try {
                    const res = await fetch("{{ route('students.promote', $student) }}", {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    });

                    // handle non-json gracefully
                    let body = {};
                    const ct = res.headers.get('content-type') || '';
                    if (ct.includes('application/json')) {
                        body = await res.json();
                    } else {
                        body = { message: await res.text() };
                    }

                    if (!res.ok) {
                        const message = body.message || ('Failed to promote (status ' + res.status + ')');
                        if (window.showToast) window.showToast(message, 'error'); else alert(message);
                        setLoading(false);
                        return;
                    }

                    // update class display
                    const classEl = document.getElementById('student-class');
                    if (classEl && body.to) classEl.textContent = body.to;

                    // update button label to reflect new class (or hide it)
                    const targetName = promoteBtn.dataset.targetName;
                    if (body.to) {
                        promoteBtn.textContent = 'Promoted to ' + body.to;
                        promoteBtn.disabled = true;
                    } else if (targetName) {
                        promoteBtn.textContent = 'Promote to ' + targetName;
                        promoteBtn.disabled = true;
                    }

                    if (window.showToast) window.showToast(body.message || 'Student promoted', 'success');
                    else alert(body.message || 'Student promoted');
                } catch (e) {
                    console.error('Promotion error', e);
                    if (window.showToast) window.showToast('Failed to promote student', 'error'); else alert('Failed to promote student');
                } finally {
                    // leave disabled after success or re-enable on error
                    if (promoteBtn.textContent && promoteBtn.textContent.toLowerCase().startsWith('promoted')) {
                        promoteBtn.disabled = true;
                    } else {
                        setLoading(false);
                    }
                }
            });
        });
        </script>
        @endpush
    </div>
</div>
@endsection
