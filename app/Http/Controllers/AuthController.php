<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\Auth; 
use App\Models\Order;
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
    
            // Create the new user in the database without hashing the password
            User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => $request->password, // Store password in plain text
                'pin' => $request->pin,
                'role' => 'user', // Set default role, adjust if necessary
            ]);
    
            // Redirect back to the same form with a success message
            return redirect()->route('register')->with('success', 'Registration successful! You can now log in.');
        }
    
        // If the request is a GET request, return the registration form view
        return view('view-orders');
    }
    

    public function pinLogin(Request $request)
    {
        $request->validate([
            'pin' => 'required|digits:5',
        ]);

        // Find the user with the provided PIN
        $user = User::where('pin', $request->pin)->first();

        if ($user) {
            // Check if the user's status is 'CONFIRMED'
            if ($user->users_status === 'CONFIRMED') {
                // Check if the user's role is 'purchasing-officer'
                if ($user->role === 'purchasing-officer') {
                    // Log the user in
                    Auth::login($user);

                    // Store the user ID in the session
                    session(['user_id' => $user->id]);

                    // Log the successful login action
                    Log::info('User logged in via PIN:', ['user_id' => $user->id]);

                    // Return a JSON response for successful login
                    return response()->json(['success' => true, 'redirect' => route('dashboard')]);
                } else {
                    // If the user's role is not 'purchasing-officer'
                    return response()->json(['success' => false, 'message' => 'Access denied. You do not have the required role.']);
                }
            } else {
                // If the user's status is not confirmed
                return response()->json(['success' => false, 'message' => 'Please wait for confirmation first or contact the admin at 094532322/3232332.']);
            }
        }

        // If the PIN is incorrect
        return response()->json(['success' => false, 'message' => 'Invalid PIN! Please try again.']);
    }

    public function logout(Request $request)
    {
        // Perform logout logic, such as invalidating the session
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login.view'); // Redirect to login view
    }

    public function staffSignup(Request $request)
    {
        // Validate form data
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'pin' => 'required|digits:5|unique:users',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()], 400);
        }

        // Create a new user with role 'staff'
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'pin' => $request->pin,
            'role' => 'staff', // Set role as 'staff'
            'users_status' => 'PENDING' // Example default status
        ]);

        return response()->json(['success' => true, 'message' => 'Staff registered successfully.']);
    }

    public function loginWithPin(Request $request)
    {
        // Validate the PIN
        $request->validate([
            'pin' => 'required|digits:5',
        ]);

        // Authenticate the user by PIN (assuming PIN is stored in the database)
        $user = User::where('pin', $request->pin)->first();

        // Check if user exists
        if ($user) {
            // Check if the user role is 'staff'
            if ($user->role === 'staff') {
                // Log in the user
                Auth::login($user);

                // Update the users_attendance to 'LOGGED IN'
                $user->users_attendance = 'LOGGED IN';
                $user->save(); // Save the change

                // Store the user ID in the session
                session(['user_id' => $user->id]);

                // Redirect to staff home page
                return response()->json(['success' => true, 'redirect' => route('view-orders')]);
            } else {
                // If the user has a different role, return an error
                return response()->json(['success' => false, 'message' => 'Access denied. This account is not a staff member.']);
            }
        }

        // If user is not found, suggest contacting admin for PIN issues
        return response()->json([
            'success' => false,
            'message' => 'Invalid PIN. If you forgot your PIN, please contact the admin. Provide your name and a screenshot of you, then send it to adminmstiniofoodstradingco@gmail.com.'
        ]);
    }
    

    public function Stafflogout(Request $request)
    {
        // Get the currently logged-in user
        $user = Auth::user();

        // Ensure that $user is an instance of the User model
        if ($user instanceof User) {
            // Set the 'users_attendance' value to 'LOGGED OUT'
            $user->users_attendance = 'LOGGED OUT';
            
            // Save the changes
            $user->save();
        }

        // Perform logout logic
        Auth::logout(); // Log the user out

        // Invalidate the session and regenerate the token
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Redirect to the staff login page
        return redirect()->route('staff.login'); // Redirect to login page
    }

    
    
    public function emailLogin(Request $request)
    {
        // Validate the incoming request data
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        // Attempt to find the user with the given email
        $user = User::where('email', $request->email)->first();

        if ($user && Hash::check($request->password, $user->password)) {
            // Check if the user's status is 'CONFIRMED'
            if ($user->users_status === 'CONFIRMED') {
                // Check if the user's role is 'purchasing-officer'
                if ($user->role === 'purchasing-officer') {
                    // Log the user in
                    Auth::login($user);

                    // Store the user ID in the session
                    session(['user_id' => $user->id]);

                    // Return a successful response with redirect URL
                    return response()->json(['success' => true, 'redirect' => route('dashboard')]);
                } else {
                    // If the user's role is not 'purchasing-officer'
                    return response()->json(['success' => false, 'message' => 'Access denied. You do not have the required role.']);
                }
            } else {
                // If the user's status is not confirmed
                return response()->json(['success' => false, 'message' => 'Please wait for confirmation or contact the admin.']);
            }
        }

        // If the credentials are incorrect
        return response()->json(['success' => false, 'message' => 'Invalid credentials. Please try again.']);
    }



    public function resetPassword(Request $request)
    {
        // Validate the incoming request data
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email', // Ensure the email exists in the users table
            'new_password' => 'required|string|min:8|confirmed', // Validate password strength and confirm password
        ]);
    
        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()], 400);
        }
    
        // Find the user by email
        $user = User::where('email', $request->email)->first();
    
        // If the user exists, update the password
        if ($user) {
            // Store the password as plain text (not hashed)
            $user->password = $request->new_password;
            $user->save();
    
            // Log the action (for monitoring purposes)
            Log::info('Password reset successfully for user:', ['user_id' => $user->id]);
    
            // Return success response
            return response()->json(['success' => true, 'message' => 'Password reset successfully.']);
        }
    
        // If user not found
        return response()->json(['success' => false, 'message' => 'Email not found.']);
    }
    
    
    

}
