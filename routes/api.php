<?php

use App\Models\MobileCall;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::post('/v1/mobile-calls', function (Request $request) {

    try {

        $validator = Validator::make($request->all(), [
            'db_no' => 'required|string|max:255|unique:mobile_calls,db_no',
            'campaign_id' => 'required|string|max:255',

            'call_date' => 'required|date',

            'start_epoch' => 'required|integer|min:0',
            'end_epoch' => 'nullable|integer|min:0',

            'user' => 'required|string|max:255',
            'status_name' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();

        // Use current timestamp if end_epoch is not sent
        $endEpoch = $validated['end_epoch'] ?? now()->timestamp;

        // Calculate call duration
        $lengthInSec = $endEpoch - $validated['start_epoch'];

        if ($lengthInSec < 0) {
            return response()->json([
                'success' => false,
                'message' => 'end_epoch cannot be smaller than start_epoch',
            ], 422);
        }

        $mobileCall = MobileCall::create([
            'db_no' => $validated['db_no'],
            'campaign_id' => $validated['campaign_id'],

            'call_date' => $validated['call_date'],

            'start_epoch' => $validated['start_epoch'],
            'end_epoch' => $endEpoch,

            'length_in_sec' => $lengthInSec,

            'user' => $validated['user'],
            'status_name' => $validated['status_name'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Mobile call stored successfully',
            'data' => $mobileCall,
        ], 201);

    } catch (\Throwable $e) {

        // Log actual error internally
        Log::error('Mobile call API error', [
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ]);

        // Generic response to client
        return response()->json([
            'success' => false,
            'message' => 'Something went wrong',
        ], 500);
    }
});