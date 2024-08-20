<?php

namespace App\Http\Controllers;

use App\Models\Voucher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class VoucherController extends Controller
{
    public function checkVoucher(Request $request)
    {
        try {
            $voucher = Voucher::where('code', $request->input('voucher_code'))->first();

            if (!$voucher) {
                return response()->json(
                    [
                        'status' => false,
                        'message' => 'Voucher not found',
                    ],
                    400,
                );
            }

            return response()->json(
                [
                    'status' => true,
                    'message' => 'Voucher found',
                    'voucher' => $voucher,
                ],
                200,
            );
        } catch (\Exception $e) {
            return response()->json(
                [
                    'status' => false,
                    'message' => 'Something went wrong',
                    'error' => $e->getMessage(),
                ],
                500,
            );
        }
    }

    public function createVoucher(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string|unique:vouchers,code',
            'discount_amount' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        try {
            $voucher = Voucher::create([
                'code' => $request->input('code'),
                'discount_amount' => $request->input('discount_amount'),
                'start_date' => $request->input('start_date'),
                'end_date' => $request->input('end_date'),
                'active' => false,
            ]);

            return response()->json(
                [
                    'status' => true,
                    'message' => 'Voucher created successfully',
                    'voucher' => $voucher,
                ],
                201,
            );
        } catch (\Exception $e) {
            return response()->json(
                [
                    'status' => false,
                    'message' => 'Something went wrong',
                    'error' => $e->getMessage(),
                ],
                500,
            );
        }
    }

    public function updateVoucher(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'sometimes|string|unique:vouchers,code,' . $id,
            'discount_amount' => 'sometimes|numeric|min:0',
            'start_date' => 'sometimes|date',
            'end_date' => 'sometimes|date|after_or_equal:start_date',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        try {
            $voucher = Voucher::findOrFail($id);

            $voucher->update([
                'code' => $request->input('code'),
                'discount_amount' => $request->input('discount_amount'),
                'start_date' => $request->input('start_date'),
                'end_date' => $request->input('end_date'),
                'active' => false, // Set to inactive until checked by the scheduler
            ]);

            return response()->json(
                [
                    'status' => true,
                    'message' => 'Voucher updated successfully',
                    'voucher' => $voucher,
                ],
                200,
            );
        } catch (\Exception $e) {
            return response()->json(
                [
                    'status' => false,
                    'message' => 'Something went wrong',
                    'error' => $e->getMessage(),
                ],
                500,
            );
        }
    }

    public function deleteVoucher($id)
    {
        try {
            $voucher = Voucher::findOrFail($id);
            $voucher->delete();

            return response()->json(
                [
                    'status' => true,
                    'message' => 'Voucher deleted successfully',
                ],
                200,
            );
        } catch (\Exception $e) {
            return response()->json(
                [
                    'status' => false,
                    'error' => $e->getMessage(),
                ],
                500,
            );
        }
    }
}
