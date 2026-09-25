@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">

    {{-- ============================================ --}}
    {{-- PAGE HEADER --}}
    {{-- ============================================ --}}
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Manage Accounts</h1>
            <p class="text-sm text-gray-500 mt-1">Approve, disable, or remove user accounts</p>
        </div>

        <a href="{{ route('admin.dashboard') }}"
           class="inline-flex items-center gap-2 text-sm text-gray-600 hover:text-gray-900 font-medium transition">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Back to Dashboard
        </a>
    </div>

    {{-- ============================================ --}}
    {{-- ALERTS --}}
    {{-- ============================================ --}}
    @if (session('success'))
        <div class="mb-4 p-3 bg-green-50 border border-green-200 text-green-800 rounded-lg text-sm flex items-center gap-2">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-800 rounded-lg text-sm flex items-center gap-2">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            {{ session('error') }}
        </div>
    @endif

    {{-- ============================================ --}}
    {{-- FILTER TABS --}}
    {{-- ============================================ --}}
    <div class="bg-white rounded-lg border border-gray-200 p-2 mb-4 overflow-x-auto">
        <div class="flex gap-1 min-w-max">
            @php
                $tabs = [
                    'all' => 'All',
                    'customer' => 'Customers',
                    'restaurant' => 'Restaurants',
                    'rider' => 'Riders',
                    'pending' => 'Pending',
                    'disabled' => 'Disabled',
                ];
            @endphp

            @foreach ($tabs as $key => $label)
                <a href="{{ route('admin.accounts', ['filter' => $key]) }}"
                   class="px-3.5 py-2 rounded-md text-sm font-medium whitespace-nowrap transition
                          {{ $filter === $key
                                ? 'bg-gray-900 text-white'
                                : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                    {{ $label }}
                    <span class="ml-1 text-xs {{ $filter === $key ? 'text-gray-300' : 'text-gray-400' }}">
                        ({{ $counts[$key] }})
                    </span>
                </a>
            @endforeach
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- ACCOUNTS TABLE --}}
    {{-- ============================================ --}}
    <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="text-left px-5 py-3 font-medium text-gray-600 text-xs uppercase tracking-wide">Name</th>
                        <th class="text-left px-5 py-3 font-medium text-gray-600 text-xs uppercase tracking-wide">Email</th>
                        <th class="text-left px-5 py-3 font-medium text-gray-600 text-xs uppercase tracking-wide">Role</th>
                        <th class="text-left px-5 py-3 font-medium text-gray-600 text-xs uppercase tracking-wide">Status</th>
                        <th class="text-left px-5 py-3 font-medium text-gray-600 text-xs uppercase tracking-wide">Registered</th>
                        <th class="text-right px-5 py-3 font-medium text-gray-600 text-xs uppercase tracking-wide">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($users as $user)
                        <tr class="hover:bg-gray-50 transition">

                            {{-- NAME + PHONE --}}
                            <td class="px-5 py-3">
                                <div class="font-medium text-gray-900">{{ $user->name }}</div>
                                @if ($user->phone)
                                    <div class="text-xs text-gray-500 mt-0.5">{{ $user->phone }}</div>
                                @endif
                            </td>

                            {{-- EMAIL --}}
                            <td class="px-5 py-3 text-gray-600">{{ $user->email }}</td>

                            {{-- ROLE BADGE --}}
                            <td class="px-5 py-3">
                                @php
                                    $roleStyles = [
                                        'admin' => 'bg-purple-50 text-purple-700 border-purple-200',
                                        'restaurant' => 'bg-blue-50 text-blue-700 border-blue-200',
                                        'rider' => 'bg-teal-50 text-teal-700 border-teal-200',
                                        'customer' => 'bg-gray-50 text-gray-700 border-gray-200',
                                    ];
                                    $roleClass = $roleStyles[$user->role] ?? 'bg-gray-50 text-gray-700 border-gray-200';
                                @endphp
                                <span class="inline-block text-xs px-2 py-0.5 rounded-md border font-medium {{ $roleClass }}">
                                    {{ ucfirst($user->role) }}
                                </span>
                            </td>

                            {{-- STATUS BADGE --}}
                            <td class="px-5 py-3">
                                @php
                                    $statusStyles = [
                                        'approved' => 'bg-green-50 text-green-700 border-green-200',
                                        'pending' => 'bg-amber-50 text-amber-700 border-amber-200',
                                        'disabled' => 'bg-red-50 text-red-700 border-red-200',
                                    ];
                                    $statusClass = $statusStyles[$user->status] ?? 'bg-gray-50 text-gray-700 border-gray-200';

                                    $statusDot = [
                                        'approved' => 'bg-green-500',
                                        'pending' => 'bg-amber-500',
                                        'disabled' => 'bg-red-500',
                                    ];
                                    $dotClass = $statusDot[$user->status] ?? 'bg-gray-400';
                                @endphp
                                <span class="inline-flex items-center gap-1.5 text-xs px-2 py-0.5 rounded-md border font-medium {{ $statusClass }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $dotClass }}"></span>
                                    {{ ucfirst($user->status) }}
                                </span>
                            </td>

                            {{-- REGISTERED DATE --}}
                            <td class="px-5 py-3 text-xs text-gray-500">
                                {{ $user->created_at->format('M d, Y') }}
                            </td>

                            {{-- ACTIONS --}}
                            <td class="px-5 py-3">
                                <div class="flex items-center justify-end gap-1.5">

                                    {{-- APPROVE --}}
                                    @if ($user->status !== 'approved')
                                        <form method="POST" action="{{ route('admin.users.approve', $user) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                    title="Approve user"
                                                    class="inline-flex items-center gap-1 text-xs font-medium text-green-700 bg-green-50 hover:bg-green-100 border border-green-200 px-2.5 py-1.5 rounded-md transition">
                                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                </svg>
                                                Approve
                                            </button>
                                        </form>
                                    @endif

                                    {{-- DISABLE --}}
                                    @if ($user->status !== 'disabled' && $user->role !== 'admin')
                                        <form method="POST" action="{{ route('admin.users.disable', $user) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                    title="Disable user"
                                                    onclick="return confirm('Disable {{ $user->name }}? They won\'t be able to log in.')"
                                                    class="inline-flex items-center gap-1 text-xs font-medium text-amber-700 bg-amber-50 hover:bg-amber-100 border border-amber-200 px-2.5 py-1.5 rounded-md transition">
                                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                          d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                                </svg>
                                                Disable
                                            </button>
                                        </form>
                                    @endif

                                    {{-- DELETE --}}
                                    @if ($user->role !== 'admin')
                                        <form method="POST" action="{{ route('admin.users.destroy', $user) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    title="Delete user"
                                                    onclick="return confirm('Delete {{ $user->name }} permanently? This cannot be undone.')"
                                                    class="inline-flex items-center gap-1 text-xs font-medium text-red-700 bg-red-50 hover:bg-red-100 border border-red-200 px-2.5 py-1.5 rounded-md transition">
                                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                                Delete
                                            </button>
                                        </form>
                                    @endif

                                    {{-- ADMIN: NO ACTIONS --}}
                                    @if ($user->role === 'admin' && $user->status === 'approved')
                                        <span class="text-xs text-gray-400 italic px-2">—</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-12 text-center">
                                <svg class="w-10 h-10 text-gray-300 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                <p class="text-sm text-gray-400">No accounts found for this filter</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- PAGINATION --}}
    {{-- ============================================ --}}
    @if ($users->hasPages())
        <div class="mt-4">
            {{ $users->links() }}
        </div>
    @endif

</div>
@endsection