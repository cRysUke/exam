<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    //getall category
    public function index()
    {

        $category = Category::all();
        if ($category->count() > 0) {
            return response()->json([
                'status' => 200,
                'data' => $category,
            ], 200);
        } else {
            return response()->json([
                'status' => 204,
                'message' => 'No Record(s) found',
            ], 204);
        }
    }
}
