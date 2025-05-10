@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
    <div class="max-w-7xl mx-auto">
        <h1 class="text-2xl font-bold mb-6">Admin Dashboard</h1>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <!-- Stats Cards -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100 text-blue-600 mr-4">
                        <i class="fas fa-users text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-gray-500 text-sm">Total Users</h3>
                        <p class="text-2xl font-semibold">{{ \App\Models\User::count() }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100 text-green-600 mr-4">
                        <i class="fas fa-thumbtack text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-gray-500 text-sm">Total posts</h3>
                        <p class="text-2xl font-semibold">{{ \App\Models\Posts::count() }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-red-100 text-red-600 mr-4">
                        <i class="fas fa-comment text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-gray-500 text-sm">Total Comments</h3>
                        <p class="text-2xl font-semibold">{{ \App\Models\Comments::count() }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <h2 class="text-xl font-semibold mb-4">Quick Actions</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <a href="{{ route('admin.users.active') }}"
                    class="bg-gray-100 hover:bg-gray-200 p-4 rounded-lg text-center">
                    <i class="fas fa-user-check text-green-600 text-2xl mb-2"></i>
                    <p>Active Users</p>
                </a>
                <a href="{{ route('admin.users.inactive') }}"
                    class="bg-gray-100 hover:bg-gray-200 p-4 rounded-lg text-center">
                    <i class="fas fa-user-times text-red-600 text-2xl mb-2"></i>
                    <p>Inactive Users</p>
                </a>
                <a href="{{ route('admin.posts.archived') }}"
                    class="bg-gray-100 hover:bg-gray-200 p-4 rounded-lg text-center">
                    <i class="fas fa-archive text-yellow-600 text-2xl mb-2"></i>
                    <p>Archived posts</p>
                </a>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="bg-white rounded-lg shadow-md p-6 h-[500px] overflow-y-scroll">
            <h2 class="text-xl font-semibold mb-4">Recent Activity</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Item
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($activities as $activity)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <img class="h-10 w-10 rounded-full"
                                                src="{{ $activity->user->avatar ?? 'https://via.placeholder.com/40' }}"
                                                alt="">
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $activity->user->name ?? $activity->user->first_name . ' ' . $activity->user->last_name }}
                                            </div>
                                            <div class="text-sm text-gray-500">{{ $activity->user->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $color = match ($activity->action) {
                                            'created' => 'bg-green-100 text-green-800',
                                            'commented' => 'bg-blue-100 text-blue-800',
                                            'deleted' => 'bg-red-100 text-red-800',
                                            default => 'bg-gray-100 text-gray-800',
                                        };
                                    @endphp
                                    <span
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $color }}">
                                        {{ ucfirst($activity->action) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $activity->description }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $activity->created_at->diffForHumans() }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-4 text-center text-gray-500">No recent activity found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
