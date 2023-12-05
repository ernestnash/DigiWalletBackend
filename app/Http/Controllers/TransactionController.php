<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try{
            // Retrieve and return user by ID
        $transactions = Transaction::all();

        return response()->json(['Transaction' => $transactions]);
        } catch (ModelNotFoundException $e) {
            // Log the exception details
            Log::error('Transactions not found: ' . $e->getMessage());
            // Return a specific error message for user not found
            return response()->json(['error' => 'Transaction not found.'], 404);
        } catch (Exception $e) {
            // Log the exception details
            Log::error('Fetch Transactions failed: ' . $e->getMessage());
            // If any other exception occurs, return a generic error message
            return response()->json(['error' => 'Failed to access Database.'], 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        try {

            $validatedData = $request->validate([
                'account_number' => 'required',
                'transaction_type' => 'required',
                'amount' => 'required',
                'description' => 'required',
                'reference' => 'required',
                'method' => 'required',
                'fee' => 'required',
                'running_balance' => 'required',
                'status' => 'required',
            ]);


            $transaction = Transaction::create([
                'account_number' => $validatedData['account_number'],
                'transaction_type' => $validatedData['transaction_type'],
                'amount' => $validatedData['amount'],
                'description' => $validatedData['description'],
                'reference' => $validatedData['reference'],
                'method' => $validatedData['method'],
                'fee' => $validatedData['fee'],
                'running_balance' => $validatedData['running_balance'],
                'status' => $validatedData['status'],
            ]);

            return response()->json(['message' => 'Transaction made successfully', 'Transaction' => $transaction]);

        } catch (ValidationException $e) {
            // If validation fails, return validation errors
            return response()->json(['error' => $e->validator->errors()], 422);
        } catch (Exception $e) {
            // Log the exception details
            Log::error('Transaction failed: ' . $e->getMessage());
            

            // If any other exception occurs, return a generic error message
            return response()->json(['error' => 'Failed to make transaction.'], 500);
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
        try{
            // Retrieve and return user by ID
        $user = Transaction::findOrFail($id);

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
