<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Voucher;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    public function checkout(Request $request)
    {
        try {
            $cart = Cart::where('user_id', auth()->id())->with('items.product')->first();

            if (!$cart || $cart->items->isEmpty()) {
                return response()->json(['message' => 'Cart is empty'], 400);
            }

            $totalAmount = $cart->items->sum(function ($item) {
                return $item->product->price * $item->quantity;
            });

            if ($request->has('voucher_code')) {
                $voucher = Voucher::where('code', $request->input('voucher_code'))
                    ->where('start_date', '<=', now())
                    ->where('end_date', '>=', now())
                    ->first();

                if ($voucher) {
                    $totalAmount -= $voucher->discount_amount;
                }
            }

            $cart->items()->delete();
            $cart->delete();

            return response()->json([
                'status' => true,
                'message' => 'Checkout successful',
                'total' => $totalAmount], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
                'error' => $e->getMessage()], 500);
        }
    }
}
