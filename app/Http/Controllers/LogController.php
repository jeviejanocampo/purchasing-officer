<?php

namespace App\Http\Controllers;

use App\Models\Log; // Import your Log model
use Illuminate\Http\Request;
use App\Models\Order;

class LogController extends Controller
{
    public function PurchasingOfficerLogs()
    {
        // Get the user ID from the session
        $userId = session('user_id');

        // Fetch logs where the role is 'PurchasingOfficer' and log_data contains the user_id
        $logs = Log::where('role', 'PurchasingOfficer') // Filter by role 'PurchasingOfficer'
                    ->where('log_data', 'like', '%"user_id":'.$userId.'%') // Filter logs by user_id within log_data
                    ->orderBy('created_at', 'desc') // Order by created_at in descending order
                    ->get();

        // Return the view with the logs
        return view('log.po-logs', compact('logs'));
    }

    public function StaffOfficerLogs()
    {
        // Fetch logs where the role is 'staff'
        $logs = Log::where('role', 'staff') // Only fetch logs where role is 'staff'
                    ->orderBy('created_at', 'desc') // Order by created_at in descending order
                    ->get();
    
        // Return the view with the logs
        return view('log.staff-logs', compact('logs'));
    }

    public function __construct()
    {
        $pendingOrdersCount = Order::where('order_status', 'Pending')->count();
        view()->share('pendingOrdersCount', $pendingOrdersCount);
    }
    

}
    