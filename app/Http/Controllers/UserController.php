<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

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
        try {

            $validatedData = $request->validate([
                'full_name' => 'required|string',
                'phone_number' => 'required|unique:users,phone_number|string',
                'pin' => 'required|string',
            ]);


            $user = User::create([
                'full_name' => $validatedData['full_name'],
                'phone_number' => $validatedData['phone_number'],
                'pin' => $validatedData['pin']
            ]);
            
            return response()->json(['message' => 'User created successfully', 'user' => $user, 'account number' => $user->id]);
            
        } catch (ValidationException $e) {
            // If validation fails, return validation errors
            return response()->json(['error' => $e->validator->errors()], 422);
        } catch (Exception $e) {
            // Log the exception details
            Log::error('User creation failed: ' . $e->getMessage());
            // If any other exception occurs, return a generic error message
            return response()->json(['error' => 'Failed to create user.'], 500);
        }
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
        try{
            // Retrieve and return user by ID
        $user = User::findOrFail($id);

        return response()->json(['user' => $user]);
        } catch (ModelNotFoundException $e) {
            // Log the exception details
            Log::error('User not found: ' . $e->getMessage());
            // Return a specific error message for user not found
            return response()->json(['error' => 'User not found.'], 404);
        } catch (Exception $e) {
            // Log the exception details
            Log::error('Fetch User failed: ' . $e->getMessage());
            // If any other exception occurs, return a generic error message
            return response()->json(['error' => 'Failed to access Database.'], 500);
        }
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
        try {
            // Validate and update user data
            $validatedData = $request->validate([
                'full_name' => 'required|string',
                'phone_number' => 'required|string',
            ]);

            $user = User::findOrFail($id);
            $user->update($validatedData);

            return response()->json(['message' => 'User updated successfully', 'user' => $user]);
        } catch(ValidationException $e) {
            // If validation fails, return validation errors
            return response()->json(['error' => $e->validator->errors()], 422);
        } catch(Exception $e) {
            // Log the exception details
            Log::error('Update User failed: ' . $e->getMessage());
            // If any other exception occurs, return a generic error message
            return response()->json(['error' => 'Failed to Update User on Database.'], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {
        try {
            // Delete user by ID
            $user = User::findOrFail($id);
            $user->delete();

            return response()->json(['message' => 'User deleted successfully']);
        } catch(ValidationException $e) {
            // If validation fails, return validation errors
            return response()->json(['error' => $e->validator->errors()], 422);
        } catch(Exception $e) {
            // Log the exception details
            Log::error('Delete User failed: ' . $e->getMessage());
            // If any other exception occurs, return a generic error message
            return response()->json(['error' => 'Failed to Delete User from Database.'], 500);
        }
    }
}
