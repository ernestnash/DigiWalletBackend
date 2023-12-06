<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\User;
use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

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
            
            // Create a new account with default values
            $account = Account::create([
                'account_number' => $user->id,
                'account_type' => 'generated',
                'account_balance' => 0,
                'status' => 'Pending First Transaction',
            ]);
            $account->save();

            return response()->json(['message' => 'User created successfully', 'user' => $user, 'account' => $account]);

        } catch (ValidationException $e) {
            // If validation fails, return validation errors
            return response()->json(['error' => $e->validator->errors()], 422);
        } catch (Exception $e) {
            // Log the exception details
            Log::error('User creation failed: ' . $e->getMessage());
            
            // If account creation fails, delete the created user and return an error
            if (isset($user)) {
                $user->delete();
            }

            // If any other exception occurs, return a generic error message
            return response()->json(['error' => 'Failed to create user.'], 500);
        }
} 

    /**
     * Store a newly created resource in storage.
     */
    public function authenticate(Request $request)
    {
        try {
            $request->validate([
                'phone_number' => 'required|string',
                'pin' => 'required|string',
            ]);

            $user = User::where('phone_number', $request->input('phone_number'))->first();
        
            $credentials = $request->only('phone_number', 'pin');
            // Log::info('Credentials:', $credentials);
            // Log::info('Login attempt:', $request->all());
            if ($user || Hash::check($request->pin, $user->pin)) {
            // if (Auth::attempt($credentials)) {
                // Authentication passed, user is logged in
                Log::info('User logged in successfully');
                return response()->json(['success' => 'User logged in successfully.']);
                // or you can redirect as follows:
                // return redirect()->intended('/users')->with('success', 'Welcome' . " " . Auth::user()->full_name);
            } else {
                // Authentication failed, user credentials are invalid
                Log::error('Failed to login user. Credentials:', $credentials);
                return response()->json(['error' => 'Failed to login user.'], 500);
                // or you can redirect as follows:
                // return redirect()->back()->withErrors(['Invalid credentials.']);
            }
        } catch (Exception $e) {
            Log::error('Exception during login:', ['exception' => $e]);
            return response()->json(['error' => $e->getMessage()], 500);
        }
        
    }

    public function getUserInfo()
    {
        $user = auth()->user(); // Assuming the user is authenticated
        return response()->json(['full_name' => $user->full_name, 'id' => $user->id]);
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
