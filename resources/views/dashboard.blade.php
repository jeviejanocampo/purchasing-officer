{{-- resources/views/dashboard.blade.php --}}
@extends('main.main')

@section('content')
    <div class="p-6 bg-white rounded-lg shadow-md">
        <h2 class="text-2xl font-semibold text-gray-800 mb-4">Dashboard</h2>
        <p>Welcome to the Admin Dashboard. Here you can manage your settings, view statistics, and more.</p>

        @include('dashboard-components.cards', [
            'inventoryCount' => $inventoryCount,
            'totalBudgetAllocated' => $totalBudgetAllocated,
        ]) 
    </div>
@endsection
