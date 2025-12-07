@extends('layouts.app')

@section('content')
<div class="flex flex-col items-center justify-center min-h-[60vh] text-center px-4">
    <div class="bg-indigo-50 rounded-full p-6 mb-4">
        <i class="fas fa-wifi text-4xl text-indigo-400"></i>
    </div>
    <h1 class="text-2xl font-bold text-gray-900 mb-2">You're Offline</h1>
    <p class="text-gray-600 mb-6 max-w-sm">
        It looks like you've lost your internet connection. 
        Don't worry, you can still access pages you've visited recently.
    </p>
    <button onclick="window.location.reload()" class="bg-indigo-600 text-white px-6 py-2 rounded-lg font-medium hover:bg-indigo-700 transition-colors">
        Try Again
    </button>
</div>
@endsection
