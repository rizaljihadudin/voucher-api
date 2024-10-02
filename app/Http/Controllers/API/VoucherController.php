<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Voucher;
use Carbon\Carbon;
use Illuminate\Http\Request;

class VoucherController extends Controller
{
    public function getVouchers()
    {
        return response()->json([
            'status'    => 'success',
            'message'   => 'Voucher retrieved successfully.',
            'data'      => Voucher::all(),
        ], 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'code'          => 'required|string|unique:vouchers',
            'discount'      => 'required|numeric',
            'start_date'    => 'required|date_format:d-m-Y',
            'end_date'      => 'required|date_format:d-m-Y',
        ]);

        try {

            $startDate  = Carbon::createFromFormat('d-m-Y', $request->start_date)->format('Y-m-d');
            $endDate    = Carbon::createFromFormat('d-m-Y', $request->end_date)->format('Y-m-d');

            $voucher = Voucher::create([
                'code'          => $request->code,
                'discount'      => $request->discount,
                'start_date'    => $startDate,
                'end_date'      => $endDate,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Voucher '.$request->code.' created successfully.',
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create Voucher. '.$request->code.' '.$th->getMessage(),
            ], 500);
        }
    }
}
