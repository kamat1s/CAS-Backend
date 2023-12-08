<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Referral;

class ReferralController extends Controller
{
    public function newReferral(Request $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            Referral::create(
                [
                    ...$request->only(
                        [
                            'studentID',
                            'referrerID',
                            'reason',
                            'anecdotalReport'
                        ]
                    ),
                    'status' => 'Pending'
                ]
            );

            DB::commit();

            $response = [
                'message' => 'Referral created',
            ];

            return response()->json($response);
        } catch (\Exception $e) {
            DB::rollBack();
            // Handle the exception, log, or return an error response
            $response = [
                'message' => 'Referral Creation Failed',
                'error' => $e->getMessage(),
            ];
            return response()->json($response, 500);
        }
    }

    public function approveReferral(Request $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            $referral = Referral::findOrFail($request->id);

            $referral->status = "Approved";

            $referral->save();

            DB::commit();

            $response = [
                'message' => 'Referral approved',
            ];

            return response()->json($response);
        } catch (\Exception $e) {
            DB::rollBack();
            // Handle the exception, log, or return an error response
            $response = [
                'message' => 'Referral Approval Failed',
                'error' => $e->getMessage(),
            ];
            return response()->json($response, 500);
        }
    }
}
