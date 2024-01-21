<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Services\TwilioService;
use Illuminate\Support\Facades\Log;
use Validator;
use Exception;
use Illuminate\Support\Facades\Mail;
use App\Mail\sendEmail;
use App\Mail\OtpMail;
use Illuminate\Contracts\Mail\Mailable;

class OTPController extends Controller
{

    // africas talking api key: 8058b0f78ea6fdad93f0a782d5d09f86f9533c90291cef3dc209e2c076048ad6
    // public function index()
    // {

    //     $subject = 'Test Subject';
    //     $body = 'Test Body';

    //     Mail::to('ernestnashville@gmail.com')->send(new OtpMail($subject, $body));

    // }

    /**
     * Display a listing of the resource.
     */
    public function requestOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'phone_number' => 'required|numeric',
        ]);

        // Generate a random 4-digit OTP
        $otp = rand(1000, 9999);
        Log::info("Generated OTP: " . $otp);


        // Calculate expiration time (e.g., 5 minutes from now)
        $expirationTime = now()->addMinutes(5);


        // Update the user with the generated OTP
        $user = User::where('phone_number', $request->phone_number)->update([
            'email' => $request->email,
            'otp' => $otp,
            'otp_expires_at' => $expirationTime,
        ]);

        if ($user) {
            // Send the OTP via email
            try {

                $subject = 'DigiWallet OTP for account ' . $request->phone_number;
                $body = 'Your DigiWallet OTP is ' . $otp;


                Mail::to($request->email)->send(new OtpMail($subject, $body));

                return response(['status' => 200, 'message' => 'OTP sent successfully', 'OTP' => $otp]);
            } catch (Exception $e) {
                // Handle email sending failure
                Log::error("Failed to send OTP email: " . $e->getMessage());
                return response(["status" => 500, 'message' => 'Failed to send OTP']);
            }
        } else {
            return response(["status" => 401, 'message' => 'Invalid']);
        }
    }

    public function verifyOtp(Request $request)
    {
        try {
            // Validate request data
            $request->validate([
                'email' => 'required|email',
                'otp' => 'required|numeric',
            ]);
    
            // Retrieve the user based on email and OTP
            $user = User::where([
                ['email', '=', $request->email],
                ['otp', '=', $request->otp],
            ])->first();
    
            // Check if the user exists and OTP has not expired
            if (!$user || now()->gt($user->otp_expires_at)) {
                return response(["status" => 401, 'message' => 'Invalid or Expired OTP']);
            }
    
            // Clear the OTP after successful verification
            User::where('email', $request->email)->update(['otp' => null, 'otp_expires_at' => null]);
    
            return response([
                "status" => 200,
                "message" => "Success",
            ]);
        } catch (Exception $e) {
            // Handle any exceptions that might occur (e.g., database query exceptions)
            return response(["status" => 500, 'message' => 'Internal Server Error']);
        }
    }
    



    /**
     * Show the form for creating a new resource.
     */
    public function findUser($phone_number)
    {
        // Debugging statement
        Log::info("Received phone number: " . $phone_number);

        $user = User::where('phone_number', $phone_number)->first();

        if ($user) {
            return response(['status' => 200, 'user_data' => $user], 200);
        } else {
            return response(['status' => 404, 'message' => 'User not found'], 404);
        }
    }



    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
