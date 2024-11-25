<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\CheckoutDetail;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    // This method handles the /view-orders route
    public function viewOrders()
    {
        // Update the order status to 'Returned' for orders whose order_date has passed
        // and where the order_status is NOT 'COMPLETED', 'REJECTED', or 'CANCELLED'
        Order::where('order_status', '!=', 'Returned')  // Ensure we don't update already returned orders
            ->whereNotIn('order_status', ['COMPLETED', 'REJECTED', 'CANCELLED'])  // Exclude orders with these statuses
            ->where('order_date', '<', now())  // Compare order_date with the current date and time
            ->update(['order_status' => 'Returned']);  // Set status to 'Returned'

        // Fetch orders with their associated checkout details
        $orders = Order::with('checkout')
            ->orderBy('order_date', 'desc')  // Order by 'order_date' in descending order
            ->paginate(6);  // 5 orders per page

        // Return the view with fetched data
        return view('staff.contents.order-management', compact('orders'));
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
    


    public function updateOrderStatus(Request $request, $orderId)
    {
        // Log the incoming request for debugging
        Log::info("Update order status request received. Order ID: $orderId, Status: " . $request->input('order_status'));

        // Find the order
        $order = Order::find($orderId);

        if (!$order) {
            Log::error("Order with ID $orderId not found.");
            return response()->json(['success' => false, 'error' => 'Order not found'], 404);
        }

        // Update the order status
        $order->order_status = $request->input('order_status');
        if ($order->save()) {
            Log::info("Order status updated successfully. Order ID: $orderId, New Status: " . $order->order_status);
            return response()->json(['success' => true]);
        } else {
            Log::error("Failed to update order status for Order ID: $orderId.");
            return response()->json(['success' => false, 'error' => 'Failed to update order status'], 500);
        }
    }

    public function changeOrderStatus(Request $request)
    {
        Log::info("Edit order status request received. Order ID: " . $request->input('order_id') . ", Status: " . $request->input('order_status'));

        $order = Order::find($request->input('order_id'));

        if (!$order) {
            Log::error("Order with ID " . $request->input('order_id') . " not found.");
            return response()->json(['success' => false, 'error' => 'Order not found'], 404);
        }

        $order->order_status = $request->input('order_status');
        if ($order->save()) {
            Log::info("Order status updated successfully. Order ID: " . $request->input('order_id') . ", New Status: " . $order->order_status);
            return response()->json(['success' => true]);
        } else {
            Log::error("Failed to update order status for Order ID: " . $request->input('order_id'));
            return response()->json(['success' => false, 'error' => 'Failed to update order status'], 500);
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
        // Initialize the lastChecked variable with the current time minus 10 minutes.
        $this->lastChecked = now()->subMinutes(10);
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
