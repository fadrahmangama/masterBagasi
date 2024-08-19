<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Products;

class CartController extends Controller
{
    public function addToCart(Request $request)
    {
        try {
            $cart = Cart::firstOrCreate(['user_id' => auth()->id()]);
            $product = Products::findOrFail($request->product_id);

            $cartItem = CartItem::updateOrCreate(['cart_id' => $cart->id, 'product_id' => $product->id], ['quantity' => $request->quantity]);

            return response()->json(
                [
                    'status' => true,
                    'message' => 'Item added to cart successfully.',
                    'data' => $cartItem,
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

    public function removeFromCart($id)
    {
        try {
            $cartItem = CartItem::findOrFail($id);
            $cartItem->delete();

            return response()->json(
                [
                    'status' => true,
                    'message' => 'Item removed from cart successfully.',
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
                404,
            );
        }
    }

    public function viewCart()
    {
        try {
            $cart = Cart::where('user_id', auth()->id())->with('items.product')->first();

            return response()->json(
                [
                    'status' => true,
                    'message' => 'Cart retrieved successfully.',
                    'data' => $cart,
                ],
                200,
            );
        } catch (\Exception $e) {
            return response()->json(
                [
                    'status' => false,
                    'message' => 'Something went wrong', 
                    'error'=> $e->getMessage(),
                ],
                500,
            );
        }
    }

    public function clearCart(Request $request)
    {
        try {
            $userId = $request->user()->id;
            Cart::where('user_id', $userId)->delete();

            return response()->json(
                [
                    'status' => true,
                    'message' => 'Cart cleared successfully.',
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
}
