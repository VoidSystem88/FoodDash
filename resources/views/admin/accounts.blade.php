@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">

    {{-- ============================================ --}}
    {{-- PAGE HEADER --}}
    {{-- ============================================ --}}
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-neutral-900 dark:text-white">Manage Accounts</h1>
            <p class="text-sm text-neutral-500 dark:text-neutral-400 mt-1">Approve, disable, or remove user accounts</p>
        </div>

        <a href="{{ route('admin.dashboard') }}"
           class="inline-flex items-center gap-2 text-sm text-neutral-600 dark:text-neutral-400 hover:text-orange-600 dark:hover:text-orange-400 font-medium transition">
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
        <div class="mb-4 p-3 bg-gradient-to-r from-orange-50 to-orange-100/50 dark:from-orange-950/30 dark:to-orange-900/10 border-l-4 border-orange-500 rounded-xl text-sm flex items-center gap-2">
            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-orange-500 to-orange-600 flex items-center justify-center flex-shrink-0 shadow-md">
                <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                </svg>
            </div>
            <span class="text-neutral-800 dark:text-neutral-200 font-medium">{{ session('success') }}</span>
        </div>
    @endif

    @if (session('error'))
        <div class="mb-4 p-3 bg-black dark:bg-neutral-900 border-l-4 border-orange-500 rounded-xl text-sm flex items-center gap-2">
            <div class="w-8 h-8 rounded-full bg-orange-500 flex items-center justify-center flex-shrink-0">
                <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
            <span class="text-white font-medium">{{ session('error') }}</span>
        </div>
    @endif

    {{-- ============================================ --}}
    {{-- FILTER TABS --}}
    {{-- ============================================ --}}
    <div class="bg-white dark:bg-[#141414] rounded-xl border border-neutral-200 dark:border-[#262626] p-2 mb-4 overflow-x-auto">
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
                   class="px-3.5 py-2 rounded-lg text-sm font-semibold whitespace-nowrap transition
                          {{ $filter === $key
                                ? 'bg-gradient-to-r from-orange-500 to-orange-600 text-white shadow-md'
                                : 'text-neutral-600 dark:text-neutral-400 hover:bg-neutral-100 dark:hover:bg-[#262626] hover:text-neutral-900 dark:hover:text-white' }}">
                    {{ $label }}
                    <span class="ml-1 text-xs {{ $filter === $key ? 'text-white/80' : 'text-neutral-400 dark:text-neutral-500' }}">
                        ({{ $counts[$key] }})
                    </span>
                </a>
            @endforeach
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- ACCOUNTS TABLE --}}
    {{-- ============================================ --}}
    <div class="bg-white dark:bg-[#141414] rounded-xl border border-neutral-200 dark:border-[#262626] overflow-hidden">

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-neutral-50 dark:bg-[#0a0a0a] border-b border-neutral-100 dark:border-[#262626]">
                    <tr>
                        <th class="text-left px-5 py-3 font-semibold text-neutral-600 dark:text-neutral-400 text-xs uppercase tracking-wide">Name</th>
                        <th class="text-left px-5 py-3 font-semibold text-neutral-600 dark:text-neutral-400 text-xs uppercase tracking-wide">Email</th>
                        <th class="text-left px-5 py-3 font-semibold text-neutral-600 dark:text-neutral-400 text-xs uppercase tracking-wide">Role</th>
                        <th class="text-left px-5 py-3 font-semibold text-neutral-600 dark:text-neutral-400 text-xs uppercase tracking-wide">Status</th>
                        <th class="text-left px-5 py-3 font-semibold text-neutral-600 dark:text-neutral-400 text-xs uppercase tracking-wide">Registered</th>
                        <th class="text-right px-5 py-3 font-semibold text-neutral-600 dark:text-neutral-400 text-xs uppercase tracking-wide">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-100 dark:divide-[#262626]">
                    @forelse ($users as $user)
                        <tr class="hover:bg-orange-50/50 dark:hover:bg-orange-950/10 transition">

                            {{-- NAME + PHONE --}}
                            <td class="px-5 py-3">
                                <div class="font-semibold text-neutral-900 dark:text-white">{{ $user->name }}</div>
                                @if ($user->phone)
                                    <div class="text-xs text-neutral-500 dark:text-neutral-400 mt-0.5">{{ $user->phone }}</div>
                                @endif
                            </td>

                            {{-- EMAIL --}}
                            <td class="px-5 py-3 text-neutral-600 dark:text-neutral-400">{{ $user->email }}</td>

                            {{-- ROLE BADGE --}}
                            <td class="px-5 py-3">
                                @php
                                    $roleStyles = [
                                        'admin' => 'bg-orange-100 dark:bg-orange-950/40 text-orange-700 dark:text-orange-400 border-orange-200 dark:border-orange-800',
                                        'restaurant' => 'bg-orange-50 dark:bg-orange-950/30 text-orange-600 dark:text-orange-400 border-orange-200 dark:border-orange-800',
                                        'rider' => 'bg-orange-50 dark:bg-orange-950/30 text-orange-600 dark:text-orange-400 border-orange-200 dark:border-orange-800',
                                        'customer' => 'bg-neutral-100 dark:bg-[#262626] text-neutral-700 dark:text-neutral-300 border-neutral-200 dark:border-[#262626]',
                                    ];
                                    $roleClass = $roleStyles[$user->role] ?? 'bg-neutral-100 dark:bg-[#262626] text-neutral-700 dark:text-neutral-300 border-neutral-200 dark:border-[#262626]';
                                @endphp
                                <span class="inline-block text-xs px-2 py-0.5 rounded-full border font-bold uppercase tracking-wide {{ $roleClass }}">
                                    {{ ucfirst($user->role) }}
                                </span>
                            </td>

                            {{-- STATUS BADGE --}}
                            <td class="px-5 py-3">
                                @php
                                    $statusStyles = [
                                        'approved' => 'bg-orange-50 dark:bg-orange-950/30 text-orange-700 dark:text-orange-400 border-orange-200 dark:border-orange-800',
                                        'pending' => 'bg-amber-50 dark:bg-amber-950/30 text-amber-700 dark:text-amber-400 border-amber-200 dark:border-amber-800',
                                        'disabled' => 'bg-neutral-100 dark:bg-[#262626] text-neutral-700 dark:text-neutral-300 border-neutral-200 dark:border-[#262626]',
                                    ];
                                    $statusClass = $statusStyles[$user->status] ?? 'bg-neutral-100 dark:bg-[#262626] text-neutral-700 dark:text-neutral-300 border-neutral-200 dark:border-[#262626]';

                                    $statusDot = [
                                        'approved' => 'bg-orange-500',
                                        'pending' => 'bg-amber-500',
                                        'disabled' => 'bg-neutral-400',
                                    ];
                                    $dotClass = $statusDot[$user->status] ?? 'bg-neutral-400';
                                @endphp
                                <span class="inline-flex items-center gap-1.5 text-xs px-2 py-0.5 rounded-full border font-bold uppercase tracking-wide {{ $statusClass }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $dotClass }}"></span>
                                    {{ ucfirst($user->status) }}
                                </span>
                            </td>

                            {{-- REGISTERED DATE --}}
                            <td class="px-5 py-3 text-xs text-neutral-500 dark:text-neutral-400">
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
                                                    class="inline-flex items-center gap-1 text-xs font-bold text-white bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 px-2.5 py-1.5 rounded-lg transition shadow-sm active:scale-95">
                                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
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
                                                    class="inline-flex items-center gap-1 text-xs font-bold text-amber-700 dark:text-amber-400 bg-amber-50 dark:bg-amber-950/30 hover:bg-amber-100 dark:hover:bg-amber-950/50 border border-amber-200 dark:border-amber-800 px-2.5 py-1.5 rounded-lg transition">
                                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
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
                                                    class="inline-flex items-center gap-1 text-xs font-bold text-red-700 dark:text-red-400 bg-red-50 dark:bg-red-950/30 hover:bg-red-100 dark:hover:bg-red-950/50 border border-red-200 dark:border-red-800 px-2.5 py-1.5 rounded-lg transition">
                                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                                Delete
                                            </button>
                                        </form>
                                    @endif

                                    {{-- ADMIN: NO ACTIONS --}}
                                    @if ($user->role === 'admin' && $user->status === 'approved')
                                        <span class="text-xs text-neutral-400 dark:text-neutral-500 italic px-2">—</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-12 text-center">
                                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-neutral-100 dark:bg-[#262626] mb-3">
                                    <svg class="w-8 h-8 text-neutral-400 dark:text-neutral-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                              d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                </div>
                                <p class="text-sm text-neutral-500 dark:text-neutral-400 font-medium">No accounts found for this filter</p>
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