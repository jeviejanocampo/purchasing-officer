<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Checkout;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\View;
use App\Models\CheckoutDetail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Log as FacadeLog;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function viewOrders()
    {
        // Update the order status to 'Returned' for orders whose order_date has passed
        // and where the order_status is NOT 'COMPLETED', 'REJECTED', or 'CANCELLED'
        Order::where('order_status', '!=', 'Returned')  // Ensure we don't update already returned orders
            ->whereNotIn('order_status', ['COMPLETED', 'REJECTED', 'CANCELLED'])  // Exclude orders with these statuses
            ->where('order_date', '<', now())  // Compare order_date with the current date and time
            ->update(['order_status' => 'Returned']);  // Set status to 'Returned'
    
        // Automatically set orders older than 3 days to 'Cancelled', excluding 'Completed' and 'Rejected' statuses
        Order::where('order_status', '!=', 'Cancelled')  // Ensure we don't update already cancelled orders
            ->whereNotIn('order_status', ['COMPLETED', 'REJECTED'])  // Exclude 'Completed' and 'Rejected' statuses
            ->where('order_date', '<', now()->subDays(3))  // Check if the order_date is older than 3 days
            ->update(['order_status' => 'Cancelled']);  // Set status to 'Cancelled'
    
        // Fetch orders with their associated checkout details
        $orders = Order::with('checkout')
            ->orderBy('order_date', 'desc')  // Order by 'order_date' in descending order
            ->paginate(15);  // 15 orders per page
    
        // Fetch active customers
        $users = DB::table('user')
            ->select('user_id', 'user_fullname', 'user_email', 'user_number', 'user_bdate', 'created_at')
            ->get();
    
        // Count Pending orders
        $pendingOrdersCount = Order::where('order_status', 'Pending')->count();
    
        // Return the view with fetched data and pending count
        return view('staff.contents.order-management', compact('orders', 'users', 'pendingOrdersCount'));
    }

    public function checkForPendingOrders()
    {
        // Log when the request is made
        Log::info('AJAX request received to check for new PENDING orders.');

        // Check for orders created in the last 30 seconds with PENDING status
        $recentPendingOrders = Order::where('order_status', 'PENDING')
            ->where('created_at', '>=', now()->subSeconds(30))
            ->get();

        // Log the number of new PENDING orders
        Log::info('Number of new PENDING orders: ' . $recentPendingOrders->count());

        // Return a JSON response with the result
        return response()->json([
            'newPendingOrders' => $recentPendingOrders->count() > 0
        ]);
    }


    


    public function updateCheckoutStatuses()
    {
        // Update the 'status' in the 'checkout_details' table to 'Cancelled' for records older than 3 days
        // and where the 'status' is not already 'Cancelled'
        DB::table('checkout_details')
            ->where('status', '!=', 'Cancelled')  // Ensure we don't update already cancelled records
            ->where('created_at', '<', now()->subDays(3))  // Check if 'created_at' is older than 3 days
            ->update(['status' => 'Cancelled']);  // Set 'status' to 'Cancelled'

        // Update the 'checkout_status' in the 'checkout' table to 'Cancelled' for records older than 3 days
        // and where the 'checkout_status' is not already 'Cancelled'
        DB::table('checkout')
            ->where('checkout_status', '!=', 'Cancelled')  // Ensure we don't update already cancelled records
            ->where('created_at', '<', now()->subDays(3))  // Check if 'created_at' is older than 3 days
            ->update(['checkout_status' => 'Cancelled']);  // Set 'checkout_status' to 'Cancelled'

        // Optionally, you can return some feedback or logs
        return response()->json(['message' => 'Checkout statuses updated successfully']);
    }


    

   
    public function overview()
    {
        $today = Carbon::today();

        $ordersToday = Order::whereDate('created_at', $today)->count();

        $pendingOrders = Order::where('order_status', 'Pending')->count();

        $deliveredOrders = Order::where('order_status', 'Delivered')->count();

        $cancelledOrders = Order::where('order_status', 'Cancelled')->count();

        return response()->json([
            'orders_today' => $ordersToday,
            'pending_orders' => $pendingOrders,
            'delivered_orders' => $deliveredOrders,
            'cancelled_orders' => $cancelledOrders,
        ]);
    }



    // This method handles the /orders route
    public function index()
    {
        // Fetch orders with their associated checkout details
        $orders = Order::with('checkout')->get();

        // Return the view with fetched data
        return view('view-orders', compact('orders'));
    }

    public function showOrderDetailsPage($checkout_id)
    {
        // Fetch the checkout details and associated product details
        $checkoutDetails = DB::table('checkout_details')
            ->join('products', 'checkout_details.product_id', '=', 'products.product_id')
            ->where('checkout_details.checkout_id', $checkout_id)
            ->get([
                'checkout_details.checkout_details_id',
                'checkout_details.checkout_id',
                'checkout_details.product_id',
                'checkout_details.cart_id',
                'checkout_details.product_quantity',
                'checkout_details.product_price',
                'checkout_details.product_image',
                'checkout_details.created_at',
                'products.product_name',
                'products.product_description',
                'products.product_stocks',
                'products.product_expiry_date',
                'products.product_image',
                'products.product_status'
            ]);
    
        // If no checkout details found, return an error message
        if ($checkoutDetails->isEmpty()) {
            return view('staff.staff-contents.view-order-details', ['message' => 'No checkout details found']);
        }
    
        // Fetch the checkout record to get user_id
        $checkout = DB::table('checkout')->where('checkout_id', $checkout_id)->first();
    
        if (!$checkout) {
            return view('staff.staff-contents.view-order-details', ['message' => 'Checkout not found']);
        }
    
        // Fetch the user based on user_id from the checkout table
        $user = DB::table('user')->where('user_id', $checkout->user_id)->first();
    
        if (!$user) {
            return view('staff.staff-contents.view-order-details', ['message' => 'User not found']);
        }
    
        // Fetch the delivery address where d_status is 'DELIVERY ADDRESS SAVED'
        $deliveryAddress = DB::table('delivery_address')
            ->where('user_id', $checkout->user_id)
            ->where('d_status', 'DELIVERY ADDRESS SAVED') // Filter for the desired status
            ->first();
    
        // If no delivery address found, return an error message
        if (!$deliveryAddress) {
            return view('staff.staff-contents.view-order-details', ['message' => 'Delivery address not found or status not valid']);
        }
    
        // Add the full URL for product images
        $checkoutDetails = $checkoutDetails->map(function ($detail) {
            $detail->product_image = asset('storage/product-images/' . $detail->product_image);
            return $detail;
        });
    
        // Pass the checkout_id as order_id along with other data to the view
        return view('staff.staff-contents.view-order-details', [
            'order_id' => $checkout_id, // Pass the selected order ID
            'checkoutDetails' => $checkoutDetails,
            'user' => $user,
            'deliveryAddress' => $deliveryAddress
        ]);
    }

    private function logAction($message, $data)
    {
        // Get the staff information from session
        $userId = session('user_id');
        $user = \App\Models\User::find($userId);
    
        // Default values if no user is found
        $staffName = $user ? $user->name : 'Guest';
        $staffId = $user ? $user->id : 'N/A';
        $role = $user ? $user->role : 'staff'; // Default to 'staff' if no user is found
    
        // Combine the message with additional data
        $logData = json_encode(array_merge([
            'message' => $message,
            'staff_id' => $staffId,
            'staff_name' => $staffName,
            'role' => $role, // Include the role in the log data
        ], $data)); // Convert to JSON format
    
        // Insert into the logs table with the role
        \App\Models\Log::create([
            'log_data' => $logData,
            'role' => $role // Insert the staff role into the 'role' column
        ]);
    
        // Optional: Log the message and data to Laravel's log files
        Log::info($message, $data);
    }
    
    

    
    public function updateOrderStatus(Request $request, $orderId)
    {
        // Get the staff information from session
        $userId = session('user_id');
        $user = \App\Models\User::find($userId);
        $staffName = $user ? $user->name : 'Guest'; // Default to 'Guest' if no user is found
        $staffId = $user ? $user->id : 'N/A'; // Default to 'N/A' if no user is found

        // Validate order status input
        $request->validate([
            'order_status' => 'required|string',
        ]);

        // Find the order
        $order = Order::find($orderId);
        if (!$order) {
            // Log order not found
            $this->logAction('Order not found', [
                'order_id' => $orderId,
                'staff_id' => $staffId,
                'staff_name' => $staffName,
                'role' => 'staff', // Default role for the staff
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Order not found',
            ], 404);
        }

        // Find associated checkout
        $checkout = $order->checkout;

        // Start a database transaction
        DB::beginTransaction();

        try {
            // Update the order status
            $order->order_status = $request->input('order_status');
            $order->save();

            // Sync with the checkout status if checkout exists
            if ($checkout) {
                $checkout->checkout_status = $request->input('order_status');
                $checkout->save();

                // Update all associated checkout details
                $checkoutDetails = $checkout->checkoutDetails; // Use relationship to fetch details
                foreach ($checkoutDetails as $detail) {
                    $detail->status = $request->input('order_status');
                    $detail->save();
                }
            }

            // Commit transaction
            DB::commit();

            // Log successful update
            $this->logAction('Order, Checkout, and CheckoutDetails statuses updated successfully', [
                'order_id' => $orderId,
                'checkout_id' => $checkout ? $checkout->checkout_id : null,
                'new_status' => $order->order_status,
                'staff_id' => $staffId,
                'staff_name' => $staffName,
                'role' => 'staff', // Default role for the staff
            ]);

            return response()->json(['success' => true, 'message' => 'Order, Checkout, and CheckoutDetails statuses updated.']);
        } catch (\Exception $e) {
            // Rollback transaction on error
            DB::rollBack();

            // Log failure
            $this->logAction('Failed to update statuses', [
                'order_id' => $orderId,
                'error' => $e->getMessage(),
                'staff_id' => $staffId,
                'staff_name' => $staffName,
                'role' => 'staff',
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Failed to update statuses.',
            ], 500);
        }
    }


    public function changeOrderStatus(Request $request)
    {
        // Validate the input
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,order_id',
            'order_status' => 'required|string',
        ]);

        // Retrieve user information
        $userId = session('user_id');
        $user = \App\Models\User::find($userId);
        $staffName = $user ? $user->name : 'Guest';
        $staffId = $user ? $user->id : 'N/A';
        $role = $user ? $user->role : 'staff';

        // Find the order and its associated checkout
        $order = Order::find($validated['order_id']);
        if (!$order) {
            $this->logAction('Order not found', ['order_id' => $validated['order_id'], 'staff_id' => $staffId]);
            return response()->json(['success' => false, 'error' => 'Order not found'], 404);
        }

        $checkout = $order->checkout; // Use relationship to fetch associated checkout

        // Start a database transaction to ensure all updates succeed
        DB::beginTransaction();

        try {
            // Update the order status
            $order->order_status = $validated['order_status'];
            $order->save();

            // Sync with the checkout status if checkout exists
            if ($checkout) {
                $checkout->checkout_status = $validated['order_status'];
                $checkout->save();

                // Update all associated checkout details
                $checkoutDetails = $checkout->checkoutDetails; // Use relationship to fetch details
                foreach ($checkoutDetails as $detail) {
                    $detail->status = $validated['order_status'];
                    $detail->save();
                }
            }

            // Commit transaction
            DB::commit();

            // Log successful update
            $this->logAction('Order, Checkout, and CheckoutDetails statuses updated successfully', [
                'order_id' => $order->order_id,
                'checkout_id' => $checkout ? $checkout->checkout_id : null,
                'order_status' => $order->order_status,
                'staff_id' => $staffId,
                'staff_name' => $staffName,
                'role' => $role,
            ]);

            return response()->json(['success' => true, 'message' => 'Order, Checkout, and CheckoutDetails statuses updated.']);
        } catch (\Exception $e) {
            // Rollback transaction on error
            DB::rollBack();

            // Log the error
            $this->logAction('Failed to update statuses', [
                'order_id' => $order->order_id,
                'error' => $e->getMessage(),
                'staff_id' => $staffId,
                'staff_name' => $staffName,
                'role' => $role,
            ]);

            return response()->json(['success' => false, 'error' => 'Failed to update statuses.'], 500);
        }
    }

    

    



    public function getOrderStatus($orderId)
    {
        // Find the order by ID
        $order = Order::find($orderId);

        if (!$order) {
            return response()->json(['success' => false, 'message' => 'Order not found'], 404);
        }

        // Return the order status
        return response()->json([
            'success' => true,
            'order_status' => $order->order_status,
        ]);
    }

    private $lastChecked;

    public function __construct()
    {
        $pendingOrdersCount = Order::where('order_status', 'Pending')->count();
        view()->share('pendingOrdersCount', $pendingOrdersCount);
    }

    public function getPendingOrders(Request $request)
    {
        // Get the current time, and subtract 5 minutes to get the time window
        $fiveMinutesAgo = now()->subMinutes(5);

        Log::info('Checking for new pending orders...', ['last_checked' => $fiveMinutesAgo]);

        // Fetch new orders that are 'PENDING' and created in the last 5 minutes
        $newOrders = Order::where('order_status', 'PENDING')
            ->where('created_at', '>', $fiveMinutesAgo)
            ->orderBy('created_at', 'asc')
            ->get();

        // If no orders are found in the last 5 minutes, return no data
        if ($newOrders->isEmpty()) {
            Log::info('No new orders in the last 5 minutes.');
            return response()->json([
                'message' => 'No new orders in the last 5 minutes.',
                'orders' => []
            ]);
        }

        // If new orders are found, log and return them
        Log::info("New orders found: {$newOrders->count()}", $newOrders->toArray());

        return response()->json($newOrders);
    }

}
