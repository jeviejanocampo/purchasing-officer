<?php

namespace App\Http\Controllers;

use App\Models\Log; // Import your Log model
use Illuminate\Http\Request;

class LogController extends Controller
{
    public function index()
    {
        // Get the user ID from the session
        $userId = session('user_id');

        // Fetch logs where the log_data JSON contains the user_id and order by created_at descending
        $logs = Log::where('log_data', 'like', '%"user_id":'.$userId.'%')
                    ->orderBy('created_at', 'desc') // Order by created_at in descending order
                    ->get();

        // Return the view with the logs
        return view('log.po-logs', compact('logs'));
    }
}
    