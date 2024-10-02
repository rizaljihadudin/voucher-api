<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Voucher;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function getProducts()
    {
        return response()->json([
            'status' => 'success',
            'message' => 'Products retrieved successfully.',
            'data' => Product::all(),
        ], 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'price' => 'required|numeric',
        ]);

        try {
            $product = Product::create([
                'name' => $request->name,
                'description' => $request->description,
                'price' => $request->price,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Product '.$request->name.' created successfully.',
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create product. '.$request->name.' '.$th->getMessage(),
            ], 500);
        }
    }


    public function order(Request $request)
    {
        $validatedData = $request->validate([
           'product_id' => 'required|exists:products,id',
           'voucher'    => 'nullable|string'
        ]);
        $product = Product::find($validatedData['product_id']);
        $voucher = isset($validatedData['voucher']) ? Voucher::where('code', $validatedData['voucher'])->first() : null;


        if ($voucher) {
            if ($voucher->end_date < now()) {
                return response()->json([
                    'status'    => 'error',
                    'message'   => 'Voucher has expired'
                ], 400);
            }

            if (!$voucher->is_active || $voucher->start_date > now()) {
                return response()->json([
                    'status'    => 'error',
                    'message'   => 'Voucher is not active yet'
                ], 400);
            }
        }

        if(!$voucher) {
            return response()->json([
                'status'    => 'error',
                'message'   => 'Voucher not found'
            ], 404);
        }



        $disc = $voucher ? $voucher->discount / 100 : 0;
        $price_after_disc = $product->price * (1 - $disc);

        return response()->json([
            'status' => 'success',
            'message' => 'Product ' . strtoupper($product->name) . ' ordered successfully.',
            'data' => [
                'product_name' => strtoupper($product->name),
                'price' => 'Rp.' . number_format($product->price, 2, ',', '.'),
                'discount' => $voucher ? $voucher->discount . '%' : '0%',
                'price_after_disc' => 'Rp.' . number_format($price_after_disc, 2, ',', '.'),
            ],
        ], 200);
    }

}
