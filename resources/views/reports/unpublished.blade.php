@extends('layouts.app')

@section('title', 'Results Unpublished')

@section('content')
<div class="container mx-auto py-12">
  <div class="bg-white p-6 rounded shadow text-center">
    <h3 class="text-2xl font-semibold">Results Not Yet Published</h3>
    <p class="mt-4 text-gray-700">Results for <strong>{{ $term->term_name ?? 'this term' }}</strong> are not published yet. Please check back later.</p>
  </div>
</div>
@endsection
