<?php

namespace App\Http\Controllers;

use App\Models\Products;
use Illuminate\Http\Request;
use \Illuminate\Validation\ValidationException;

class ProductsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function getAllProduct()
    {
        try {
            $products = Products::all();
            if ($products!=null){
                return response()->json(
                    [
                        'status' => true,
                        'message' => 'Products retrieved successfully',
                        'data' => $products,
                    ],
                    200,
                );
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Products is null',
                ], 404);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
                'error' => $e->getMessage()], 
                500);
        }
    }

    public function storeProduct(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
        ]);

        try {
            $product = Products::create($request->only('name','description','price','stock'));
            return response()->json(
                [
                    'status' => true,
                    'message' => 'Product created successfully',
                    'data'=> $product
                ],
                201);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        }
         catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
                'error' => $e->getMessage()], 
                500);
        }
    }

    public function getProduct($id)
    {
        try {
            $product = Products::findOrFail($id);
            return response()->json(
                [
                    'status' => true,
                    'message' => 'Product retrieved successfully',
                    'data' => $product
                ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
                'error' => $e->getMessage()], 404);
        }
    }

    public function updateProduct(Request $request, $id)
    {
        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'sometimes|required|numeric|min:0',
            'stock' => 'sometimes|required|integer|min:0',
        ]);

        try {

            if ($request->all()===[]){
                return response()->json([
                    'status' => false,
                    'message' => 'No updates inputted',
                ], 404);
            }

            $product = Products::findOrFail($id);
            $product->update($request->all());
            return response()->json([
                'status' => true,
                'message' => 'Product updated successfully',
                'data' => $product
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
                'error' => $e->getMessage()
            ], 501);
        }
    }

    public function destroyProduct($id)
    {
        try {
            $product = Products::findOrFail($id);
            $product->delete();
            return response()->json([
                'status' => true,
                'message' => 'Product deleted'], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
                'error' => $e->getMessage()], 501);
        }
    }
}
