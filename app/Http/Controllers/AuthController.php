<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\Auth; 
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    /**
     * Show the registration form or handle registration request.
     */
    public function register(Request $request)
    {
        // If the request is a POST request, handle registration
        if ($request->isMethod('post')) {
            // Validate the incoming request data
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8',
                'pin' => 'required|digits:5|unique:users',
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            // Create the new user in the database
            User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password), // Ensure password is hashed
                'pin' => $request->pin,
                'role' => 'user', // Set default role, adjust if necessary
            ]);

            // Redirect back to the same form with a success message
            return redirect()->route('register')->with('success', 'Registration successful! You can now log in.');
        }

        // If the request is a GET request, return the registration form view
        return view('po-login.po-login');
    }

    public function pinLogin(Request $request)
    {
        $request->validate([
            'pin' => 'required|digits:5',
        ]);

        // Find the user with the provided PIN
        $user = User::where('pin', $request->pin)->first();

        if ($user) {
            // Check if the user's status is confirmed
            if ($user->users_status === 'CONFIRMED') {
                // Log the user in
                Auth::login($user);

                // Store the user ID in the session
                session(['user_id' => $user->id]);

                // Log the successful login action
                Log::info('User logged in:', ['user_id' => $user->id]);

                // Return a JSON response for successful login
                return response()->json(['success' => true, 'redirect' => route('dashboard')]);
            } else {
                // If the user's status is not confirmed, return an error response
                return response()->json(['success' => false, 'message' => 'Please wait for confirmation first or contact the admin with contact number 094532322/3232332.']);
            }
        }

        // If the PIN is incorrect, return an error response
        return response()->json(['success' => false, 'message' => 'Invalid PIN! Please try again.']);
    }

    public function logout(Request $request)
    {
        // Perform logout logic, such as invalidating the session
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login.view'); // Redirect to login view
    }


    
}
