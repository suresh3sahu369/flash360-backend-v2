<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;

class PublicCategoryController extends Controller
{
    public function index()
    {
        return response()->json(
            Category::select('id', 'name', 'slug')
                ->orderBy('name')
                ->get()
        );
    }
}
