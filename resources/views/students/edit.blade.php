@extends('layouts.app')

@section('title', 'Edit Student')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="py-6">
        <h1 class="text-2xl font-semibold text-gray-900">Edit Student</h1>
    </div>

    @if ($errors->any())
    <div class="mb-4 p-4 bg-red-100 text-red-700 rounded">
        <ul class="list-disc pl-5">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="bg-white shadow sm:rounded-lg p-6">
        <form action="{{ route('students.update', $student) }}" method="POST" class="space-y-4" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-medium text-gray-700">Admission No</label>
                <input name="adm_no" value="{{ old('adm_no', $student->adm_no) }}" required class="mt-1 block w-full rounded-md border-gray-300 p-2" />
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Full name</label>
                <input name="full_name" value="{{ old('full_name', $student->full_name) }}" required class="mt-1 block w-full rounded-md border-gray-300 p-2" />
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Class</label>
                <select name="class_id" required class="mt-1 block w-full rounded-md border-gray-300 p-2">
                    <option value="">Choose class</option>
                    @foreach($classes as $c)
                        <option value="{{ $c->id }}" {{ $student->class_id == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Session</label>
                <select name="session_id" required class="mt-1 block w-full rounded-md border-gray-300 p-2">
                    <option value="">Choose session</option>
                    @foreach($sessions as $s)
                        <option value="{{ $s->id }}" {{ $student->session_id == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Date of birth</label>
                <input type="date" name="dob" value="{{ old('dob', $student->dob ? $student->dob->format('Y-m-d') : '') }}" class="mt-1 block w-full rounded-md border-gray-300 p-2" />
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Gender</label>
                <select name="gender" class="mt-1 block w-full rounded-md border-gray-300 p-2">
                    <option value="">Select</option>
                    <option value="male" {{ $student->gender=='male' ? 'selected' : '' }}>Male</option>
                    <option value="female" {{ $student->gender=='female' ? 'selected' : '' }}>Female</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Passport Photograph</label>
                @if($student->passport)
                    <div class="mt-2 mb-2">
                        <img src="{{ asset('storage/' . $student->passport) }}" alt="Current Passport" class="h-20 w-20 object-cover rounded-full">
                    </div>
                @endif
                <input type="file" name="passport" accept="image/*" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" />
                <p class="mt-1 text-sm text-gray-500">Upload to replace current photo (optional)</p>
            </div>

            <div class="pt-4">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Update student</button>
                <a href="{{ route('students.index') }}" class="ml-3 text-gray-600">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
