<?php

namespace App\Http\Controllers;

use Exception;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Cheque;
use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;


class ChequeController extends Controller
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
    public function create(Request $request)
    {

        DB::beginTransaction();


        try {

            $validatedData = $request->validate([
                // 'cheque_number' => 'required|unique:cheques,cheque_number',
                'account_number' => 'required',
                'payee_name' => 'required|string',
                'amount' => 'required|numeric',
                'cheque_status' => 'nullable|in:issued,cashed,void',
                'date_issued' => 'nullable',
                'date_cashed' => 'nullable',
                'authorization_status' => 'nullable|in:authorized,unauthorized',
                'stop_payment_flag' => 'nullable',
                'issuing_branch' => 'required',
                'memo' => 'nullable|string',
            ]);


            // Get account
            $account = Account::findOrFail($validatedData['account_number']);

            // Get User
            $user = User::findOrFail($account->account_number);

            // Ensure that $user->id is numeric
            $userId = is_numeric($user->id) ? $user->id : 0;

            // Set current date and time
            $currentDateTime = Carbon::now();

            // Calculate the next cheque number within a transaction
            // Count the number of cheques for the specified account number
            $chequeCount = Cheque::where('account_number', $validatedData['account_number'])->count();

            // Calculate the next cheque number
            $nextChequeNumber = $chequeCount + 1;

            // Get current year
            $currentYear = date('Y');

            $chequeNumber = sprintf("%03d", $userId) . '/' . $validatedData['account_number'] . '/' . sprintf("%03d", $nextChequeNumber) . ':' . $currentYear;


            // Check if the generated cheque number already exists
            if (Cheque::where('cheque_number', $chequeNumber)->exists()) {
                // Rollback the transaction if the cheque number is not unique
                DB::rollBack();
                return response()->json(['error' => 'Duplicate cheque number.'], 422);
            }

            Log::info('Cheque details: ' . json_encode($validatedData, JSON_PRETTY_PRINT). 'Cheque Number ' . $chequeNumber);
            
            // save cheque
            $cheque = Cheque::create([
                'cheque_number' => $chequeNumber,
                'account_number' => $validatedData['account_number'],
                'payee_name' => $validatedData['payee_name'],
                'amount' => $validatedData['amount'],
                'cheque_status' => 'issued',
                'date_issued' => $currentDateTime,
                'date_cashed' => null,
                'authorization_status' => 'authorized',
                'stop_payment_flag' => 1,
                'issuing_branch' => $validatedData['issuing_branch'],
                'memo' => $validatedData['memo'],
            ]);

            // DB::commit();

            // Update the cheque status to 'cashed' if needed
            if ($cheque['cheque_status'] === 'cashed') {
                $cheque->update(['date_cashed' => $currentDateTime]);
            }

            // if ($request->validated()) {
                DB::commit();
            //     return response()->json(['message' => 'Cheque issued successfully', 'Cheque' => $cheque]);
            // } else {
            //     DB::rollBack();
            //     return response()->json(['error' => 'Validation failed.'], 422);
            // }

            return response()->json(['message' => 'Cheque issued successfully', 'Cheque' => $cheque]);

        } catch (ValidationException $e) {
            DB::rollBack();
            // If validation fails, return validation errors
            return response()->json(['error' => $e->validator->errors()], 422);
        } catch (Exception $e) {
            // Log the exception details
            Log::error('Cheque Issuing failed: ' . $e->getMessage());
            
            // Rollback the transaction in case of an exception
            DB::rollBack();

            // If any other exception occurs, return a generic error message
            return response()->json(['error' => 'Failed to issue Cheque.'], 500);
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
        try {
            // Retrieve the cheque data
            $chequeData = Cheque::findOrFail($id);

            // Set current date and time
            $currentDateTime = Carbon::now();

            // Validate the request data
            $validatedData = $request->validate([
                // 'cheque_number' => 'required|unique:cheques,cheque_number',
                'account_number' => 'required',
                'payee_name' => 'required|string',
                'amount' => 'required|numeric',
                'cheque_status' => 'nullable|in:issued,cashed,void',
                'date_issued' => 'nullable|date',
                'date_cashed' => 'nullable|date',
                'authorization_status' => 'nullable|in:authorized,unauthorized',
                'stop_payment_flag' => 'nullable',
                'issuing_branch' => 'required',
                'memo' => 'nullable|string',
            ]);

            if (
                // Update the cheque status to 'void' if needed
                array_key_exists('authorization_status', $validatedData) &&
                $validatedData['authorization_status'] === 'unauthorized' &&
                $chequeData->cheque_status !== 'void'
            ) {
                $chequeData->update(['cheque_status' => 'void']);
            }
            // Update the date issued to the current date if needed
            if (
                array_key_exists('cheque_status', $validatedData) &&
                $validatedData['cheque_status'] === 'cashed' &&
                !isset($validatedData['date_issued'])
            ) {
                $chequeData->update(['date_cashed' => $currentDateTime]);
            }

            // Update the cheque data
            $chequeData->update($validatedData);

            return response()->json(['message' => 'Cheque updated successfully', 'Cheque' => $chequeData]);
        } catch (ValidationException $validationException) {
            // Handle validation errors
            return response()->json(['error' => $validationException->errors()], 400);
        } //catch (ModelNotFoundException $notFoundException) {
            // Handle model not found exception
            //return response()->json(['error' => 'Cheque not found'], 404);
        catch (Exception $exception) {
            // Handle other exceptions
            return response()->json(['error' => $exception->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
