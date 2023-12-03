<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function register(Request $request)
    {

        // Generate a unique account number
        $accountNumber = User::generateAccountNumber();

        // Check if the account number already exists
        $accountExists = Account::where('account_number', $accountNumber)->exists();

        if (!$accountExists) {
            // The account does not exist, handle accordingly
            return response()->json(['error' => 'Account not found'], 404);
        }

        // Create a new account with default values
        $account = Account::create([
            'account_number' => $accountNumber,
            'account_type' => 'generated',
            'account_balance' => 0,
            'status' => 0,
            // Add other default values as needed
        ]);

        Log::info("Generated Account Number: $accountNumber");

        
        // Validate and store new user data
        $validatedData = $request->validate([
            'full_name' => 'required|string',
            'phone_number' => 'required|unique:users,phone_number|string',
            // 'account_number' => 'required|unique:users,account_number|string',
            'pin' => 'required|string',
            // Add more validation rules as needed
        ]);

        

        $user = User::create([
            'full_name' => $validatedData['full_name'],
            'phone_number' => $validatedData['phone_number'],
            'account_number' => $accountNumber,
            'pin' => $validatedData['pin']
        ]);

        // $user = User::create($validatedData);

        return response()->json(['message' => 'User created successfully', 'user' => $user, 'account number' => $account]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function login(Request $request)
    {
        $request->validate([
            'account_number' => 'required|string',
            'pin' => 'required|string',
        ]);

        $credentials = $request->only('account_number', 'pin');

        if (Auth::attempt($credentials)) {
            // Authentication passed, user is logged in
            return redirect()->intended('/dashboard')->with('success', 'Welcome' . " " . Auth::user()->username); // Redirect to the intended page with success message
        } else {
            // Authentication failed, user credentials are invalid
            return redirect()->back()->withErrors(['Invalid credentials.']); // redirect back with error message
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Retrieve and return user by ID
        $user = User::findOrFail($id);

        return response()->json(['user' => $user]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Validate and update user data
        $validatedData = $request->validate([
            'full_name' => 'required|string',
            'phone_number' => 'required|string',
            'account_number' => 'required|string',
            'pin' => 'required|string',
            // Add more validation rules as needed
        ]);

        $user = User::findOrFail($id);
        $user->update($validatedData);

        return response()->json(['message' => 'User updated successfully', 'user' => $user]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {
        // Delete user by ID
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json(['message' => 'User deleted successfully']);
    }
}
