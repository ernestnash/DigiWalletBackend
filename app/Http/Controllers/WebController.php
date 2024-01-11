<?php
namespace App\Http\Controllers;

use Exception;
use App\Models\User;
use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;

class WebController extends Controller
{
    public function index()
    {
        return view('login');
    }
    public function dashboard()
    {
        return view('admin.dashboard');
    }
    public function login(Request $request)
    {
        $validatedData = $request->validate([
            'phone_number' => 'required|numeric|min:10',
            'pin' => 'required|min:4',
        ]);

        $user = User::where('phone_number', $validatedData['phone_number'])->first();

        if (!$user) {
            return redirect()->back()->with('error', 'The user does not exist. Please register.');
        }

        $plainPin = $validatedData['pin'];

        if (Hash::check($plainPin, $user->pin)) {
            Auth::login($user);

            Log::info('User logged in successfully:', ['user_id' => $user->id]);

            return redirect('/dashboard')->with('success', 'Welcome ' . $user->full_name . ' ' . 'login successful');
        } else {
            Log::error('Failed to login user:', ['user_id' => $user->id]);

            return redirect()->back()->with('error', 'Incorrect Pin, Failed to login. Check your credentials.');
        }
    }


    public function register(Request $request)
    {
        try {

            $validatedData = $request->validate([
                'first_name' => 'required|string',
                'last_name' => 'required|string',
                'email' => 'required|string|email',
                'phone_number' => 'required|unique:users,phone_number|string',
                'pin' => 'required|string',
                'confirm_pin' => 'required|string',
            ]);

            if ($validatedData['pin'] != $validatedData['confirm_pin']) {
                Log::error('Pins did not match');
                return response()->json(['pins must match']);
            }

            $full_name = $validatedData['first_name'] . " " . $validatedData['last_name'];


            $user = User::create([
                'full_name' => $full_name,
                'phone_number' => $validatedData['phone_number'],
                'pin' => $validatedData['pin']
            ]);

            // Create a new account with default values
            $account = Account::create([
                'account_number' => $user->id,
                'account_type' => 'Generated',
                'account_balance' => 0.0,
                'status' => 'Pending First Transaction',
            ]);
            $account->save();

            return redirect()->intended('dashboard')->withSuccess('Signed in');
            // return response()->json(['message' => 'User created successfully', 'user' => $user, 'account' => $account]);

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
    public function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password'])
        ]);
    }
    // public function dashboard()
    // {
    //     if (Auth::check()) {
    //         return view('auth.dashboard');
    //     }
    //     return redirect("login")->withSuccess('You are not allowed to access');
    // }
    public function signOut()
    {
        Session::flush();
        Auth::logout();
        return view('login');
        // return Redirect('login');
    }
}