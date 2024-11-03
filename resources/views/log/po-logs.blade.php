<!-- C:\xampp\htdocs\purchasing-officer\resources\views\log\po-logs.blade.php -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PO Logs</title>
    <!-- Tailwind CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
@extends('main.main') 

@section('content')
    <div class="container mx-auto p-5">
        <h1 class="text-3xl font-bold mb-4">Purchase Officer Logs</h1>
        
        @if(session('user_id'))
            <p class="hidden" id="user-id">Your User ID: {{ session('user_id') }}</p>
        @else
            <p class="hidden">You are not logged in.</p>
        @endif  

        <table class="min-w-full bg-white border border-gray-300 rounded-lg shadow-md">
            <thead>
                <tr class="bg-gray-200 text-gray-600 uppercase text-sm">
                    <th class="p-4 border-b">ID</th>
                    <th class="p-4 border-b">Log Data</th>
                    <th class="p-4 border-b">Created At</th>
                    <th class="p-4 border-b">Updated At</th>
                </tr>
            </thead>
            <tbody>
                @foreach($logs as $log)
                    <tr class="hover:bg-gray-100">
                        <td class="p-4 border-b text-center">{{ $log->id }}</td>
                        <td class="p-4 border-b">{{ $log->log_data }}</td>
                        <td class="p-4 border-b text-center">{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                        <td class="p-4 border-b text-center">{{ $log->updated_at->format('Y-m-d H:i:s') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        @if($logs->isEmpty())
            <div class="mt-4 text-center text-gray-500">
                No logs found.
            </div>
        @endif
    </div>
@endsection

</body>
</html>
