<?php

namespace App\Http\Controllers;

use Exception;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Account;
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
        // try{
        //     // Retrieve and return user by ID
        // $transactions = Transaction::all();

        // return response()->json(['Transaction' => $transactions]);
        // } catch (ModelNotFoundException $e) {
        //     // Log the exception details
        //     Log::error('Transactions not found: ' . $e->getMessage());
        //     // Return a specific error message for user not found
        //     return response()->json(['error' => 'Transaction not found.'], 404);
        // } catch (Exception $e) {
        //     // Log the exception details
        //     Log::error('Fetch Transactions failed: ' . $e->getMessage());
        //     // If any other exception occurs, return a generic error message
        //     return response()->json(['error' => 'Failed to access Database.'], 500);
        // }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        try {

            $validatedData = $request->validate([
                'account_number' => 'required',
                'transaction_type' => 'required|in:deposit,withdrawal',
                'amount' => 'required',
                // 'description' => 'string',
                // 'reference' => 'string',
                // 'method' => 'string',
                // 'fee' => 'string',
                // 'running_balance' => 'string',
                // 'status' => 'string',
            ]);

            // Get account
            $account = Account::findOrFail($validatedData['account_number']);

            // Check if there are any existing transactions for this account
            $existingTransactions = Transaction::where('account_number', $validatedData['account_number'])
                ->where('created_at', '>=', Carbon::now()->subDays(30))
                ->exists();

            // Retrieve current account balance
            $currentBalance = $account->account_balance;

            // If the transaction is a withdrawal, check if there's enough money in the account
            if ($validatedData['transaction_type'] === 'withdrawal' && $currentBalance < $validatedData['amount']) {
                $errorMessage = "Not enough money in your account to withdraw {$validatedData['amount']}.";
                return response()->json(['error' => $errorMessage], 400);
            }

            // Determine the new running balance based on the transaction type
            $newRunningBalance = ($validatedData['transaction_type'] === 'deposit')
                ? $currentBalance + $validatedData['amount']
                : $currentBalance - $validatedData['amount'];

            // save transaction
            $transaction = Transaction::create([
                'account_number' => $validatedData['account_number'],
                'transaction_type' => $validatedData['transaction_type'],
                'amount' => $validatedData['amount'],
                // 'description' => $validatedData['description'],
                // 'reference' => $validatedData['reference'],
                // 'method' => $validatedData['method'],
                // 'fee' => $validatedData['fee'],
                'running_balance' => $newRunningBalance,
                // 'status' => $validatedData['status'],
            ]);

            

            // Affect column for account balance whenever a transaction is made
            if ($validatedData['transaction_type'] === 'deposit') {
                $account->increment('account_balance', $validatedData['amount']);
            } elseif ($validatedData['transaction_type'] === 'withdrawal') {
                $account->decrement('account_balance', $validatedData['amount']);
            } // elseif ($validatedData['transaction_type'] === 'transfer') {
            //     $account->decrement('account_balance', $validatedData['amount']);
            // }

            // If there are no previous transactions, change the account status to active
            if (!$existingTransactions) {
                $account->update(['status' => 'Active']);
            }

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
        try {
            // Retrieve user by ID along with their transactions
            $user = User::with('transactions')->findOrFail($id);

            // Check if the user has transactions
            if ($user->transactions->isEmpty()) {
                return response()->json(['message' => 'No Transactions to display.']);
            }

            return response()->json(['user' => $user]);
        } catch (ModelNotFoundException $e) {
            // Log the exception details
            Log::error('Account not found: ' . $e->getMessage());
            // Return a specific error message for user not found
            return response()->json(['error' => 'Account not found.'], 404);
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
