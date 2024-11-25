<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js"></script>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <style>
        body {
            font-size: 14px;
        }

        .hidden {
            display: none;
        }

        .stocks-low-animation {
            animation: beat 0.5s infinite; /* Infinite beat animation */
        }

        @keyframes beat {
            0% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.2); /* Scale up */
            }
            100% {
                transform: scale(1); /* Scale back to original size */
            }
        }
    </style>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="bg-gray-100" style="font-family: 'Lato', sans-serif;">

@extends('staff.home.staff-main')

@section('content')

<h1 class="text-2xl font-bold text-gray-800 mb-4">Orders</h1>
@if(session('user_id'))
    <p class="hidden" id="user-id">Your User ID: {{ session('user_id') }}</p>
@else
    <p class="hidden">You are not logged in.</p>
@endif

<div class="mb-4">
    <label for="section-selector" class="block text-sm font-medium text-gray-700">Select Section</label>
    <select id="section-selector" class="block w-full border border-gray-300 rounded-md p-2 shadow-sm focus:ring focus:ring-indigo-200" onchange="toggleSections()">
        <optgroup label="Orders">
            <option value="view-orders">View Orders</option>
            <option value="view-staff">View Staff</option>
        </optgroup>
    </select>
</div>

@include('staff.staff-contents.view-orders')


<script src="{{ asset('js/selection.js') }}"></script>


@include('components.budget-modal')

@endsection

</body>
</html>
