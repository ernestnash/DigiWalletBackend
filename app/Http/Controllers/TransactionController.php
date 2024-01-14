<?php

namespace App\Http\Controllers;

use Exception;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
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
                'transaction_type' => 'required|in:Deposit,Withdrawal',
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
            if ($validatedData['transaction_type'] === 'Withdrawal' && $currentBalance < $validatedData['amount']) {
                $errorMessage = "Not enough money in your account to withdraw {$validatedData['amount']}.";
                return response()->json(['error' => $errorMessage], 400);
            }

            // Determine the new running balance based on the transaction type
            $newRunningBalance = ($validatedData['transaction_type'] === 'Deposit')
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
            if ($validatedData['transaction_type'] === 'Deposit') {
                $account->increment('account_balance', $validatedData['amount']);
            } elseif ($validatedData['transaction_type'] === 'Withdrawal') {
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
     * Fetch all transactions by the currently logged-in user.
     */
    public function getUserTransactions($id)
    {
        try {
            // Find the user by ID
            $user = User::find($id);

            // Check if the user exists
            if (!$user) {
                return response()->json(['error' => 'User not found.'], 404);
            }

            // Retrieve the user's transactions and order them by the latest transaction first
            $transactions = $user->transactions()->orderBy('created_at', 'desc')->get();

            // Check if the user has transactions
            if ($transactions->isEmpty()) {
                return response()->json(['message' => 'No transactions to display for the user.']);
            }

            return response()->json(['transactions' => $transactions]);
        } catch (Exception $e) {
            // Log the exception details
            Log::error('Fetch User Transactions failed: ' . $e->getMessage());
            // If any other exception occurs, return a generic error message
            return response()->json(['error' => 'Failed to fetch user transactions.'], 500);
        }
    }




    /**
     * Transfer of funds.
     */
    public function transfer(Request $request, $originAccountId, $destinationAccountId)
    {
        // Validate the request data
        $request->validate([
            'amount' => 'required|numeric|min:100',
        ]);

        // Retrieve the accounts
        $originAccount = Account::findOrFail($originAccountId);
        $destinationAccount = Account::findOrFail($destinationAccountId);

        // Retrieve the associated users
        $originUser = $originAccount->user;
        $destinationUser = $destinationAccount->user;

        // Perform the funds transfer
        $amount = $request->input('amount');
        $transferDescription = "Transfer from $originUser->full_name acc no $originAccountId to $destinationUser->full_name acc no $destinationAccountId";

        // Start a database transaction to ensure data consistency
        DB::beginTransaction();

        try {
            // Ensure the origin account has sufficient balance
            if ($originAccount->account_balance < $amount) {
                throw new \Exception('Insufficient balance');
            }

            // Create a transaction for the origin account (Sent)
            $sentTransaction = Transaction::create([
                'account_number' => $originAccount->account_number,
                'transaction_type' => 'Sent',
                'amount' => $amount,
                'running_balance' => $originAccount->account_balance - $amount,
                'description' => $transferDescription,
            ]);

            // Create a transaction for the destination account (Received)
            $receivedTransaction = Transaction::create([
                'account_number' => $destinationAccount->account_number,
                'transaction_type' => 'Received',
                'amount' => $amount,
                'running_balance' => $destinationAccount->account_balance + $amount,
                'description' => $transferDescription,
            ]);

            // Update the balances
            $originAccount->decrement('account_balance', $amount);
            $destinationAccount->increment('account_balance', $amount);

            // Commit the transaction
            DB::commit();

            // You can return a response or perform additional actions as needed
            return response()->json(['message' => 'Funds transferred successfully']);
        } catch (\Exception $e) {
            // Something went wrong, rollback the transaction
            DB::rollBack();

            return response()->json(['error' => $e->getMessage()], 500);
        }
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
