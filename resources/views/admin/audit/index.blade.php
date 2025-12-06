@extends('layouts.app')

@section('title', 'Activity Logs')
@section('page-title', 'Activity Logs')
@section('page-description', 'Track and monitor system activities.')

@section('content')
<div class="space-y-6">
    <!-- Filters -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
        <form action="{{ route('audit-logs.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Search Action</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="e.g. Updated Score" 
                    class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm text-sm">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">User</label>
                <select name="user_id" class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm text-sm">
                    <option value="">All Users</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                            {{ $user->name }} ({{ $user->getRoleDisplayName() }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Date From</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}" 
                    class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm text-sm">
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition-colors text-sm font-medium shadow-sm">
                    <i class="fas fa-filter mr-2"></i> Filter
                </button>
                <a href="{{ route('audit-logs.index') }}" class="bg-gray-100 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200 transition-colors text-sm font-medium">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Logs Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date & Time</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Details</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200 text-sm">
                    @forelse($logs as $log)
                    <tr class="hover:bg-gray-50 transition-colors" x-data="{ expanded: false }">
                        <td class="px-6 py-4 whitespace-nowrap text-gray-500">
                            {{ $log->created_at->format('M d, Y H:i:s') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($log->user)
                                <div class="flex items-center">
                                    <div class="h-8 w-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-bold text-xs mr-2">
                                        {{ strtoupper(substr($log->user->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="font-medium text-gray-900">{{ $log->user->name }}</div>
                                        <div class="text-xs text-gray-500">{{ $log->user->getRoleDisplayName() }}</div>
                                    </div>
                                </div>
                            @else
                                <span class="text-gray-400 italic">System / Deleted User</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-gray-700 font-medium">
                            {{ $log->action }}
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $meta = is_string($log->meta) ? json_decode($log->meta, true) : $log->meta;
                                $hasChanges = !empty($meta['old']) || !empty($meta['new']);
                            @endphp

                            @if($hasChanges)
                                <button @click="expanded = !expanded" class="text-indigo-600 hover:text-indigo-800 text-xs font-medium focus:outline-none">
                                    <span x-text="expanded ? 'Hide Details' : 'View Changes'"></span>
                                    <i class="fas fa-chevron-down ml-1 transition-transform" :class="expanded ? 'rotate-180' : ''"></i>
                                </button>
                                
                                <div x-show="expanded" x-collapse class="mt-2 bg-gray-50 rounded p-3 text-xs border border-gray-200">
                                    <div class="grid grid-cols-2 gap-4">
                                        @if(!empty($meta['old']))
                                        <div>
                                            <span class="font-bold text-red-600 block mb-1">Old Values:</span>
                                            <ul class="list-disc list-inside space-y-1 text-gray-600">
                                                @foreach($meta['old'] as $key => $value)
                                                    <li><span class="font-semibold">{{ $key }}:</span> {{ is_array($value) ? json_encode($value) : $value }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                        @endif
                                        
                                        @if(!empty($meta['new']))
                                        <div>
                                            <span class="font-bold text-green-600 block mb-1">New Values:</span>
                                            <ul class="list-disc list-inside space-y-1 text-gray-600">
                                                @foreach($meta['new'] as $key => $value)
                                                    <li><span class="font-semibold">{{ $key }}:</span> {{ is_array($value) ? json_encode($value) : $value }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                        @endif
                                    </div>
                                    
                                    @if(isset($meta['ip_address']))
                                    <div class="mt-2 pt-2 border-t border-gray-200 text-gray-400 text-xs text-right">
                                        IP: {{ $meta['ip_address'] }}
                                    </div>
                                    @endif
                                </div>
                            @else
                                <span class="text-gray-400 text-xs">No detailed changes</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                            <i class="fas fa-clipboard-list text-4xl mb-3 text-gray-300"></i>
                            <p>No activity logs found matching your criteria.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($logs->hasPages())
        <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
            {{ $logs->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
