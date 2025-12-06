@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-description', 'Welcome back! Here\'s what\'s happening with your school.')

@push('styles')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@endpush

@section('content')
<div class="space-y-6">
    {{-- Active Session Banner --}}
    @if($activeSession)
    <div class="rounded-lg shadow-lg p-6 text-white" style="background: linear-gradient(to right, #4F46E5, #9333EA);">
        <div class="flex items-center justify-between flex-wrap gap-4">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="flex items-center gap-2 text-sm font-medium text-indigo-100">
                        <span class="flex h-2 w-2 relative">
                            <span class="animate-ping absolute inline-flex h-2 w-2 rounded-full bg-green-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span>
                        </span>
                        Active Session
                    </span>
                </div>
                <h2 class="text-2xl font-bold">{{ $activeSession->name }}</h2>
                <p class="text-indigo-100 mt-1">
                    @if($currentTerm)
                        Current Term: <span class="font-semibold">{{ $currentTerm->name }}</span>
                    @else
                        No active term
                    @endif
                </p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('students.create') }}" class="bg-white text-indigo-600 px-4 py-2 rounded-lg font-medium hover:bg-indigo-50 transition-colors flex items-center gap-2">
                    <i class="fas fa-user-plus"></i> Add Student
                </a>
                <a href="{{ route('scratch-cards.index') }}" class="bg-indigo-500 text-white px-4 py-2 rounded-lg font-medium hover:bg-indigo-400 transition-colors flex items-center gap-2">
                    <i class="fas fa-ticket-alt"></i> Generate Cards
                </a>
            </div>
        </div>
    </div>
    @else
    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-6 rounded-lg">
        <div class="flex items-center">
            <i class="fas fa-exclamation-triangle text-yellow-400 text-2xl mr-3"></i>
            <div>
                <h3 class="text-lg font-medium text-yellow-800">No Active Session</h3>
                <p class="text-yellow-700 mt-1">Please activate an academic session to enable all features.</p>
            </div>
        </div>
    </div>
    @endif

    {{-- Stats Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        {{-- Total Students --}}
        <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow duration-300">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Students</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">{{ number_format($totalStudents) }}</p>
                    </div>
                    <div class="bg-indigo-100 rounded-full p-4">
                        <i class="fas fa-user-graduate text-indigo-600 text-2xl"></i>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-6 py-3 border-t border-gray-100">
                <a href="{{ route('students.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium flex items-center gap-1">
                    View all <i class="fas fa-arrow-right text-xs"></i>
                </a>
            </div>
        </div>

        {{-- Total Classes --}}
        <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow duration-300">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Classes</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">{{ number_format($totalClasses) }}</p>
                    </div>
                    <div class="bg-green-100 rounded-full p-4">
                        <i class="fas fa-chalkboard text-green-600 text-2xl"></i>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-6 py-3 border-t border-gray-100">
                <a href="{{ route('classes.index') }}" class="text-sm text-green-600 hover:text-green-800 font-medium flex items-center gap-1">
                    Manage <i class="fas fa-arrow-right text-xs"></i>
                </a>
            </div>
        </div>

        {{-- Total Subjects --}}
        <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow duration-300">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Subjects</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">{{ number_format($totalSubjects) }}</p>
                    </div>
                    <div class="bg-purple-100 rounded-full p-4">
                        <i class="fas fa-book text-purple-600 text-2xl"></i>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-6 py-3 border-t border-gray-100">
                <a href="{{ route('subjects.index') }}" class="text-sm text-purple-600 hover:text-purple-800 font-medium flex items-center gap-1">
                    View all <i class="fas fa-arrow-right text-xs"></i>
                </a>
            </div>
        </div>

        {{-- Available Cards --}}
        <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow duration-300">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Available Cards</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">{{ number_format($availableCards) }}</p>
                    </div>
                    <div class="bg-yellow-100 rounded-full p-4">
                        <i class="fas fa-ticket-alt text-yellow-600 text-2xl"></i>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-6 py-3 border-t border-gray-100">
                <a href="{{ route('scratch-cards.index') }}" class="text-sm text-yellow-600 hover:text-yellow-800 font-medium flex items-center gap-1">
                    Manage <i class="fas fa-arrow-right text-xs"></i>
                </a>
            </div>
        </div>

        {{-- Results Processed --}}
        <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow duration-300">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Results Processed</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">{{ number_format($totalResultsProcessed) }}</p>
                    </div>
                    <div class="bg-blue-100 rounded-full p-4">
                        <i class="fas fa-file-alt text-blue-600 text-2xl"></i>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-6 py-3 border-t border-gray-100">
                <a href="{{ route('results.index') }}" class="text-sm text-blue-600 hover:text-blue-800 font-medium flex items-center gap-1">
                    View results <i class="fas fa-arrow-right text-xs"></i>
                </a>
            </div>
        </div>

        {{-- Success Rate --}}
        <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow duration-300">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Success Rate</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">{{ $successRate }}%</p>
                    </div>
                    <div class="bg-emerald-100 rounded-full p-4">
                        <i class="fas fa-chart-line text-emerald-600 text-2xl"></i>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-6 py-3 border-t border-gray-100">
                <p class="text-sm text-gray-600">Students with ≥50% average</p>
            </div>
        </div>

        {{-- Average Score --}}
        <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow duration-300">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Average Score</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">{{ $averageScore }}%</p>
                    </div>
                    <div class="bg-orange-100 rounded-full p-4">
                        <i class="fas fa-star text-orange-600 text-2xl"></i>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-6 py-3 border-t border-gray-100">
                <p class="text-sm text-gray-600">Across all subjects</p>
            </div>
        </div>

        {{-- Attendance Rate --}}
        <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow duration-300">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Attendance Rate</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">{{ $attendanceRate }}%</p>
                    </div>
                    <div class="bg-teal-100 rounded-full p-4">
                        <i class="fas fa-user-check text-teal-600 text-2xl"></i>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-6 py-3 border-t border-gray-100">
                <a href="{{ route('attendance.index') }}" class="text-sm text-teal-600 hover:text-teal-800 font-medium flex items-center gap-1">
                    View records <i class="fas fa-arrow-right text-xs"></i>
                </a>
            </div>
        </div>
    </div>

    {{-- Charts Section --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Students per Class --}}
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                <i class="fas fa-chart-bar text-indigo-600"></i>
                Students per Class
            </h3>
            <div class="relative" style="height: 300px;">
                <canvas id="studentsPerClassChart"></canvas>
            </div>
        </div>

        {{-- Score Distribution --}}
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                <i class="fas fa-chart-pie text-purple-600"></i>
                Score Distribution
            </h3>
            <div class="relative" style="height: 300px;">
                <canvas id="scoreDistributionChart"></canvas>
            </div>
        </div>

        {{-- Subject Performance --}}
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                <i class="fas fa-chart-area text-green-600"></i>
                Subject Performance
            </h3>
            <div class="relative" style="height: 300px;">
                <canvas id="subjectPerformanceChart"></canvas>
            </div>
        </div>

        {{-- Enrollment Trend --}}
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                <i class="fas fa-chart-line text-blue-600"></i>
                Enrollment Trend (6 Months)
            </h3>
            <div class="relative" style="height: 300px;">
                <canvas id="enrollmentTrendChart"></canvas>
            </div>
        </div>
    </div>

    {{-- Tables and Activity Section --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Top Performing Classes --}}
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                <i class="fas fa-trophy text-yellow-500"></i>
                Top Performing Classes
            </h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Class</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Students</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Avg Score</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($topClasses as $class)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $class->name }}</td>
                            <td class="px-4 py-3 text-sm text-gray-600">{{ $class->student_count }}</td>
                            <td class="px-4 py-3 text-sm">
                                <span class="px-2 py-1 rounded-full text-xs font-semibold 
                                    {{ $class->average_score >= 70 ? 'bg-green-100 text-green-800' : ($class->average_score >= 50 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                    {{ $class->average_score }}%
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="px-4 py-3 text-sm text-gray-500 text-center">No data available</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Recent Activity --}}
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                <i class="fas fa-history text-indigo-600"></i>
                Recent Activity
            </h3>
            <div class="space-y-4 max-h-96 overflow-y-auto">
                @forelse($recentActivities as $activity)
                <div class="flex items-start gap-3 pb-3 border-b border-gray-100 last:border-0">
                    <div class="flex-shrink-0 mt-1">
                        <div class="h-8 w-8 rounded-full bg-indigo-100 flex items-center justify-center">
                            <i class="fas fa-user text-indigo-600 text-xs"></i>
                        </div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900">{{ $activity->formatted_action ?? $activity->action }}</p>
                        @if($activity->meta)
                        <p class="text-xs text-gray-500 mt-1">{{ implode(' ', (array)$activity->meta) }}</p>
                        @endif
                        <p class="text-xs text-gray-400 mt-1">{{ $activity->created_at->diffForHumans() }}</p>
                    </div>
                </div>
                @empty
                <p class="text-sm text-gray-500 text-center py-4">No recent activity</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
            <i class="fas fa-bolt text-yellow-500"></i>
            Quick Actions
        </h3>
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
            <a href="{{ route('students.create') }}" class="flex flex-col items-center p-4 rounded-lg hover:bg-gray-50 transition-colors group">
                <div class="bg-indigo-100 group-hover:bg-indigo-200 rounded-full p-3 mb-2">
                    <i class="fas fa-user-plus text-indigo-600 text-xl"></i>
                </div>
                <span class="text-sm font-medium text-gray-700">Add Student</span>
            </a>
            <a href="{{ route('scores.scoresheet') }}" class="flex flex-col items-center p-4 rounded-lg hover:bg-gray-50 transition-colors group">
                <div class="bg-green-100 group-hover:bg-green-200 rounded-full p-3 mb-2">
                    <i class="fas fa-table text-green-600 text-xl"></i>
                </div>
                <span class="text-sm font-medium text-gray-700">Scoresheet</span>
            </a>
            <a href="{{ route('attendance.index') }}" class="flex flex-col items-center p-4 rounded-lg hover:bg-gray-50 transition-colors group">
                <div class="bg-teal-100 group-hover:bg-teal-200 rounded-full p-3 mb-2">
                    <i class="fas fa-clipboard-check text-teal-600 text-xl"></i>
                </div>
                <span class="text-sm font-medium text-gray-700">Attendance</span>
            </a>
            <a href="{{ route('results.index') }}" class="flex flex-col items-center p-4 rounded-lg hover:bg-gray-50 transition-colors group">
                <div class="bg-blue-100 group-hover:bg-blue-200 rounded-full p-3 mb-2">
                    <i class="fas fa-poll text-blue-600 text-xl"></i>
                </div>
                <span class="text-sm font-medium text-gray-700">Results</span>
            </a>
            <a href="{{ route('classes.index') }}" class="flex flex-col items-center p-4 rounded-lg hover:bg-gray-50 transition-colors group">
                <div class="bg-purple-100 group-hover:bg-purple-200 rounded-full p-3 mb-2">
                    <i class="fas fa-chalkboard text-purple-600 text-xl"></i>
                </div>
                <span class="text-sm font-medium text-gray-700">Classes</span>
            </a>
            <a href="{{ route('report-settings.index') }}" class="flex flex-col items-center p-4 rounded-lg hover:bg-gray-50 transition-colors group">
                <div class="bg-orange-100 group-hover:bg-orange-200 rounded-full p-3 mb-2">
                    <i class="fas fa-cog text-orange-600 text-xl"></i>
                </div>
                <span class="text-sm font-medium text-gray-700">Settings</span>
            </a>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Students per Class Chart
new Chart(document.getElementById('studentsPerClassChart'), {
    type: 'bar',
    data: {
        labels: {!! json_encode($studentsPerClass->pluck('name')) !!},
        datasets: [{
            label: 'Number of Students',
            data: {!! json_encode($studentsPerClass->pluck('count')) !!},
            backgroundColor: 'rgba(79, 70, 229, 0.8)',
            borderColor: 'rgba(79, 70, 229, 1)',
            borderWidth: 1,
            borderRadius: 8,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: { stepSize: 1 }
            }
        }
    }
});

// Score Distribution Chart
new Chart(document.getElementById('scoreDistributionChart'), {
    type: 'doughnut',
    data: {
        labels: {!! json_encode(array_keys($scoreDistribution)) !!},
        datasets: [{
            data: {!! json_encode(array_values($scoreDistribution)) !!},
            backgroundColor: [
                'rgba(16, 185, 129, 0.8)',
                'rgba(59, 130, 246, 0.8)',
                'rgba(251, 191, 36, 0.8)',
                'rgba(249, 115, 22, 0.8)',
                'rgba(239, 68, 68, 0.8)',
            ],
            borderWidth: 2,
            borderColor: '#fff'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom',
                labels: { padding: 15, font: { size: 11 } }
            }
        }
    }
});

// Subject Performance Chart
new Chart(document.getElementById('subjectPerformanceChart'), {
    type: 'radar',
    data: {
        labels: {!! json_encode($subjectPerformance->pluck('name')) !!},
        datasets: [{
            label: 'Average Score',
            data: {!! json_encode($subjectPerformance->pluck('average')) !!},
            backgroundColor: 'rgba(16, 185, 129, 0.2)',
            borderColor: 'rgba(16, 185, 129, 1)',
            borderWidth: 2,
            pointBackgroundColor: 'rgba(16, 185, 129, 1)',
            pointBorderColor: '#fff',
            pointHoverBackgroundColor: '#fff',
            pointHoverBorderColor: 'rgba(16, 185, 129, 1)'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            r: {
                beginAtZero: true,
                max: 100,
                ticks: { stepSize: 20 }
            }
        }
    }
});

// Enrollment Trend Chart
new Chart(document.getElementById('enrollmentTrendChart'), {
    type: 'line',
    data: {
        labels: {!! json_encode(array_column($enrollmentTrend, 'month')) !!},
        datasets: [{
            label: 'New Enrollments',
            data: {!! json_encode(array_column($enrollmentTrend, 'count')) !!},
            backgroundColor: 'rgba(59, 130, 246, 0.1)',
            borderColor: 'rgba(59, 130, 246, 1)',
            borderWidth: 2,
            fill: true,
            tension: 0.4,
            pointBackgroundColor: 'rgba(59, 130, 246, 1)',
            pointBorderColor: '#fff',
            pointBorderWidth: 2,
            pointRadius: 4,
            pointHoverRadius: 6
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: { stepSize: 1 }
            }
        }
    }
});
</script>
@endpush

@endsection