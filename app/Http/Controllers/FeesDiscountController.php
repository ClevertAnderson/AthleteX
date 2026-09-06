<?php

namespace App\Http\Controllers;

use App\Models\FeesDiscount;
use App\Models\Athlete;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FeesDiscountController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'athlete_id' => 'required|exists:athletes,id',
            'academic_year' => 'required|string|max:50',
            'total_units' => 'nullable|integer',
            'tuition_fee' => 'nullable|numeric',
            'miscellaneous_fee' => 'nullable|numeric',
            'other_charges' => 'nullable|numeric',
            'classification' => 'nullable|string',
            'remarks' => 'nullable|string|max:255',
        ]);

        $athlete = Athlete::findOrFail($request->athlete_id);

        // Use the modal's classification, or default to whatever is already saved
        $newClassification = $validated['classification'] ?? $athlete->classification;

        // Strict backend math
        $tuition = (float) ($validated['tuition_fee'] ?? 0);
        $misc = (float) ($validated['miscellaneous_fee'] ?? 0);
        $other = (float) ($validated['other_charges'] ?? 0);
        $assessment = $tuition + $misc + $other;

        // Auto discount rate
        $discountRate = 0;
        switch ($newClassification) {
            case 'Class_A': $discountRate = 1.00; break;
            case 'Class_B': $discountRate = 0.75; break;
            case 'Class_C': $discountRate = 0.50; break;
        }

        $discount = $tuition * $discountRate;
        $balance = $assessment - $discount;

        // Build the payload for the Fees table (excluding classification)
        $feePayload = $validated;
        $feePayload['total_assessment'] = $assessment;
        $feePayload['total_discount'] = $discount;
        unset($feePayload['classification']); 

        // 1. Save to the fees ledger
        FeesDiscount::create($feePayload);

        // 2. Direct DB update bypassing fillable protection
        DB::table('athletes')->where('id', $athlete->id)->update([
            'classification' => $newClassification,
            'total_unit' => $validated['total_units'] ?? DB::raw('total_unit'),
            'tuition_fee' => $tuition,
            'misc_fee' => $misc,
            'other_charges' => $other,
            'total_assessment' => $assessment,
            'total_discount' => $discount,
            'balance' => $balance
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Fee securely calculated and saved.'
        ]);
    }

    public function show($athlete_id)
    {
        $records = FeesDiscount::where('athlete_id', $athlete_id)
            ->orderBy('created_at', 'desc')
            ->get();
            
        return response()->json($records);
    }
}