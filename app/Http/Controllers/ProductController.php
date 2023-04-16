<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    //get all products
    public function index()
    {

        $products = Product::with('category')->orderByDesc('updated_at')->get();
        if ($products->count() > 0) {
            return response()->json([
                'status' => 200,
                'success' => true,
                'data' => $products,
            ], 200);
        } else {
            return response()->json([
                'status' => 204,
                'success' => false,
                'message' => 'No Record(s) found',
            ], 204);
        }
    }

    //insert a product
    public function store(Request $request)
    {
        // return $request->all();

        $validator = Validator::make($request->all(), [
            'name' => 'required|max:30',
            'category_id' => 'required|exists:categories,id',
            'description' => 'required|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'success' => false,
                'message' =>  $validator->errors(),
            ], 422);
        }

        $insert = Product::create($request->all());
        if ($insert) {
            return response()->json([
                'status' => 201,
                'success' => true,
                'message' => 'Product added successfully.',
            ], 201);
        } else {
            return response()->json([
                'status' => 500,
                'success' => false,
                'message' => 'Something went wrong',
            ], 500);
        }
    }

    //show a product
    public function show(int $id)
    {
        $product = Product::with('category')->find($id);
        if ($product->count() > 0) {
            return response()->json([
                'status' => 200,
                'success' => true,
                'data' => $product,
            ], 200);
        } else {
            return response()->json([
                'status' => 204,
                'success' => true,
                'message' => 'No Record(s) found',
            ], 204);
        }
    }

    //update a product
    public function update(Request $request, int $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:30',
            'category_id' => 'required|exists:categories,id',
            'description' => 'required|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'success' => false,
                'message' =>  $validator->errors(),
            ], 422);
        }

        $product = Product::find($id);
        if ($product) {
            $update = $product->update($request->all());
            if ($update) {
                return response()->json([
                    'status' => 201,
                    'success' => true,
                    'message' => 'Product updated successfully.',
                ], 201);
            } else {
                return response()->json([
                    'status' => 500,
                    'success' => false,
                    'message' => 'Something went wrong',
                ], 500);
            }
        } else {
            return response()->json([
                'status' => 204,
                'success' => false,
                'message' => 'No Record(s) found',
            ], 204);
        }
    }

    //delete a product
    public function destroy(int $id)
    {
        $product = Product::find($id);
        if ($product) {
            $update = $product->delete();
            if ($update) {
                return response()->json([
                    'status' => 201,
                    'success' => true,
                    'message' => 'Product deleted successfully.',
                ], 201);
            } else {
                return response()->json([
                    'status' => 500,
                    'success' => false,
                    'message' => 'Something went wrong',
                ], 500);
            }
        } else {
            return response()->json([
                'status' => 204,
                'success' => false,
                'message' => 'No Record(s) found',
            ], 204);
        }
    }

    //search by keywords and filter by category
    public function search(Request $request)
    {
        $keywords = $request->get('s');
        $category = $request->get('c');
        if ($keywords != null and $category != null) {
            $products = Product::where('category_id', $category)->where(function ($query) use ($keywords) {
                $query->where('name', 'LIKE', '%' . $keywords . '%')
                    ->orWhere('description', 'LIKE', '%' . $keywords . '%');
            })->with('category')->get();

            if ($products->count() > 0) {
                return response()->json([
                    'status' => 200,
                    'success' => true,
                    'data' => $products,
                ], 200);
            } else {
                return response()->json([
                    'status' => 204,
                    'success' => false,
                    'message' => 'No Record(s) found',
                ], 204);
            }
        } elseif ($keywords != null and $category == null) {
            $products = Product::where('name', 'LIKE', '%' . $keywords . '%')
                ->orWhere('description', 'LIKE', '%' . $keywords . '%')->with('category')->get();

            if ($products->count() > 0) {
                return response()->json([
                    'status' => 200,
                    'success' => true,
                    'data' => $products,
                ], 200);
            } else {
                return response()->json([
                    'status' => 204,
                    'success' => false,
                    'message' => 'No Record(s) found',
                ], 204);
            }
        } elseif ($keywords == null and $category != null) {
            $products = Product::where('category_id', $category)->with('category')->get();

            if ($products->count() > 0) {
                return response()->json([
                    'status' => 200,
                    'success' => true,
                    'data' => $products,
                ], 200);
            } else {
                return response()->json([
                    'status' => 204,
                    'success' => false,
                    'message' => 'No Record(s) found',
                ], 204);
            }
        } else {
            return $this->index();
        }
    }
}
